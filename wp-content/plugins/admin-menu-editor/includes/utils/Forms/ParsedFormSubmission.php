<?php

namespace YahnisElsts\AdminMenuEditor\Utils\Forms;

class ParsedFormSubmission {
	/**
	 * @var GenericSettingsForm
	 */
	protected $form;

	protected $selectedActorId = null;
	protected $selectedActorFieldName = null;

	protected $requestParams = [];
	protected $queryParams = [];

	public function __construct(
		GenericSettingsForm $form,
		array               $requestParams,
		array               $queryParams,
		?string             $selectedActorId,
		?string             $selectedActorFieldName
	) {
		$this->form = $form;
		$this->requestParams = $requestParams;
		$this->queryParams = $queryParams;
		$this->selectedActorId = $selectedActorId;
		$this->selectedActorFieldName = $selectedActorFieldName;
	}

	/**
	 * Get an associative array with the selected actor field name as the key
	 * and the selected actor ID as the value.
	 *
	 * Example: ['selectedActor' => 'role:editor']
	 *
	 * @return array
	 */
	public function getSelectedActorParams(): array {
		return [$this->selectedActorFieldName => $this->selectedActorId];
	}

	public function getRequestParams(): array {
		return $this->requestParams;
	}

	public function getQueryParams(): array {
		return $this->queryParams;
	}

	public function getRequestParam(string $name) {
		return $this->requestParams[$name] ?? null;
	}

	public function getQueryParam(string $name) {
		return $this->queryParams[$name] ?? null;
	}

	protected function getForm(): GenericSettingsForm {
		return $this->form;
	}

	/**
	 * Convenience method to perform a success redirect using the form's configuration.
	 *
	 * @param array $extraParams
	 */
	public function performSuccessRedirect(array $extraParams = []) {
		$this->form->performSuccessRedirect($this, $extraParams);
	}
}