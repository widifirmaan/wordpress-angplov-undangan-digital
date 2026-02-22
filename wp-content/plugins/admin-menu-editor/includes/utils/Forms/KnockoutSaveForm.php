<?php

namespace YahnisElsts\AdminMenuEditor\Utils\Forms;

use WP_Error;

class KnockoutSaveForm extends GenericSettingsForm {
	/**
	 * @var KnockoutSaveFormConfig
	 */
	protected $config;

	protected $settingsFieldName = 'settings';

	public function __construct(KnockoutSaveFormConfig $config) {
		parent::__construct($config);
	}

	/**
	 * Get the configuration data for the JavaScript part of the form.
	 *
	 * See the SaveFormConfigFromServer interface in free-ko-extensions.ts.
	 *
	 * @return array
	 */
	public function getJsSaveFormConfig(): array {
		$config = [
			'action'      => $this->config->action,
			'actionNonce' => wp_create_nonce($this->config->action),
			'submitUrl'   => $this->config->submitUrl,
			'referer'     => remove_query_arg('_wp_http_referer'),
		];
		if ( !empty($this->config->submitButtonText) ) {
			$config['saveButtonText'] = $this->config->submitButtonText;
		}
		return $config;
	}

	public function processKnockoutSubmission(array $post, array $queryParams =[]): ParsedKnockoutFormSubmission {
		$submission = $this->preprocessSubmission($post, $queryParams);

		if ( empty($post[$this->settingsFieldName]) ) {
			$this->handleError(new WP_Error(
				'ame_missing_settings_field',
				sprintf('The "%s" field is missing or empty.', $this->settingsFieldName)
			));
		}

		if ( !is_string($post[$this->settingsFieldName]) ) {
			$this->handleError(new WP_Error(
				'ame_invalid_settings_field',
				sprintf('The "%s" field must contain a JSON-encoded string.', $this->settingsFieldName)
			));
		}

		$newSettings = json_decode($post[$this->settingsFieldName], true);
		if ( !is_array($newSettings) ) {
			$this->handleError(new WP_Error(
				'ame_invalid_settings_data',
				sprintf('The "%s" field must contain a valid JSON object.', $this->settingsFieldName)
			));
		}

		$parsed = $this->config->settingsFieldSchema->parse($newSettings);
		if ( is_wp_error($parsed) ) {
			$this->handleError($parsed);
		}
		$newSettings = $parsed;

		return new ParsedKnockoutFormSubmission($submission, $newSettings);
	}

	/**
	 * @param string $action
	 * @return SettingsFormBuilder<self>
	 */
	public static function builder(string $action): SettingsFormBuilder {
		return (new KnockoutSaveFormBuilder())->action($action);
	}

	/**
	 * @param \ameModule $module
	 * @return KnockoutSaveFormBuilder
	 */
	public static function builderFor(\ameModule $module): SettingsFormBuilder {
		return (new KnockoutSaveFormBuilder())->initFromModule($module);
	}
}