<?php

namespace YahnisElsts\AdminMenuEditor\Collections;

use YahnisElsts\AdminMenuEditor\Options\Option;

interface IterableOps {
	public function flatMap($callback): self;

	public function toArray(): array;
}

class ArrayWrapper implements \IteratorAggregate, IterableOps {
	private array $array;

	public function __construct(array $array) {
		$this->array = $array;
	}

	public function toArray(): array {
		return $this->array;
	}

	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->array);
	}

	/**
	 * Utility method that simply returns the value passed to it.
	 *
	 * @template V
	 * @param V $value
	 * @return V Returns $value.
	 */
	public static function identity($value) {
		return $value;
	}

	public function get($key): Option {
		if ( array_key_exists($key, $this->array) ) {
			return Option::some($this->array[$key]);
		} else {
			return Option::none();
		}
	}

	/**
	 * Creates a new collection by applying the callback to each element in the array.
	 *
	 * Note: Unlike array_map(), the callback receives both the value and the key as arguments.
	 *
	 * @param callable(mixed $value, int|string $key):mixed $callback
	 * @return self
	 */
	public function map(callable $callback): self {
		$mapped = [];
		foreach ($this->array as $key => $value) {
			$mapped[$key] = $callback($value, $key);
		}
		return new ArrayWrapper($mapped);
	}

	/**
	 * Creates a new array by applying the callback to each value in the array.
	 *
	 * The callback receives only the value as an argument. Useful for compatibility with array_map()
	 * and built-in functions.
	 *
	 * @param callable(mixed $value):mixed $callback
	 * @return self
	 */
	public function mapValues(callable $callback): self {
		return new ArrayWrapper(array_map($callback, $this->array));
	}

	/**
	 * @template R
	 * @param callable(mixed $value):IterableOps<R> $callback
	 * @return IterableOps<R>
	 */
	public function flatMap($callback): IterableOps {
		$results = [];
		foreach ($this->array as $value) {
			$mapped = $callback($value);
			if ( is_iterable($mapped) ) {
				foreach ($mapped as $item) {
					$results[] = $item;
				}
			} else {
				throw new \InvalidArgumentException('Callback must return an iterable.');
			}
		}
		return new ArrayWrapper($results);
	}

	public function intersect($other): self {
		if ( $other instanceof ArrayWrapper ) {
			$otherArray = $other->toArray();
		} else if ( is_array($other) ) {
			$otherArray = $other;
		} else {
			throw new \InvalidArgumentException('Argument must be an array or ArrayWrapper.');
		}

		$intersected = array_intersect($this->array, $otherArray);
		return new ArrayWrapper($intersected);
	}

	public function diff($other): self {
		if ( $other instanceof ArrayWrapper ) {
			$otherArray = $other->toArray();
		} else if ( is_array($other) ) {
			$otherArray = $other;
		} else {
			throw new \InvalidArgumentException('Argument must be an array or ArrayWrapper.');
		}

		$diffed = array_diff($this->array, $otherArray);
		return new ArrayWrapper($diffed);
	}

	/**
	 * Like diff(), but uses strict comparison (===) when comparing values.
	 *
	 * @param array|ArrayWrapper $other
	 * @return self
	 */
	public function strictDiff($other): self {
		if ( $other instanceof ArrayWrapper ) {
			$otherArray = $other->toArray();
		} else if ( is_array($other) ) {
			$otherArray = $other;
		} else {
			throw new \InvalidArgumentException('Argument must be an array or ArrayWrapper.');
		}

		$diffed = array_udiff($this->array, $otherArray, function ($a, $b) {
			if ( $a === $b ) {
				return 0;
			}
			return strcmp((string)$a, (string)$b);
		});
		return new ArrayWrapper($diffed);
	}

	/**
	 * Filter the array using the provided callback.
	 *
	 * Array keys are preserved.
	 *
	 * Note: Unfortunately, the argument cannot be type-hinted as callable here because we want a default
	 * "identity" callback and PHP doesn't seem to have a reasonable way to express that. Arrays like
	 * ['ClassName', 'method'] and strings like 'ClassName::method' are valid callables, but they get
	 * rejected as syntax errors.
	 *
	 * @param callable $callback
	 * @return self
	 */
	public function filterValues($callback = [self::class, 'identity']): self {
		$filtered = array_filter($this->array, $callback);
		return new ArrayWrapper($filtered);
	}

	public function rejectValues(callable $callback): self {
		$filtered = array_filter($this->array, function ($item) use ($callback) {
			return !$callback($item);
		});
		return new ArrayWrapper($filtered);
	}

	public function rejectKeys(callable $callback): self {
		$filtered = array_filter($this->array, function ($key) use ($callback) {
			return !$callback($key);
		}, ARRAY_FILTER_USE_KEY);
		return new ArrayWrapper($filtered);
	}

	public function implode($separator): string {
		return implode($separator, $this->array);
	}

	public function pick($keys): self {
		if ( $keys instanceof ArrayWrapper ) {
			$keys = $keys->toArray();
		}

		$picked = [];
		foreach ($keys as $key) {
			if ( array_key_exists($key, $this->array) ) {
				$picked[$key] = $this->array[$key];
			}
		}
		return new ArrayWrapper($picked);
	}

	/**
	 * Creates a new ArrayWrapper where keys that exist in $source are added to the current array
	 * only if they do not already exist in the current array.
	 *
	 * This is similar to array_merge() with $source as the first argument and the current
	 * array as the second.
	 *
	 * @param array|ArrayWrapper $source
	 * @return self
	 */
	public function defaults($source): self {
		if ( $source instanceof ArrayWrapper ) {
			$source = $source->toArray();
		}

		$merged = $this->array + $source;
		return new ArrayWrapper($merged);
	}

	public function keys(): self {
		return new ArrayWrapper(array_keys($this->array));
	}

	public function contains($value): bool {
		return in_array($value, $this->array, true);
	}

	public function headOption() {
		if ( empty($this->array) ) {
			return Option::none();
		} else {
			$firstKey = array_key_first($this->array);
			return Option::some($this->array[$firstKey]);
		}
	}

	public function headOrNull() {
		if ( empty($this->array) ) {
			return null;
		} else {
			$firstKey = array_key_first($this->array); //PHP 7.3+ or WP 5.9+
			return $this->array[$firstKey];
		}
	}

	/**
	 * @template A Accumulator type
	 * @template T Collection element type
	 *
	 * @param A $accumulator
	 * @param callable(A,T):A $callback
	 * @return A
	 */
	public function foldLeft($accumulator, callable $callback) {
		foreach ($this->array as $value) {
			$accumulator = $callback($accumulator, $value);
		}
		return $accumulator;
	}

	/**
	 * Computes the Cartesian product of this array and another array or iterable.
	 *
	 * Each element of the resulting array is a two-element array, where the first element
	 * comes from this array and the second element comes from the other array.
	 *
	 * @param array|ArrayWrapper|iterable $other
	 * @return ArrayWrapper
	 */
	public function cartesianProduct($other): self {
		$result = [];
		foreach ($this->array as $value1) {
			foreach ($other as $value2) {
				$result[] = [$value1, $value2];
			}
		}
		return new ArrayWrapper($result);
	}
}

function w($value): ArrayWrapper {
	if ( is_array($value) ) {
		return new ArrayWrapper($value);
	} else if ( is_iterable($value) ) {
		return new ArrayWrapper(iterator_to_array($value));
	} else {
		throw new \InvalidArgumentException('Value must be an array or iterable.');
	}
}