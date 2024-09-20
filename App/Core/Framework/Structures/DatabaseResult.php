<?php
namespace App\Core\Framework\Structures;

use App\Core\Framework\Structures\Collection;

class DatabaseResult
{
	/**
	 * @var bool The result of the database operation.
	 */
	public bool $result;

	/**
	 * @var string The code associated with the database operation.
	 */
	public string $code;

	/**
	 * @var string The message associated with the database operation.
	 */
	public string $message;

	/**
	 * @var int The number of rows affected by the database operation.
	 */
	public int $rowCount;

	/**
	 * @var array The fetched data from the database operation.
	 */
	public array $fetch;

	/**
	 * @var int The last inserted id from the database operation.
	 * If the operation is not an insert operation, this value will be -1.
	 */
	public int $lastInsertId;

	/**
	 * DatabaseResult constructor.
	 *
	 * @param bool $result The result of the database operation.
	 * @param string $code The code associated with the database operation.
	 * @param string $message The message associated with the database operation.
	 * @param int $rowCount The number of rows affected by the database operation.
	 * @param array $fetch The fetched data from the database operation.
	 */
	public function __construct(bool $result, string $code, string $message, int $rowCount, array $fetch = [], int $lastInsertId = -1)
	{
		$this->result = $result;
		$this->code = $code;
		$this->message = $message;
		$this->rowCount = $rowCount;
		$this->fetch = $fetch;
		$this->lastInsertId = $lastInsertId;
	}

	/**
	 * Converts the DatabaseResult object to JSON.
	 *
	 * @return string The JSON representation of the DatabaseResult object.
	 */
	public function __toJSON()
	{
		return json_encode($this);
	}

	/**
	 * Converts the DatabaseResult object to a string.
	 *
	 * @return string The message associated with the DatabaseResult object.
	 */
	public function __toString()
	{
		return $this->message;
	}

	/**
	 * Converts the DatabaseResult object to an array.
	 *
	 * @return array The array representation of the DatabaseResult object.
	 */
	public function __toArray()
	{
		return [
			"result" => $this->result,
			"code" => $this->code,
			"message" => $this->message,
			"rowCount" => $this->rowCount,
			"fetch" => $this->fetch
		];
	}

	/**
	 * Converts the DatabaseResult object to a Collection.
	 *
	 * @return Collection The Collection representation of the DatabaseResult object.
	 */
	public function __toCollection(){
		$Collection = new Collection();
		foreach ($this->fetch as $Row) {
			$Collection->addElement($Row);
		}
		return $Collection;
	}

	/**
	 * Converts the DatabaseResult object to a Collection of models.
	 *
	 * @param string $modelClass The class of the model to convert the data to.
	 * @return Collection Collection of models.
	 */
	public function __toCollectionModel(string $modelClass): Collection
	{
		$Collection = new Collection();
		foreach ($this->fetch as $Row) {
			$Model = new $modelClass();
			foreach ($Row as $Key => $Value) {
				$Model->$Key = $Value;
			}
			$Collection->addElement($Model);
		}
		return $Collection;
	}

	/**
	 * Converts the DatabaseResult object to an array of models.
	 *
	 * @param string $modelClass The class of the model to convert the data to.
	 * @return array Array of models.
	 */
	public function __toArrayModel(string $modelClass): array
	{
		$Models = [];
		foreach ($this->fetch as $Row) {
			$Model = new $modelClass();
			foreach ($Row as $Key => $Value) {
				$Model->$Key = $Value;
			}
			$Models[] = $Model;
		}
		return $Models;
	}
}