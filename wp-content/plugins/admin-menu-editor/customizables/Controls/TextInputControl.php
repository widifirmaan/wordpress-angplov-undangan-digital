<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Renderer;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\StringSetting;

class TextInputControl extends ClassicControl {
	protected $type = 'text';
	protected $koComponentName = 'ame-text-input';

	/**
	 * @var StringSetting
	 */
	protected $mainBinding;

	/**
	 * @var bool Whether to style the value as code (e.g. using fixed width fonts).
	 */
	protected $isCode = false;

	protected $inputType = 'text';

	public function __construct($settings = array(), $params = array(), $children = []) {
		parent::__construct($settings, $params, $children);

		$this->hasPrimaryInput = true;
		$this->isCode = !empty($params['isCode']);
		if ( !empty($params['inputType']) ) {
			$this->inputType = $params['inputType'];
		}
	}

	public function renderContent(Renderer $renderer, Context $context) {
		$classes = array('regular-text');
		if ( $this->isCode ) {
			$classes[] = 'code';
		}
		$classes[] = 'ame-text-input-control';
		$value = $this->getMainSettingValue(null, $context);

		//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- buildInputElement() is safe
		echo $this->buildInputElement(
			$context, array(
				'type'      => $this->inputType,
				'value'     => ($value === null) ? '' : $value,
				'class'     => $classes,
				'style'     => $this->styles,
				'data-bind' => $this->makeKoDataBind(array_merge([
					'value' => $this->getKoObservableExpression($value),
				], $this->getKoEnableBinding())),
			)
		);
		//phpcs:enable
		$this->outputSiblingDescription();
	}

	protected function getKoComponentParams(): array {
		$params = parent::getKoComponentParams();
		$params['isCode'] = $this->isCode;
		$params['inputType'] = $this->inputType;
		return $params;
	}

}