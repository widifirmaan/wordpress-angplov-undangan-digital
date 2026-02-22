<?php

namespace YahnisElsts\AdminMenuEditor\Utils\Forms;

abstract class GenericSettingsForm {
	const NEW_SELECTED_ACTOR_FIELD = 'selectedActor';
	const CLASSIC_SELECTED_ACTOR_FIELD = 'selected_actor';

	/**
	 * @var SettingsFormConfig
	 */
	protected $config;

	protected function __construct(SettingsFormConfig $config) {
		//Copy the config to (mostly) prevent external modifications after initialization.
		$this->config = clone $config;
	}

	/**
	 * Perform nonce checks, parameter parsing, and any other configured preprocessing.
	 *
	 * Does not handle the actual updating of settings or post-submission redirection.
	 * Meant to be called at the start of form submission handling, or used by modules
	 * that want to handle submission processing more directly.
	 *
	 * @param array $requestParams
	 * @param array $queryParams
	 * @return ParsedFormSubmission
	 */
	public function preprocessSubmission(array $requestParams, array $queryParams = []): ParsedFormSubmission {
		//Check action.
		$action = strval($requestParams['action'] ?? '');
		if ( !empty($this->config->action) && ($action !== $this->config->action) ) {
			$this->handleError(new \WP_Error(
				'ame_invalid_action',
				sprintf(
					'The action parameter has an invalid value. Expected: "%s", actual value: "%s".',
					esc_html($this->config->action),
					esc_html($action)
				)
			));
		}

		//Verify nonce.
		if ( !empty($this->config->action) ) {
			if ( wp_doing_ajax() ) {
				check_ajax_referer($this->config->action);
			} else {
				check_admin_referer($this->config->action);
			}
		}

		//Get the selected actor (most but not all forms have this).
		list($foundSelectedActorId, $foundSelectedActorParam) = $this->extractSelectedActorDetails($requestParams);

		return new ParsedFormSubmission(
			$this,
			$requestParams,
			$queryParams,
			$foundSelectedActorId,
			$foundSelectedActorParam
		);
	}

	/**
	 * @param \WP_Error $error
	 * @return never-returns
	 */
	protected function handleError(\WP_Error $error) {
		wp_die(wsAmeEscapeWpError($error));
	}

	/**
	 * Extract the selected actor details from the request parameters.
	 *
	 * @param array $params Request parameters (e.g. $_POST or $_GET).
	 * @return array{0: string|null, 1: string|null} Tuple of selected actor ID and parameter name.
	 *  Both can be NULL if no valid actor ID was found.
	 */
	public function extractSelectedActorDetails(array $params): array {
		$foundSelectedActorId = null;
		$foundSelectedActorParam = null;
		foreach ($this->config->selectedActorParamNames as $paramName) {
			if ( isset($params[$paramName]) && $this->couldBeValidActorId($params[$paramName]) ) {
				$foundSelectedActorId = strval($params[$paramName]);
				$foundSelectedActorParam = $paramName;
				break;
			}
		}
		return [$foundSelectedActorId, $foundSelectedActorParam];
	}

	protected function couldBeValidActorId($actorId): bool {
		return (
			is_string($actorId)
			&& (strlen($actorId) <= 200)
			&& (
				($actorId === 'special:super_admin')
				|| preg_match('/^(role|user):[a-z0-9_]+$/i', $actorId)
			)
		);
	}

	/**
	 * Get the selected actor ID from the request parameters.
	 *
	 * Utility method for when you only need the actor ID and not the parameter name.
	 *
	 * @param array $params
	 * @return string|null
	 */
	public function getSelectedActorId(array $params): ?string {
		list($foundSelectedActorId,) = $this->extractSelectedActorDetails($params);
		return $foundSelectedActorId;
	}

	/**
	 * Redirect to the configured success URL, adding any configured query parameters.
	 *
	 * @param ParsedFormSubmission|null $submission Used to retrieve selected actor, if applicable.
	 * @param array<string,mixed> $extraParams Additional query parameters (added on top of the configured ones).
	 * @return never-returns
	 */
	public function performSuccessRedirect(?ParsedFormSubmission $submission = null, array $extraParams = []): void {
		$this->performRedirect($submission, $this->config->successRedirectParams, $extraParams);
	}

	/**
	 * @param ParsedFormSubmission|null $submission
	 * @param array $redirectParams
	 * @param array $extraParams
	 * @return never-returns
	 */
	protected function performRedirect(
		?ParsedFormSubmission $submission = null,
		array $redirectParams = [],
		array $extraParams = []
	) {
		if ( empty($this->config->redirectUrl) ) {
			throw new \RuntimeException('Cannot perform a redirect: no redirect URL is configured.');
		}

		//Pass through the selected actor, if any.
		if ( $submission ) {
			$redirectParams = array_merge($redirectParams, $submission->getSelectedActorParams());
		}

		//Optionally, pass through other configured parameters.
		//(We need the submission to access form fields and query params.)
		if ( $submission ) {
			foreach ($this->config->passThroughParams as $paramName) {
				//Do not overwrite existing parameters.
				if ( array_key_exists($paramName, $redirectParams) ) {
					continue;
				}

				//Prefer request parameters, then query parameters.
				//These could be the same if it's a GET request (but usually our forms use POST).
				$paramValue = $submission->getRequestParam($paramName);
				if ( $paramValue !== null ) {
					$redirectParams[$paramName] = $paramValue;
				} else {
					$paramValue = $submission->getQueryParam($paramName);
					if ( $paramValue !== null ) {
						$redirectParams[$paramName] = $paramValue;
					}
				}
			}
		}

		$redirectParams = array_merge($redirectParams, $extraParams);

		if ( wp_safe_redirect(add_query_arg($redirectParams, $this->config->redirectUrl)) ) {
			exit;
		} else {
			wp_die(wsAmeEscapeWpError(new \WP_Error(
				'ame_redirect_failed',
				'Failed to redirect to the next page.'
			)));
		}
	}
}