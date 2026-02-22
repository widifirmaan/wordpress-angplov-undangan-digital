<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls\ControlFlow;

use YahnisElsts\AdminMenuEditor\Customizable\Builders\ElementBuilder;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\StaticHtml;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\UiElement;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Renderer;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\Binding;

class ForEachBlock extends ControlFlowBlock {
	/**
	 * @var Binding
	 */
	private $items;
	/**
	 * @var array<UiElement|ElementBuilder>
	 */
	private $itemTemplateChildren;

	public function __construct(Binding $items, $itemTemplateChildren = [], $params = array(), $children = []) {
		parent::__construct($params, $children);
		$this->items = $items;
		$this->itemTemplateChildren = $itemTemplateChildren;
	}

	protected function getJsUiElementType(): string {
		return 'foreach';
	}

	public function renderContent(Renderer $renderer, Context $context) {
		$optionResult = $context->resolveBinding($this->items);
		if ( $optionResult->isEmpty() ) {
			$renderer->renderElement(
				new StaticHtml(sprintf(
					'<p><em>ForEachBlock: could not resolve "%s".</em></p>',
					esc_html($this->items->getBindingString())
				)),
				$context,
				$this
			);
			return;
		}

		$resolved = $optionResult->get();
		$itemList = $resolved->getCachedValue();

		if ( !is_array($itemList) ) {
			$renderer->renderElement(
				new StaticHtml(sprintf(
					'<p><em>ForEachBlock: "%s" did not resolve to an array.</em></p>',
					esc_html($this->items->getBindingString())
				)),
				$context,
				$this
			);
			return;
		}

		$innerContext = $context->withDataSource($resolved, 'foreach');
		$templateElements = $this->getBuiltTemplateElements();
		foreach ($itemList as $key => $item) {
			$innerContext->setAttribute(Context::CURRENT_KEY_ATTRIBUTE, $key);
			$innerContext->setAttribute(Context::CURRENT_ITEM_ATTRIBUTE, $item);

			$renderer->renderItems($templateElements, $innerContext, $this);
		}
	}

	protected $isTemplateBuilt = false;

	protected function getBuiltTemplateElements(): array {
		//Build the template children only once.
		if ( !$this->isTemplateBuilt ) {
			$elements = [];
			foreach ($this->itemTemplateChildren as $child) {
				if ( $child instanceof ElementBuilder ) {
					$elements[] = $child->build();
				} elseif ( $child instanceof UiElement ) {
					$elements[] = $child;
				} else {
					$typeString = is_object($child) ? get_class($child) : gettype($child);
					throw new \InvalidArgumentException(
						'Invalid item type for ForEachBlock template: ' . $typeString
					);
				}
			}
			$this->itemTemplateChildren = $elements;
			$this->isTemplateBuilt = true;
		}

		return $this->itemTemplateChildren;
	}

	public function serializeForJs(Context $context): array {
		$result = parent::serializeForJs($context);

		$result['items'] = self::serializeBindingForJs($this->items, $context);

		$templateElements = $this->getBuiltTemplateElements();
		if ( !empty($templateElements) ) {
			$result['children'] = [];
			foreach ($templateElements as $child) {
				//Commented out because the JS side doesn't support references yet.
				//$result['children'][] = $child->serializeForJs();
			}
		}

		return $result;
	}
}