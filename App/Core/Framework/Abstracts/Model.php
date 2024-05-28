<?php
namespace App\Core\Framework\Abstracts;

use App\Core\Application\Configuration;
use App\Core\Framework\Classes\QueryBuilder;
use App\Core\Framework\Structures\DatabaseResult;
use App\Core\Exceptions\ModelException;
use App\Core\Framework\Interfaces\Modelable;
use App\Core\Framework\Structures\Collection;

abstract class Model implements Modelable
{
	public abstract static function table(): string;
	public abstract static function primaryKey(): string;

	/**
	 * Gets all records from the table of the model and return them as a Collection
	 * @return Collection
	 */
	public static function all(): Collection{
		$Query = new QueryBuilder();
		$Query->select(static::table(), ["*"]);
		$Result = $Query->execute();
		if (!$Result->result) {
			throw new ModelException($Result->message . " @" . static::table(), 678);
		}
		$Collection = new Collection();
		foreach ($Result->fetch as $Row) {
			$Model = new static();
			foreach ($Row as $Key => $Value) {
				$Model->$Key = $Value;
			}
			$Collection->addElement($Model);
		}
		return $Collection;
	}

	/**
	 * Gets records from the table of the model and return them as a QueryBuilder
	 * @param array $columns The columns to select from the table.
	 * @return QueryBuilder
	 */
	public static function get(array $columns = ["*"]): QueryBuilder
	{
		$Query = new QueryBuilder();
		$Query->select(static::table(), $columns);
		return $Query;	
	}

	/**
	 * Creates a new record in the table of the model
	 * @param array $data The data to insert into the table.
	 * @return DatabaseResult
	 */
	public static function create(array $data): DatabaseResult{
		$Query = new QueryBuilder();
		$Query->insert(static::table(), $data);
		return $Query->execute();
	}

	/**
	 * Updates records in the table of the model
	 * @param array $data The data to update in the table.
	 * @return QueryBuilder
	 */
	public static function update(array $data): QueryBuilder{
		$Query = new QueryBuilder();
		$Query->update(static::table(), $data);
		return $Query;
	}

	/**
	 * Deletes records from the table of the model
	 * @param bool $ignoreFrom Whether to ignore the FROM clause in the DELETE query, defaults to false.
	 * @return QueryBuilder
	 */
	public static function delete(bool $ignoreFrom = false): QueryBuilder{
		$Query = new QueryBuilder();
		$Query->delete(static::table(), $ignoreFrom);
		return $Query;
	}

	/**
	 * Sets a property of the model
	 * @param string $key The property to set.
	 * @param mixed $value The value to set the property to.
	 * @throws ModelException if the property does not exist in the model and Configuration::STRICT_MODELS is true.
	 */
	public function set(string $key, $value){
		if (!property_exists(static::class, $key) && Configuration::STRICT_MODELS) {
			throw new ModelException("Property $key does not exist in " . static::class, 678);
		}
		$this->$key = $value;
	}

	/**
	 * Saves the model to the database
	 * @return DatabaseResult
	 */
	public function save(): DatabaseResult
	{
		$Query = new QueryBuilder();
		$data = get_object_vars($this);
		$primaryKey = static::primaryKey();

		if ($primaryKey) {
			// If there is a primary key, use upsert logic
			$existingRecord = $Query->select(static::table(), [$primaryKey])->where($primaryKey, $this->$primaryKey)->execute();

			if ($existingRecord->result && count($existingRecord->fetch) > 0) {
				// If the record exists, update it
				$Query->update(static::table(), $data)->where($primaryKey, $this->$primaryKey);
			} else {
				// If the record does not exist, create it
				$Query->insert(static::table(), $data);
			}
		} else {
			// If there is no primary key, always insert a new record
			$Query->insert(static::table(), $data);
		}
		return $Query->execute();
	}

	/**
	 * Converts the model to a JSON string
	 * @return string
	 */
	public function __toString()
	{
		return json_encode($this);
	}
}