<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Renderer;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractSetting;

class CheckBox extends ClassicControl {
	protected $type = 'checkbox';
	protected $koComponentName = 'ame-toggle-checkbox';

	public function __construct($settings = [], $params = [], $children = []) {
		$this->hasPrimaryInput = true;
		parent::__construct($settings, $params, $children);
	}

	public function renderContent(Renderer $renderer, Context $context) {
		//buildInputElement() is safe, and we intentionally allow HTML in the label and description.
		//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<label>';
		echo $this->buildInputElement(
			$context, [
				'type'      => 'checkbox',
				'checked'   => $this->isChecked(),
				'data-bind' => $this->makeKoDataBind([
					'checked' => $this->getKoObservableExpression($this->isChecked()),
				]),
			]
		);
		echo ' ', $this->getLabel($context);

		$this->outputNestedDescription();
		echo '</label>';
		//phpcs:enable
	}

	public function isChecked() {
		if ( $this->mainBinding instanceof AbstractSetting ) {
			return boolval($this->mainBinding->getValue());
		}
		return false;
	}

	public function includesOwnLabel(): bool {
		return true;
	}

	protected function getKoComponentParams(): array {
		return array_merge(
			parent::getKoComponentParams(),
			[
				'onValue'  => true,
				'offValue' => false,
			]
		);
	}
}