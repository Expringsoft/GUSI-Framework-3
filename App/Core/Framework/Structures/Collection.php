<?php

namespace App\Core\Framework\Structures;

use ArrayObject;
use App\Core\Exceptions\AppException;
use App\Core\Framework\Interfaces\Countable;

/**
 * Collections: Class for performing operations with associative arrays.
 */
class Collection extends ArrayObject implements Countable
{
	private $collection = array();

	public const INDEX_NOT_FOUND = "Index out of bounds or not existing in the collection.";
	public const KEY_NOT_FOUND = "Key not existing in the collection.";
	public const DUPLICATED_KEY = "An element with the provided key already exists.";

	/**
	 * Class instance creation.
	 *
	 * @param array     $fromArray   Optional, if an array is provided, it will operate based on it.
	 */
	public function __construct($fromArray = array())
	{
		$this->collection = $fromArray;
	}

	/**
	 * Adds an element to the collection.
	 *
	 * @param string    $key          Key where the value will be stored.
	 * @param mixed     $element      The element to add.
	 *
	 * @return array    The array with the added element.
	 */
	public function addElement($element, string $key = null): array
	{
		if ($key == null) {
			$this->collection[] = $element;
			return $this->collection;
		} else {
			if (!isset($this->collection[$key])) {
				$this->collection[$key] = $element;
				return $this->collection;
			} else {
				throw new AppException($this::DUPLICATED_KEY, 304);
			}
		}
	}

	/**
	 * Replaces an existing element in the collection.
	 *
	 * @param string    $key          Key where the value will be stored.
	 * @param mixed     $element      The element to add.
	 *
	 * @return array    The array with the added element.
	 */
	public function replaceElementByKey(string $key, $element): array
	{
		if (isset($this->collection[$key])) {
			$this->collection[$key] = $element;
			return $this->collection;
		} else {
			throw new AppException($this::KEY_NOT_FOUND, 404);
		}
	}

	/**
	 * Removes an element from the collection.
	 *
	 * @param mixed     $key      Key of the element to remove.
	 *
	 * @return array    The array with the removed element.
	 */
	public function removeElement(string $key): array
	{
		if (!isset($this->collection[$key])) {
			unset($this->collection[$key]);
			return $this->collection;
		} else {
			throw new AppException($this::KEY_NOT_FOUND, 404);
		}
	}

	/**
	 * Gets an element from the collection.
	 *
	 * @param string    $key      The key of the element to get.
	 *
	 * @return mixed    The value of the element.
	 */
	public function getElement(string $key)
	{
		if (isset($this->toArray()[$key])) {
			return $this->collection[$key];
		} else {
			throw new AppException($this::KEY_NOT_FOUND, 404);
		}
	}

	/**
	 * Gets an element from the collection at the specified index.
	 *
	 * @param string    $key      The key of the element to get.
	 *
	 * @return mixed    The value of the element.
	 */
	public function elementAt(int $index)
	{
		if (isset($this->toArray()[$index])) {
			return $this->toArray()[$index];
		} else {
			throw new AppException($this::KEY_NOT_FOUND, 404);
		}
	}

	/**
	 * Gets the first element of the collection.
	 *
	 * @return mixed    The first element of the collection.
	 */
	public function firstElement()
	{
		return $this->elementAt(0);
	}

	/**
	 * Gets the last element of the collection.
	 *
	 * @return mixed    The last element of the collection.
	 */
	public function lastElement()
	{
		return $this->elementAt($this->count() - 1);
	}

	/**
	 * Gets the keys of the collection.
	 *
	 * @return array    An array containing the keys of the collection.
	 */
	public function getKeys(): array
	{
		return array_keys($this->collection);
	}

	/**
	 * Gets the count of elements in the collection.
	 *
	 * @return int  The total count.
	 */
	public function count(): int
	{
		return count($this->collection);
	}

	/**
	 * Returns a boolean if the key exists in the collection.
	 * 
	 * @param string    $key      The key of the element to get.
	 *
	 * @return bool  True if the key exists, otherwise false.
	 */
	public function keyExists(string $key): bool
	{
		return isset($this->collection[$key]);
	}

	/**
	 * Returns a boolean if the element exists in the collection.
	 *
	 * @param mixed    $element       The element to search in the collection.
	 * @param bool     $strict        (Optional) if a strict search is set. Default: false.
	 * 
	 * @return bool     true if the element exists, otherwise false.
	 */
	public function elementExists(mixed $element, bool $strict = false): bool
	{
		return in_array($element, $this->collection, $strict);
	}

	/**
	 * Removes all content from the collection.
	 */
	public function clear(): void
	{
		$this->collection = array();
	}

	/**
	 * Returns the array of this object.
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		return $this->collection;
	}

	/**
	 * Returns the JSON representation of the array of this object.
	 *
	 * @return string   The JSON, if the conversion fails it generates an exception.
	 */
	public function toJSON(): string
	{
		try {
			return json_encode($this->collection);
		} catch (\Throwable $th) {
			throw new AppException($th->getMessage(), $th->getCode(), $th->getPrevious());
		}
	}
}
