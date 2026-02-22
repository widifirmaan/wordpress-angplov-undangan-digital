<?php

namespace YahnisElsts\AdminMenuEditor\Customizable\Controls;

class NumberConfig {
	private $min;
	private $max;
	private $step;
	private $bindingMayContainFloat;
	private $estimatedMaxDigits;

	public function __construct(
		$min = null,
		$max = null,
		$step = null,
		$bindingMayContainFloat = false,
		?int $estimatedMaxDigits = null
	) {
		$this->min = $min;
		$this->max = $max;
		$this->step = $step;
		$this->bindingMayContainFloat = $bindingMayContainFloat;
		$this->estimatedMaxDigits = $estimatedMaxDigits;
	}

	/**
	 * @return null
	 */
	public function getMin() {
		return $this->min;
	}

	/**
	 * @return null
	 */
	public function getMax() {
		return $this->max;
	}

	/**
	 * @return null
	 */
	public function getStep() {
		return $this->step;
	}

	public function mayContainFloat(): bool {
		return $this->bindingMayContainFloat;
	}

	/**
	 * @return int|null
	 */
	public function getEstimatedMaxDigits(): ?int {
		return $this->estimatedMaxDigits;
	}
}