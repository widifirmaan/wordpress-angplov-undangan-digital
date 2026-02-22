<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\HtmlHelper;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;
use YahnisElsts\AdminMenuEditor\Customizable\Settings\AbstractSetting;

abstract class UiElement {
	/**
	 * @var string
	 */
	protected $id = '';

	/**
	 * @var string|callable|null
	 */
	protected $description = '';

	/**
	 * @var array List of CSS classes to apply to the outermost DOM node of the element.
	 * This property might not be meaningful for elements that output multiple nodes without
	 * a common parent or that don't have a visible representation.
	 */
	protected $classes = array();

	/**
	 * @var array List of CSS styles to apply to the outermost DOM node of the element.
	 */
	protected $styles = array();

	/**
	 * @var UiElement[]
	 */
	protected $children = [];

	protected $renderCondition = true;

	/**
	 * Lets the renderer know that the element doesn't want new line breaks added
	 * before and after its content.
	 *
	 * - Block elements (e.g. &lt;fieldset&gt;) and elements that surround their
	 * content with &lt;p&gt; or &lt;br&gt; tags should set this to true.
	 * - Elements that output partial or unclosed tags should also set this to
	 * true to avoid producing invalid HTML.
	 *
	 * @var bool
	 */
	protected $declinesExternalLineBreaks = false;

	/**
	 * @var null|Tooltip
	 */
	protected $tooltip = null;

	public function __construct($params = [], $children = []) {
		if ( !empty($params['id']) ) {
			$this->id = $params['id'];
		}
		if ( !empty($params['description']) ) {
			$this->description = $params['description'];
		}
		if ( !empty($params['classes']) ) {
			$this->classes = (array)$params['classes'];
		}
		if ( !empty($params['styles']) ) {
			$this->styles = (array)$params['styles'];
		}
		if ( isset($params['renderCondition']) ) {
			$this->renderCondition = $params['renderCondition'];
		}
		if ( isset($params['tooltip']) ) {
			$this->tooltip = $params['tooltip'];
		}

		foreach ($children as $child) {
			$this->add($child);
		}
	}

	/**
	 * @param UiElement $child
	 * @return void
	 */
	public function add(UiElement $child) {
		$this->children[] = $child;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	public function getHtmlIdBase(?Context $context = null) {
		return $this->getId();
	}

	/**
	 * @return string
	 */
	public function getDescription(?Context $context = null) {
		if ( is_string($this->description) ) {
			return $this->description;
		} elseif ( is_callable($this->description) ) {
			return call_user_func($this->description);
		} elseif ( isset($this->description) ) {
			return strval($this->description);
		} else if ( !empty($this->mainBinding) ) {
			$this->description = $this->mainBinding->resolveDescription($context);
		}
		return '';
	}

	/**
	 * @return array
	 */
	public function getClasses() {
		return $this->classes;
	}

	/**
	 * @return bool
	 */
	public function hasTooltip() {
		return ($this->tooltip !== null);
	}

	/**
	 * @return Tooltip|null
	 */
	public function getTooltip() {
		return $this->tooltip;
	}

	protected function buildTag($tagName, $attributes = array(), $content = null) {
		return HtmlHelper::tag($tagName, $attributes, $content);
	}

	/**
	 * @return bool
	 */
	public function declinesExternalLineBreaks() {
		return $this->declinesExternalLineBreaks;
	}

	public function shouldRender() {
		if ( is_callable($this->renderCondition) ) {
			return call_user_func($this->renderCondition);
		}
		return (bool)$this->renderCondition;
	}

	public function serializeForJs(Context $context): array {
		$result = ['t' => $this->getJsUiElementType()];

		if ( !empty($this->id) ) {
			$result['id'] = $this->id;
		}

		$params = $this->getKoComponentParams();

		if ( !empty($this->classes) ) {
			$params['classes'] = $this->classes;
		}
		if ( !empty($this->styles) ) {
			$params['styles'] = $this->styles;
		}
		$description = $this->getDescription();
		if ( !empty($description) ) {
			$params['description'] = $description;
		}

		if ( $this->hasTooltip() ) {
			$params['tooltip'] = $this->tooltip->serializeForJs();
		}

		if ( !empty($params) ) {
			$result['params'] = $params;
		}

		if ( !empty($this->children) ) {
			$result['children'] = [];
			foreach ($this->children as $child) {
				$result['children'][] = $child->serializeForJs($context);
			}
		}

		return $result;
	}

	abstract protected function getJsUiElementType();

	protected static function serializeBindingForJs(Binding $binding, Context $context) {
		if ( $binding instanceof AbstractSetting ) {
			return $binding->getId();
		} else {
			$option = $context->resolveBinding($binding);
			if ( $option->isDefined() ) {
				$resolution = $option->get();
				return (object)[
					'bind' => $resolution->serializeForJs(),
				];
			}
			return (object)['bind' => $binding->getBindingString()];
		}
	}

	/**
	 * Recursively get all settings referenced by this element and its descendants.
	 *
	 * @param Context $context Used to resolve bindings.
	 */
	public function getAllReferencedSettings(Context $context) {
		return [];
	}

	/**
	 * Get additional parameters for the Knockout component that renders this element.
	 *
	 * @return array
	 */
	protected function getKoComponentParams(): array {
		return [];
	}

	public function enqueueKoComponentDependencies() {
		//Do nothing by default.
	}
}