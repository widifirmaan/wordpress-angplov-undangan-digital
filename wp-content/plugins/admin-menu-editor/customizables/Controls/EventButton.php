<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Renderer;

class EventButton extends ClassicControl {
	protected $koComponentName = 'ame-event-button';
	protected $eventName = '';
	protected $eventData = [];
	protected $wrap = false;

	public function __construct($settings = [], $params = [], $children = []) {
		parent::__construct($settings, $params, $children);

		if ( array_key_exists('eventName', $params) ) {
			$this->eventName = (string)$params['eventName'];
		} else {
			$this->eventName = 'adminMenuEditor:defaultCustomEvent';
		}

		if ( array_key_exists('eventData', $params) && is_array($params['eventData']) ) {
			$this->eventData = $params['eventData'];
		}

		if ( array_key_exists('wrap', $params) ) {
			$this->wrap = (bool)$params['wrap'];
		}
	}

	public function renderContent(Renderer $renderer, Context $context) {
		//Currently only implemented as a placeholder in HTML output.
		//The real action happens in the Knockout component.
		echo '[EventButton: ' . esc_html($this->getLabel($context)) . ']';
	}

	protected function getKoComponentParams(): array {
		$params = parent::getKoComponentParams();

		if ( !empty($this->eventName) ) {
			$params['eventName'] = $this->eventName;
		}
		if ( !empty($this->eventData) ) {
			$params['eventData'] = $this->eventData;
		}
		$params['wrap'] = $this->wrap;
		return $params;
	}

}