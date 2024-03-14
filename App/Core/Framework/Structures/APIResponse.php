<?php
namespace App\Core\Framework\Structures;

class APIResponse{
	/**
	 * HTTP Response code
	 *
	 * @var int
	 */
	public int $code;

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
	 * APIResponse constructor.
	 *
	 * @param int $code The HTTP Response code in response body.
	 * @param string $message The message associated with the operation.
	 * @param array $data The data associated with the operation.
	 */
	public function __construct(int $code, string $message, array $data = [])
	{
		$this->code = $code;
		$this->message = $message;
		$this->data = $data;
	}

	/**
	 * Converts the APIResponse object to JSON format.
	 *
	 * @return string The JSON representation of the APIResponse object.
	 */
	public function __toJSON()
	{
		return json_encode($this);
	}
}