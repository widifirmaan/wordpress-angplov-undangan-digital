<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Rendering;

use YahnisElsts\AdminMenuEditor\Customizable\Schemas\Schema;
use YahnisElsts\AdminMenuEditor\Customizable\Schemas;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractSetting;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\WithSchema\SettingWithSchema;


class ResolvedBinding {
	/**
	 * @var AbstractSetting
	 */
	private $setting;
	/**
	 * @var array
	 */
	private $pathInSetting;
	/**
	 * @var mixed
	 */
	private $cachedValue;

	private $cachedValueSchema;
	private $triedToGetSchema = false;
	private $pathExists;

	public function __construct(
		AbstractSetting $setting,
		array           $pathInSetting,
						$pathExists,
		                $cachedValue,
		?Schema         $valueSchema = null
	) {
		$this->setting = $setting;
		$this->pathInSetting = $pathInSetting;
		$this->cachedValue = $cachedValue;
		$this->pathExists = $pathExists;

		$this->cachedValueSchema = $valueSchema;
		if ( $valueSchema !== null ) {
			$this->triedToGetSchema = true;
		}
	}

	public function getSetting(): AbstractSetting {
		return $this->setting;
	}

	public function getPathInSetting(): array {
		return $this->pathInSetting;
	}

	public function getCachedValue() {
		return $this->cachedValue;
	}

	public function getFullPath(): array {
		$settingIdPath = explode('.', $this->setting->getId());
		return array_merge($settingIdPath, $this->pathInSetting);
	}

	public function getSchema(): ?Schema {
		if ( $this->triedToGetSchema ) {
			return $this->cachedValueSchema;
		}

		$this->triedToGetSchema = true;
		$this->cachedValueSchema = null;

		if ( $this->setting === null ) {
			return null;
		}

		if ( $this->setting instanceof SettingWithSchema ) {
			$schema = $this->setting->getSchema();
		} else {
			return null;
		}

		$schemaFound = true;
		foreach ($this->pathInSetting as $segment) {
			switch ($segment) {
				case Context::CURRENT_ITEM_ATTRIBUTE:
					if ( $schema instanceof Schemas\Collection ) {
						$schema = $schema->getItemSchema();
					} else {
						$schemaFound = false;
					}
					break;
				case Context::CURRENT_KEY_ATTRIBUTE:
					if ( $schema instanceof Schemas\Collection ) {
						$schema = $schema->getKeySchema();
					} else {
						$schemaFound = false;
					}
					break;
				default:
					if ( $schema instanceof Schemas\Collection ) {
						//Assume segment is a key in the collection. The specific key doesn't matter
						//for schema resolution, and we may not have access to the actual keys here.
						$schema = $schema->getItemSchema();
					} else if ( $schema instanceof Schemas\Struct ) {
						$schema = $schema->getFieldShema($segment);
						if ( $schema === null ) {
							$schemaFound = false;
						}
					} else {
						$schemaFound = false;
					}
			}

			if ( !$schemaFound || ($schema === null) ) {
				$schemaFound = false;
				break;
			}
		}

		if ( $schemaFound ) {
			$this->cachedValueSchema = $schema;
		}
		return $this->cachedValueSchema;
	}

	public function serializeForJs(): array {
		$result = [];
		if ( $this->setting ) {
			$result['settingId'] = $this->setting->getId();
		}
		if ( !empty($this->pathInSetting) ) {
			$result['pathInSetting'] = $this->pathInSetting;
		}
		return $result;
	}
}