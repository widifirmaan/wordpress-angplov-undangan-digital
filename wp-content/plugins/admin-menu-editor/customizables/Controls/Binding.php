<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

use YahnisElsts\AdminMenuEditor\Customizable\Rendering\Context;

interface Binding {
	public function resolveLabel(?Context $context = null): string;

	public function resolveDescription(?Context $context = null): string;

	public function getBindingString(): string;

	public function isEditableByUser(?Context $context = null);
}