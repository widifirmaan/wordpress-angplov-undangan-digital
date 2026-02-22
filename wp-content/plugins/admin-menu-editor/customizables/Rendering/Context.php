<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Rendering;

use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractSetting;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractStructSetting;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\DynamicBinding;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\Binding;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\WithSchema\SettingWithSchema;
use YahnisElsts\AdminMenuEditor\Customizable\Storage\AbstractSettingsDictionary;
use YahnisElsts\AdminMenuEditor\Options\Option;
use YahnisElsts\AdminMenuEditor\Customizable\Schemas;

class Context {
	const CURRENT_ITEM_ATTRIBUTE = '$item';
	const CURRENT_KEY_ATTRIBUTE = '$key';

	protected static $contextCounter = 0;

	protected $parent;
	protected $attributes;
	/**
	 * @var null|ResolvedBinding
	 */
	protected $dataSource = null;
	/**
	 * @var string Internal unique ID for this context instance. Only for debugging.
	 */
	protected $contextId;
	/**
	 * @var int Incremented each time the context is modified, to invalidate caches.
	 */
	protected $version = 1;

	protected $resolutionCache = [];

	public function __construct(
		?Context         $parent = null,
		array            $attributes = [],
		?ResolvedBinding $dataSource = null,
		                 $idPrefix = ''
	) {
		++self::$contextCounter;
		$this->contextId = ($idPrefix ?: 'context') . ' {' . self::$contextCounter . '}';

		$this->parent = $parent;
		$this->attributes = $attributes;
		$this->dataSource = $dataSource;
	}

	public function getAttribute($key, $default = null) {
		if ( array_key_exists($key, $this->attributes) ) {
			return $this->attributes[$key];
		} elseif ( $this->parent !== null ) {
			return $this->parent->getAttribute($key, $default);
		} else {
			return $default;
		}
	}

	public function setAttribute($key, $value) {
		$this->attributes[$key] = $value;
		$this->version++;
	}

	public function withAttributes(array $attributes): Context {
		return new Context($this, $attributes);
	}

	public function withDataSource(ResolvedBinding $dataSource, $idPrefix = ''): Context {
		return new Context($this, [], $dataSource, $idPrefix);
	}

	public function resolveValuePath(Binding $thing): array {
		$resolution = $this->resolveBinding($thing);
		if ( $resolution->isDefined() ) {
			return $resolution->get()->getFullPath();
		} else {
			return [];
		}
	}

	/**
	 * @param Binding|null $thing
	 * @param mixed $customDefaultValue
	 * @return Option<ResolvedBinding>
	 */
	public function resolveBinding(?Binding $thing = null, $customDefaultValue = null): Option {
		if ( $thing === null ) {
			return Option::none();
		}

		$cacheKey = $thing->getBindingString();
		if ( $customDefaultValue !== null ) {
			$cacheKey .= ':' . $customDefaultValue;
		}

		if ( array_key_exists($cacheKey, $this->resolutionCache) ) {
			list($cachedVersion, $cachedResolution) = $this->resolutionCache[$cacheKey];
			if ( $cachedVersion === $this->version ) {
				return $cachedResolution;
			}
		}

		$resolution = $this->internalResolveBinding($thing, $customDefaultValue);
		$this->resolutionCache[$cacheKey] = [$this->version, $resolution];
		return $resolution;
	}

	protected function internalResolveBinding(Binding $thing, $customDefault = null): Option {
		if ( $thing instanceof AbstractSetting ) {
			return Option::some(new ResolvedBinding($thing, [], true, $thing->getValue($customDefault)));
		}

		if ( !($thing instanceof DynamicBinding) ) {
			return Option::none();
		}

		$dataSource = $this->getCurrentDataSource();
		if ( \ameUtils::stringStartsWith($thing->getBindingString(), '$') && !$dataSource ) {
			throw new \RuntimeException('Cannot resolve a dynamic binding without a data source in context.');
		}

		$path = explode('.', $thing->getBindingString());
		$pathExists = true;
		$current = $thing->getSettingDictionary();

		if ( $dataSource ) {
			$leafSetting = $dataSource->getSetting();
			$pathInSetting = $dataSource->getPathInSetting();
			$currentSchema = $dataSource->getSchema();
		} else {
			$leafSetting = null;
			$pathInSetting = [];
			$currentSchema = null;
		}

		while (!empty($path)) {
			$segment = array_shift($path);

			if ( \ameUtils::stringStartsWith($segment, '$') ) {
				$current = $this->getAttribute($segment);
				if ( $segment === self::CURRENT_ITEM_ATTRIBUTE ) {
					$pathInSetting[] = $this->getAttribute(self::CURRENT_KEY_ATTRIBUTE);
				} else {
					$pathInSetting[] = $segment;
				}
				continue;
			}

			if ( $current instanceof AbstractSettingsDictionary ) {
				$current = $current->findSetting($segment);
				if ( $current instanceof AbstractSetting ) {
					$leafSetting = $current;
				} else {
					$pathExists = false;
				}
			} else if ( $current instanceof AbstractStructSetting ) {
				$current = $current->getChild($segment);
				if ( $current instanceof AbstractSetting ) {
					$leafSetting = $current;
				} else {
					$pathExists = false;
				}
			} else if ( $current instanceof AbstractSetting ) {
				$leafSetting = $current;
				$current = $current->getValue();
				//Go back to the top and look for the same segment again in the setting value.
				array_unshift($path, $segment);
			} else if ( is_array($current) && array_key_exists($segment, $current) ) {
				$current = $current[$segment];
				$pathInSetting[] = $segment;

				if ( $currentSchema instanceof Schemas\Collection ) {
					$currentSchema = $currentSchema->getItemSchema();
				} else if ( $currentSchema instanceof Schemas\Struct ) {
					$currentSchema = $currentSchema->getFieldShema($segment);
				} else {
					$currentSchema = null;
				}
			} else if ( is_object($current) && property_exists($current, $segment) ) {
				$current = $current->$segment;
				$pathInSetting[] = $segment;

				if ( $currentSchema instanceof Schemas\Collection ) {
					$currentSchema = $currentSchema->getItemSchema();
				} else if ( $currentSchema instanceof Schemas\Struct ) {
					$currentSchema = $currentSchema->getFieldShema($segment);
				} else {
					$currentSchema = null;
				}
			} else {
				//Could not resolve the segment.
				$pathExists = false;

				//However, for collection/struct schemas, we could do a partial resolution to get
				//the item schema and default value.
				if ( $currentSchema instanceof Schemas\Collection ) {
					$currentSchema = $currentSchema->getItemSchema();
				} else if ( $currentSchema instanceof Schemas\Struct ) {
					$currentSchema = $currentSchema->getFieldShema($segment);
				} else {
					$currentSchema = null;
				}

				if ( $currentSchema ) {
					$current = $currentSchema->getDefaultValue();
					$pathInSetting[] = $segment;
				} else {
					$current = null;
					break;
				}
			}

			if ( $current instanceof SettingWithSchema ) {
				$currentSchema = $current->getSchema();
			}
		}

		if ( $leafSetting instanceof AbstractSetting ) {
			if ( $current instanceof AbstractSetting ) {
				$cachedValue = $current->getValue($customDefault);
			} else {
				$cachedValue = $current;
			}
			return Option::some(new ResolvedBinding(
				$leafSetting,
				$pathInSetting,
				$pathExists,
				$cachedValue,
				$currentSchema
			));
		}
		return Option::none();
	}

	public function resolveValue(Binding $binding, $customDefault = null) {
		if ( $binding instanceof AbstractSetting ) {
			return $binding->getValue($customDefault);
		}

		$resolution = $this->resolveBinding($binding);
		if ( $resolution->isDefined() ) {
			return $resolution->get()->getCachedValue();
		} else {
			return $customDefault;
		}
	}

	public function getId(): string {
		return $this->contextId;
	}

	public function getVersion(): int {
		return $this->version;
	}

	public function getCurrentDataSource(): ?ResolvedBinding {
		if ( $this->dataSource ) {
			return $this->dataSource;
		} elseif ( $this->parent !== null ) {
			return $this->parent->getCurrentDataSource();
		} else {
			return null;
		}
	}
}