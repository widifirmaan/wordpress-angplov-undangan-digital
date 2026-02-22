<?php

namespace YahnisElsts\AdminMenuEditor\Utils\Forms;

use YahnisElsts\AdminMenuEditor\Customizable\Schemas\Schema;

class KnockoutSaveFormBuilder extends SettingsFormBuilder {
	/**
	 * @var KnockoutSaveFormConfig
	 */
	protected $config;

	protected function createInitialConfig(): SettingsFormConfig {
		return new KnockoutSaveFormConfig();
	}

	public function build(): KnockoutSaveForm {
		$this->assertMinimalBuildRequirements();
		return new KnockoutSaveForm($this->config);
	}

	public function settingsFieldSchema(Schema $schema): self {
		$this->config->settingsFieldSchema = $schema;
		return $this;
	}
}