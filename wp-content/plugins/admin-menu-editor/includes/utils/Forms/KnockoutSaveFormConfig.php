<?php

namespace YahnisElsts\AdminMenuEditor\Utils\Forms;

use YahnisElsts\AdminMenuEditor\Customizable\Schemas;

class KnockoutSaveFormConfig extends SettingsFormConfig {
	/**
	 * @var Schemas\Schema
	 */
	public $settingsFieldSchema;

	public function __construct() {
		//Default schema accepts anything. You should usually override this in the form builder.
		$this->settingsFieldSchema = new Schemas\Anything();
	}
}