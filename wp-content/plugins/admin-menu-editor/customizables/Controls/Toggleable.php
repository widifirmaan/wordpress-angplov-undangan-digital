<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;
use YahnisElsts\AdminMenuEditor\Customizable\SettingCondition;

trait Toggleable {
	/**
	 * @var callable
	 */
	protected $enabled = '__return_true';

	protected function parseEnabledParam($params) {
		if ( array_key_exists('enabled', $params) ) {
			if (
				is_bool($params['enabled'])
				|| is_numeric($params['enabled'])
				|| ($params['enabled'] === null)
			) {
				$this->enabled = $params['enabled'] ? '__return_true' : '__return_false';
			} else {
				$this->enabled = $params['enabled'];
			}
		} else if ( !empty($this->mainBinding) ) {
			$this->enabled = function (?Context $context = null) {
				return $this->mainBinding->isEditableByUser($context);
			};
		}
	}

	/**
	 * @param Context|null $context
	 * @return bool
	 */
	public function isEnabled(?Context $context = null) {
		return call_user_func($this->enabled, $context);
	}

	protected function getKoEnableBinding() {
		if ( $this->enabled instanceof SettingCondition ) {
			return ['enable' => $this->enabled->getJsKoExpression()];
		}
		return $this->isEnabled() ? [] : ['enable' => false];
	}

	protected function serializeConditionForJs() {
		if ( $this->enabled instanceof SettingCondition ) {
			return $this->enabled->serializeForJs();
		}
		return $this->isEnabled();
	}
}