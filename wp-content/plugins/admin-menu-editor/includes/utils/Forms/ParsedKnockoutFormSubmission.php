<?php

namespace YahnisElsts\AdminMenuEditor\Utils\Forms;

class ParsedKnockoutFormSubmission extends ParsedFormSubmission {
	private $settings;

	public function __construct(ParsedFormSubmission $submission, array $settings) {
		parent::__construct(
			$submission->getForm(),
			$submission->getRequestParams(),
			$submission->getQueryParams(),
			$submission->selectedActorId,
			$submission->selectedActorFieldName
		);
		$this->settings = $settings;
	}

	public function getSettings(): array {
		return $this->settings;
	}
}