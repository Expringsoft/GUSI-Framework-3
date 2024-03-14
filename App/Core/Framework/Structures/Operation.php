<?php

namespace App\Core\Framework\Structures;

class Operation
{
	/**
	 * Represents the result of an operation.
	 *
	 * @var bool
	 */
	public bool $result;

	/**
	 * Represents the message associated with the operation.
	 *
	 * @var string
	 */
	public string $message;

	/**
	 * Represents the data associated with the operation.
	 *
	 * @var array
	 */
	public array $data;

	/**
	 * Operation constructor.
	 *
	 * @param bool $result The result of the operation.
	 * @param string $message The message associated with the operation.
	 * @param array $data The data associated with the operation.
	 */
	public function __construct(bool $result, string $message, array $data = [])
	{
		$this->result = $result;
		$this->message = $message;
		$this->data = $data;
	}

	/**
	 * Converts the Operation object to JSON format.
	 *
	 * @return string The JSON representation of the Operation object.
	 */
	public function __toJSON()
	{
		return json_encode($this);
	}
}
