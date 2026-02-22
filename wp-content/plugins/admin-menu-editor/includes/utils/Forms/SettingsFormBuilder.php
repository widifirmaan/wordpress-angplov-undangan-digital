<?php

namespace YahnisElsts\AdminMenuEditor\Utils\Forms;

abstract class SettingsFormBuilder {
	protected $config;

	public function __construct() {
		$this->config = $this->createInitialConfig();
	}

	abstract protected function createInitialConfig(): SettingsFormConfig;

	/**
	 * Set the action name that will be used for nonce generation, hooks,
	 * and the hidden "action" field.
	 *
	 * Not to be confused with the "action" attribute of a form element.
	 * Use the submitUrl() method to set that.
	 *
	 * @param string $actionName
	 * @return $this
	 */
	public function action(string $actionName): self {
		$this->config->action = $actionName;
		return $this;
	}

	/**
	 * @param string $httpMethod Either 'get' or 'post'.
	 * @return $this
	 */
	public function method(string $httpMethod): self {
		$httpMethod = trim(strtolower($httpMethod));
		if ( ($httpMethod !== 'get') && ($httpMethod !== 'post') ) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid HTTP method "%s" for a settings form. Must be "get" or "post".',
				$httpMethod
			));
		}

		$this->config->method = $httpMethod;
		return $this;
	}

	public function submitUrl(string $url): self {
		$this->config->submitUrl = $url;
		return $this;
	}

	public function id(string $htmlElementId): self {
		$this->config->formElementId = $htmlElementId;
		return $this;
	}

	public function submitButtonText(string $text): self {
		$this->config->submitButtonText = $text;
		return $this;
	}

	public function successRedirect(string $redirectUrl, array $successParams = ['updated' => 1]): self {
		$this->config->redirectUrl = $redirectUrl;
		$this->config->successRedirectParams = $successParams;
		return $this;
	}

	public function passThroughParams($params): self {
		$this->config->passThroughParams = $params;
		return $this;
	}

	abstract public function build();

	protected function assertMinimalBuildRequirements() {
		if ( empty($this->config->submitUrl) ) {
			throw new \RuntimeException('Cannot build a settings form: submit URL is not set.');
		}
		if ( empty($this->config->action) ) {
			throw new \RuntimeException('Cannot build a settings form: action name is not set.');
		}
	}

	/**
	 * @param \ameModule $module
	 * @return self
	 */
	public function initFromModule(\ameModule $module): self {
		$action = $module->getSettingsFormAction();
		if ( !empty($action) ) {
			$this->action($action);
		}
		$this->submitUrl($module->getTabUrl(['noheader' => 1]));
		$this->successRedirect($module->getTabUrl());
		return $this;
	}
}