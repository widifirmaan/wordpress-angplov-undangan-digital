<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Rendering;

use YahnisElsts\AdminMenuEditor\Customizable\Controls\Control;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\ControlFlow\ControlFlowBlock;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\ControlGroup;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\InterfaceStructure;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\Section;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\Tooltip;
use YahnisElsts\AdminMenuEditor\Customizable\Controls\UiElement;

abstract class Renderer {

	/**
	 * @param UiElement[] $elements
	 * @param Context $context
	 * @param UiElement|null $parent
	 * @return void
	 */
	public function renderItems(array $elements, Context $context, ?UIElement $parent = null) {
		foreach ($elements as $element) {
			if ( !$element->shouldRender() ) {
				continue;
			}
			$this->renderElement($element, $context, $parent);
		}
	}

	public function renderElement(UiElement $element, Context $context, ?UIElement $parent = null) {
		if ( $element instanceof Section ) {
			if ( $parent instanceof Section ) {
				$this->renderChildSection($element, $context);
			} else {
				$this->renderSection($element, $context);
			}
		} else if ( $element instanceof ControlGroup ) {
			if ( $parent instanceof ControlGroup ) {
				$this->renderChildControlGroup($element, $context);
			} else {
				$this->renderControlGroup($element, $context);
			}
		} else if ( $element instanceof Control ) {
			if ( $parent instanceof ControlGroup ) {
				$this->renderControl($element, $context);
			} else {
				$this->renderUngroupedControl($element, $context);
			}
		} else if ( $element instanceof ControlFlowBlock ) {
			$this->renderControlFlowBlock($element, $context);
		} else {
			throw new \RuntimeException('Unexpected element type: ' . (get_class($element)));
		}
	}

	/**
	 * @param InterfaceStructure $structure
	 * @return void
	 */
	public function renderStructure(InterfaceStructure $structure) {
		$context = new Context();
		$this->renderItems($structure->getAsSections(), $context);
	}

	/**
	 * @param Section $section
	 * @param Context $context
	 * @return void
	 */
	abstract public function renderSection(Section $section, Context $context);

	protected function renderSectionChildren(Section $section, Context $context) {
		$this->renderItems($section->getChildren(), $context, $section);
	}

	/**
	 * @param Section $section
	 * @param Context $context
	 * @return void
	 */
	protected function renderChildSection(Section $section, Context $context) {
		$this->renderSection($section, $context);
	}

	/**
	 * @param ControlGroup $group
	 * @param Context $context
	 * @return void
	 */
	abstract protected function renderControlGroup(ControlGroup $group, Context $context);

	protected function renderGroupChildren(ControlGroup $group, Context $context) {
		$this->renderItems($group->getChildren(), $context, $group);
	}

	protected function renderChildControlGroup(ControlGroup $group, Context $context) {
		$this->renderControlGroup($group, $context);
	}

	/**
	 * @param Control $control
	 * @param Context $context
	 */
	protected function renderUngroupedControl(Control $control, Context $context) {
		$params = [];
		$controlId = $control->getHtmlIdBase($context);
		if ( $controlId ) {
			$params['id'] = 'ame_control_group-' . $controlId;
		}

		$tempGroup = new ControlGroup($control->getAutoGroupTitle(), $params, [$control]);
		$this->renderControlGroup($tempGroup, $context);
	}

	/**
	 * @param Control $control
	 * @param Context $context
	 */
	public function renderControl(Control $control, Context $context) {
		$control->renderContent($this, $context);
	}

	abstract public function renderTooltipTrigger(Tooltip $tooltip);

	public function renderControlFlowBlock(ControlFlowBlock $block, Context $context) {
		if ( !$block->shouldRender() ) {
			return;
		}
		$block->renderContent($this, $context);
	}

	/**
	 * @param string $containerSelector The CSS selector for the element that contains
	 *                                  the rendered controls. Typically, this is the form element.
	 * @return void
	 */
	public function enqueueDependencies(string $containerSelector = '') {
		//No dependencies by default.
	}
}