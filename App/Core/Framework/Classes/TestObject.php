<?php
namespace App\Core\Framework\Classes;

use App\Core\Framework\Interfaces\Expectable;
use App\Core\Framework\Structures\Operation;

/**
 * Class TestObject
 * 
 * This class represents a test object that implements the Expectable interface.
 */
class TestObject implements Expectable{
	private $object;
	private const TEST_DESCRIPTION = 'Completed.';

	/**
	 * TestObject constructor.
	 * 
	 * @param mixed $object The object to be set as the test object.
	 */
	public function __construct($object = null)
	{
		$this->object = $object;
	}

	/**
	 * Get the test object.
	 * 
	 * @return mixed The test object.
	 */
	public function get(): mixed
	{
		return $this->object;
	}

	/**
	 * Set the test object.
	 * 
	 * @param mixed $object The object to be set as the test object.
	 * @return void
	 */
	public function set($object): void
	{
		$this->object = $object;
	}

	/**
	 * Test the test object to be equal to the expected value.
	 * 
	 * @param mixed $expected The expected value.
	 * @return Operation The result of the test.
	 */
	public function toBe($expected): Operation
	{
		return new Operation($this->object === $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Test the test object to be not equal to the expected value.
	 * 
	 * @param mixed $expected The expected value.
	 * @return Operation The result of the test.
	 */
	public function notToBe($expected): Operation
	{
		return new Operation($this->object !== $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Test the test object to be strictly equal to the expected value.
	 * 
	 * @param mixed $expected The expected value.
	 * @return Operation The result of the test.
	 */
	public function toBeStrictly($expected): Operation
	{
		return new Operation($this->object === $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Test the test object to be strictly not equal to the expected value.
	 * 
	 * @param mixed $expected The expected value.
	 * @return Operation The result of the test.
	 */
	public function notToBeStrictly($expected): Operation
	{
		return new Operation($this->object !== $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object is of a specific type.
	 *
	 * @param mixed $expected The expected type of the object.
	 * @return Operation An Operation object representing the result of the type check.
	 */
	public function toBeType($expected): Operation
	{
		return new Operation(gettype($this->object) === $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object is an instance of the expected class or interface.
	 *
	 * @param mixed $expected The expected class or interface.
	 * @return Operation An Operation object representing the result of the check.
	 */
	public function toBeInstanceOf($expected): Operation
	{
		return new Operation(
			$this->object instanceof $expected,
			sprintf(self::TEST_DESCRIPTION, __FUNCTION__),
			['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]
		);
	}

	/**
	 * Checks if the object is numeric.
	 *
	 * @return Operation The result of the operation.
	 */
	public function toBeNumeric(): Operation
	{
		return new Operation(is_numeric($this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the value of the object is numeric and greater than the expected value.
	 *
	 * @param float|int $expected The expected value to compare against.
	 * @return Operation The result of the operation.
	 */
	public function toBeNumericGreaterThan(float|int $expected): Operation
	{
		return new Operation(
			$this->object > $expected,
			sprintf(self::TEST_DESCRIPTION, __FUNCTION__),
			['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]
		);
	}

	/**
	 * Checks if the value of the object is numeric and less than the expected value.
	 *
	 * @param float|int $expected The expected value to compare against.
	 * @return Operation The result of the operation.
	 */
	public function toBeNumericLessThan(float|int $expected): Operation
	{
		return new Operation($this->object < $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the value of the object is numeric and greater than or equal to the expected value.
	 *
	 * @param float|int $expected The expected value to compare against.
	 * @return Operation The result of the operation.
	 */
	public function toBeNumericGreaterThanOrEqual(float|int $expected): Operation
	{
		return new Operation($this->object >= $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the value of the object is numeric and less than or equal to the expected value.
	 *
	 * @param float|int $expected The expected value to compare against.
	 * @return Operation The result of the operation.
	 */
	public function toBeNumericLessThanOrEqual(float|int $expected): Operation
	{
		return new Operation($this->object <= $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the length of the object is greater than the expected length.
	 *
	 * @param int $expected The expected length.
	 * @return Operation An Operation object representing the result of the comparison.
	 */
	public function toBeLongerThan(int $expected): Operation
	{
		return new Operation(strlen($this->object) > $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the length of the object is shorter than the expected length.
	 *
	 * @param int $expected The expected length.
	 * @return Operation The result of the operation.
	 */
	public function toBeShorterThan(int $expected): Operation
	{
		return new Operation(strlen($this->object) < $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the object is empty.
	 *
	 * @return Operation The result of the operation.
	 */
	public function toBeEmpty(): Operation
	{
		return new Operation(empty($this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the object is falsy.
	 *
	 * @return Operation The result of the operation.
	 */
	public function toBeFalsy(): Operation
	{
		return new Operation(!$this->object, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the object is truthy.
	 *
	 * @return Operation The result of the operation.
	 */
	public function toBeTruthy(): Operation
	{
		return new Operation(!!$this->object, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the object is null.
	 *
	 * @return Operation The result of the operation.
	 */
	public function toBeNull(): Operation
	{
		return new Operation(is_null($this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object is undefined.
	 *
	 * @return Operation
	 */
	public function toBeUndefined(): Operation
	{
		return new Operation(!isset($this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object is defined.
	 *
	 * @return Operation
	 */
	public function toBeDefined(): Operation
	{
		return new Operation(isset($this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object is an array.
	 *
	 * @return Operation
	 */
	public function toBeArray(): Operation
	{
		return new Operation(is_array($this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object has a specific key.
	 *
	 * @param mixed $key The key to check.
	 * @return Operation
	 */
	public function toHaveKey($key): Operation
	{
		return new Operation(array_key_exists($key, $this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object has all the specified keys.
	 *
	 * @param array $keys The keys to check.
	 * @return Operation
	 */
	public function toHaveKeys(array $keys): Operation
	{
		foreach ($keys as $key) {
			if (!array_key_exists($key, $this->object)) {
				return new Operation(false, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
			}
		}
		return new Operation(true, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object has a specific property.
	 *
	 * @param mixed $property The property to check.
	 * @return Operation
	 */
	public function toHaveProperty($property): Operation
	{
		return new Operation(property_exists($this->object, $property), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object has all the specified properties.
	 *
	 * @param array $properties The properties to check.
	 * @return Operation
	 */
	public function toHaveProperties(array $properties): Operation
	{
		foreach ($properties as $property) {
			if (!property_exists($this->object, $property)) {
				return new Operation(false, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
			}
		}
		return new Operation(true, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object has a specific method.
	 *
	 * @param mixed $method The method to check.
	 * @return Operation
	 */
	public function toHaveMethod($method): Operation
	{
		return new Operation(method_exists($this->object, $method), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object has a specific length.
	 *
	 * @param int $expected The expected length.
	 * @return Operation
	 */
	public function toHaveLength(int $expected): Operation
	{
		return new Operation(count($this->object) === $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the length of the object is greater than the expected length.
	 *
	 * @param int $expected The expected length.
	 * @return Operation The result of the operation.
	 */
	public function toHaveLengthGreaterThan(int $expected): Operation
	{
		return new Operation(count($this->object) > $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the length of the object is less than the expected length.
	 *
	 * @param int $expected The expected length.
	 * @return Operation The result of the operation.
	 */
	public function toHaveLengthLessThan(int $expected): Operation
	{
		return new Operation(count($this->object) < $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the length of the object is greater than or equal to the expected length.
	 *
	 * @param int $expected The expected length.
	 * @return Operation The result of the operation.
	 */
	public function toHaveLengthGreaterThanOrEqual(int $expected): Operation
	{
		return new Operation(count($this->object) >= $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the length of the object is less than or equal to the expected length.
	 *
	 * @param int $expected The expected length.
	 * @return Operation The result of the operation.
	 */
	public function toHaveLengthLessThanOrEqual(int $expected): Operation
	{
		return new Operation(count($this->object) <= $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the object matches the given regular expression.
	 *
	 * @param mixed $regex The regular expression to match.
	 * @return Operation The result of the operation.
	 */
	public function toMatch($regex): Operation
	{
		return new Operation(preg_match($regex, $this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the object contains the given item.
	 *
	 * @param mixed $item The item to check for.
	 * @return Operation The result of the operation.
	 */
	public function toContain($item): Operation
	{
		return new Operation(in_array($item, $this->object), sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Checks if the object has returned the given value.
	 *
	 * @param mixed $value The value to check for.
	 * @return Operation The result of the operation.
	 */
	public function toHaveReturned($value): Operation
	{
		return new Operation($this->object === $value, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}
	
	/**
	 * Check if the object has thrown an exception.
	 *
	 * @return Operation An Operation object representing the result of the check.
	 */
	public function toHaveThrown(): Operation
	{
		return new Operation($this->object instanceof \Throwable, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object has thrown an exception with the expected message.
	 *
	 * @param mixed $expected The expected exception message.
	 * @return Operation An Operation object representing the result of the check.
	 */
	public function toHaveThrownWith($expected): Operation
	{
		return new Operation($this->object->getMessage() === $expected, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

	/**
	 * Check if the object is an instance of any of the expected classes.
	 *
	 * @param array $expected An array of expected class names.
	 * @return Operation An Operation object representing the result of the check.
	 */
	public function toBeInstanceOfAny(array $expected): Operation
	{
		foreach ($expected as $expect) {
			if ($this->object instanceof $expect) {
				return new Operation(true, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
			}
		}
		return new Operation(false, sprintf(self::TEST_DESCRIPTION, __FUNCTION__), ['value' => $this->get(), 'type' => gettype($this->object), 'condition' => __FUNCTION__]);
	}

}