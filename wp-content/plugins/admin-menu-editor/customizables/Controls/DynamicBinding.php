<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\ResolvedBinding;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractSetting;
use YahnisElsts\AdminMenuEditor\Customizable\Storage\AbstractSettingsDictionary;

class DynamicBinding implements Binding {
	/**
	 * @var string
	 */
	private $settingIdOrPath;
	/**
	 * @var AbstractSettingsDictionary|null
	 */
	private $dictionary;

	public function __construct($settingIdOrPath, ?AbstractSettingsDictionary $dictionary = null) {
		$this->settingIdOrPath = $settingIdOrPath;
		$this->dictionary = $dictionary;
	}

	public function getSettingDictionary(): ?AbstractSettingsDictionary {
		return $this->dictionary;
	}

	protected function tryResolveFinalSetting(?Context $context, callable $callback) {
		$resolutionError = '[Unresolved setting: ' . $this->settingIdOrPath . ']';

		if ( $context ) {
			$option = $context->resolveBinding($this);
			if ( $option->isEmpty() ) {
				return $resolutionError;
			}

			$resolution = $option->get();
			$path = $resolution->getPathInSetting();
			//Is the setting the final target? If it's not, the setting's label/description
			//might not be applicable to whatever value is being referenced.
			if ( empty($path) ) {
				return call_user_func($callback, $resolution->getSetting(), $context);
			}
		} else {
			$setting = $this->getSettingDirectly();
			if ( $setting ) {
				return call_user_func($callback, $setting, null);
			}
		}

		return $resolutionError;
	}

	protected function getSettingDirectly(): ?AbstractSetting {
		if ( $this->dictionary ) {
			$setting = $this->dictionary->findSetting($this->settingIdOrPath);
			if ( $setting instanceof AbstractSetting ) {
				return $setting;
			}
		}
		return null;
	}

	public function resolveLabel(?Context $context = null): string {
		return $this->tryResolveFinalSetting(
			$context,
			function (AbstractSetting $setting, ?Context $context) {
				return $setting->resolveLabel($context);
			}
		);
	}

	public function resolveDescription(?Context $context = null): string {
		return $this->tryResolveFinalSetting(
			$context,
			function (AbstractSetting $setting, ?Context $context) {
				return $setting->resolveDescription($context);
			}
		);
	}

	public function getBindingString(): string {
		return $this->settingIdOrPath;
	}

	public function isEditableByUser(?Context $context = null): bool {
		if ( $context ) {
			return $context
				->resolveBinding($this)
				->map(function (ResolvedBinding $resolution) {
					return $resolution->getSetting()->isEditableByUser();
				})
				->getOrElse(true);
		}
		return true;
	}
}