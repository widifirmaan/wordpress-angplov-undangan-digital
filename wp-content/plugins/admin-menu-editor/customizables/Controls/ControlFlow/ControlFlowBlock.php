<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls\ControlFlow;

use YahnisElsts\AdminMenuEditor\Customizable\Controls\UiElement;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;
use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Renderer;

abstract class ControlFlowBlock extends UiElement {
	abstract public function renderContent(Renderer $renderer, Context $context);
}