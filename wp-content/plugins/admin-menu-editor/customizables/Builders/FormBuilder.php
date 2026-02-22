<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Builders;

use YahnisElsts\AdminMenuEditor\Customizable\Controls;
use YahnisElsts\AdminMenuEditor\Customizable\FormConfig;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Renderer;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractSetting;
use YahnisElsts\AdminMenuEditor\Customizable\SettingsForm;
use YahnisElsts\AdminMenuEditor\Utils\Forms\SettingsFormBuilder;
use YahnisElsts\AdminMenuEditor\Utils\Forms\SettingsFormConfig;

class FormBuilder extends SettingsFormBuilder {
	/**
	 * @var FormConfig
	 */
	protected $config;

	protected function createInitialConfig(): SettingsFormConfig {
		return new FormConfig();
	}

	public function structure(Controls\InterfaceStructure $structure): self {
		$this->config->structure = $structure;
		return $this;
	}

	/**
	 * @param array<string,AbstractSetting> $settings
	 * @return $this
	 */
	public function settings(array $settings): self {
		$this->config->settings = $settings;
		return $this;
	}

	public function renderer(Renderer $renderer): self {
		$this->config->renderer = $renderer;
		return $this;
	}

	/**
	 * Alias for action().
	 *
	 * @param string $actionName
	 * @return $this
	 */
	public function actionName(string $actionName): self {
		return $this->action($actionName);
	}

	public function requiredCapability(?string $capability): self {
		$this->config->requiredCapability = $capability;
		return $this;
	}

	/**
	 * @param callable $callback
	 * @return $this
	 */
	public function permissionCallback($callback): self {
		$this->config->permissionCallback = $callback;
		return $this;
	}

	/**
	 * @param bool $shouldAddButton
	 * @return $this
	 */
	public function addDefaultSubmitButton(bool $shouldAddButton = true): self {
		$this->config->defaultSubmitButtonEnabled = $shouldAddButton;
		return $this;
	}

	public function redirectAfterSaving($url, $successParams = ['updated' => 1]): self {
		return $this->successRedirect($url, $successParams);
	}

	public function dieOnError(): self {
		$this->config->errorReporting = SettingsForm::DIE_ON_ERRORS;
		return $this;
	}

	public function storeErrors($transientName = null): self {
		$this->config->errorReporting = SettingsForm::STORE_ERRORS;
		$this->config->errorTransientName = $transientName;
		return $this;
	}

	public function postProcessSettings($callback): self {
		$this->config->postProcessingCallback = $callback;
		return $this;
	}

	public function skipMissingFields(): self {
		$this->config->missingFieldHandling = SettingsForm::SKIP_MISSING_FIELDS;
		return $this;
	}

	public function treatMissingFieldsAsEmpty(): self {
		$this->config->missingFieldHandling = SettingsForm::TREAT_MISSING_FIELDS_AS_EMPTY;
		return $this;
	}

	public function allowPartialUpdates(): self {
		$this->config->partialUpdatesAllowed = true;
		return $this;
	}

	public function forbidPartialUpdates() {
		$this->config->partialUpdatesAllowed = false;
		return $this;
	}

	public function stopOnFirstValidationError() {
		$this->config->stopOnFirstError = true;
		return $this;
	}

	public function build(): SettingsForm {
		return new SettingsForm($this->config);
	}
}