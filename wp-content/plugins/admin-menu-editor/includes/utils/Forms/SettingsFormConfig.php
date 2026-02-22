<?php

namespace YahnisElsts\AdminMenuEditor\Utils\Forms;

class SettingsFormConfig {
	/**
	 * @var string Action used for nonce generation and verification.
	 */
	public $action = '';

	/**
	 * @var string URL where the form will be submitted.
	 */
	public $submitUrl = '';

	/**
	 * @var string HTTP method used for form submission. Either 'post' or 'get'.
	 */
	public $method = 'post';

	public $submitButtonText = ''; //Defaults to "Save Changes" if empty.
	/**
	 * @var bool Whether to include the primary submit button in the form.
	 */
	public $submitButtonEnabled = true;

	/**
	 * @var string URL to redirect to after the form is submitted and processed.
	 */
	public $redirectUrl = '';
	/**
	 * @var array<string,mixed> Query parameters to add to the redirect URL on success.
	 */
	public $successRedirectParams = ['updated' => 1];
	/**
	 * @var array Optional query parameters to pass through when redirecting.
	 */
	public $passThroughParams = [];

	/**
	 * @var string|null ID attribute of the form element.
	 */
	public $formElementId = null;

	/**
	 * @var array<string> Names of the request parameters that may contain the selected actor's ID.
	 *  If specified, they will be checked in order when retrieving the selected actor.
	 */
	public $selectedActorParamNames = [
		GenericSettingsForm::NEW_SELECTED_ACTOR_FIELD,
		GenericSettingsForm::CLASSIC_SELECTED_ACTOR_FIELD,
	];
}