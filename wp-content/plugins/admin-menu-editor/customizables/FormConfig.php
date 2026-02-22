<?php

namespace YahnisElsts\AdminMenuEditor\Customizable;

use YahnisElsts\AdminMenuEditor\Customizable\Controls\InterfaceStructure;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Renderer;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractSetting;
use YahnisElsts\AdminMenuEditor\Customizable\SettingsForm;
use YahnisElsts\AdminMenuEditor\Utils\Forms\SettingsFormConfig;

class FormConfig extends SettingsFormConfig {
	/**
	 * @var InterfaceStructure
	 */
	public $structure;

	/**
	 * @var array<string, AbstractSetting>
	 */
	public $settings = [];

	/**
	 * @var Renderer|null
	 */
	public $renderer = null;

	/**
	 * @var string|null
	 */
	public $requiredCapability = null;

	/**
	 * @var callable|null
	 */
	public $permissionCallback = null;

	public $defaultSubmitButtonEnabled = true;

	/**
	 * @var int One of the SettingsForm::DIE_ON_ERRORS or
	 * SettingsForm::STORE_ERRORS constants.
	 */
	public $errorReporting = SettingsForm::DIE_ON_ERRORS;
	/**
	 * @var string|null
	 */
	public $errorTransientName = null;

	/**
	 * @var int One of the SettingsForm::SKIP_MISSING_FIELDS or
	 * SettingsForm::TREAT_MISSING_FIELDS_AS_EMPTY constants.
	 */
	public $missingFieldHandling = SettingsForm::SKIP_MISSING_FIELDS;

	/**
	 * @var callable|null
	 */
	public $postProcessingCallback = null;

	/**
	 * @var bool When some of the submitted settings are invalid, should we still
	 *  save the settings that are valid?
	 */
	public $partialUpdatesAllowed = false;

	/**
	 * @var bool Whether to stop validation after the first error,
	 * or continue validating the rest of the settings.
	 */
	public $stopOnFirstError = false;
}