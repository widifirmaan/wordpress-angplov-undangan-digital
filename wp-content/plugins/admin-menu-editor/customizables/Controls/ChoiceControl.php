<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\Schemas\Enum;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\EnumSetting;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\Setting;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\WithSchema\SettingWithSchema;

abstract class ChoiceControl extends ClassicControl {
	protected $type = 'choice';

	/**
	 * @var Setting
	 */
	protected $mainBinding;

	/**
	 * @var ChoiceControlOption[]
	 */
	protected $options = [];

	public function __construct($settings = [], $params = [], $children = []) {
		parent::__construct($settings, $params, $children);

		if ( isset($params['choices']) ) {
			if ( is_callable($params['choices']) ) {
				$choices = call_user_func($params['choices']);
			} else {
				$choices = $params['choices'];
			}

			foreach ($choices as $key => $item) {
				if ( is_string($item) ) {
					//List of [value => label] pairs.
					$this->options[] = new ChoiceControlOption($key, $item);
				} else if ( is_array($item) ) {
					//List of arrays where each item is [value => X, label => Y, ...].
					//Alternatively, [value => [label => Y, ...]] pairs.
					if ( !array_key_exists('value', $item) ) {
						$item['value'] = $key;
					}
					$this->options[$key] = ChoiceControlOption::fromArray($item);
				} else if ( $item instanceof ChoiceControlOption ) {
					//List of nicely predefined option objects.
					$this->options[] = $item;
				} else {
					throw new \InvalidArgumentException("Invalid option: $item");
				}
			}
		} else if ( $this->mainBinding instanceof EnumSetting ) {
			$this->options = $this->mainBinding->generateChoiceOptions();
		} else if ( $this->mainBinding instanceof SettingWithSchema ) {
			$schema = $this->mainBinding->getSchema();
			if ( $schema instanceof Enum ) {
				$this->options = ChoiceControlOption::fromEnumSchema($schema);
			}
		}
	}

	protected function getKoComponentParams(): array {
		$params = parent::getKoComponentParams();
		$params['options'] = array_map(
			function ($option) {
				return $option->serializeForJs();
			},
			$this->options
		);
		return $params;
	}
}