<?php

namespace App\Core\Framework\Classes;

use App\Core\Framework\Enumerables\QueryTypes;
use App\Core\Framework\Structures\DatabaseResult;
use App\Core\Server\Database\Database;
use BadFunctionCallException;
use LogicException;

/**
 * Class QueryBuilder
 * 
 * Represents a query builder for constructing SQL queries.
 */
class QueryBuilder
{
	private $queryParts = [];
	private $params = [];
	private $queryType;
	private $ignoreFrom = false;

	public const LIKE_ENDS_WITH = "%@"; // Starts with
	public const LIKE_STARTS_WITH = "@%"; // Ends with
	public const LIKE_CONTAINS = "%@%"; // Contains

	/**
	 * QueryBuilder constructor.
	 * 
	 * Initializes the query parts and parameters.
	 */
	public function __construct()
	{
		$this->setDefaults();
	}

	/**
	 * Sets the default values for the query parts and parameters.
	 */
	public function setDefaults()
	{
		$this->queryParts = [
			'select' => '',
			'from' => '',
			'join' => [],
			'where' => [],
			'group' => '',
			'having' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'insert' => '',
			'update' => '',
			'delete' => '',
		];
		$this->params = [];
		$this->queryType = null;
		$this->ignoreFrom = false;
	}

	/**
	 * Sets the query type to SELECT and constructs the SELECT query.
	 *
	 * @param string $table The name of the table to select from.
	 * @param array $columns An array of column names to select.
	 * @return $this The QueryBuilder instance.
	 */
	public function select(string $table, array $columns)
	{
		$this->queryType = QueryTypes::SELECT;
		$this->queryParts['select'] = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $table;
		return $this;
	}

	/**
	 * Sets the query type to INSERT and constructs the INSERT query.
	 *
	 * @param string $table The name of the table to insert into.
	 * @param array $data An associative array of column names and values to insert.
	 * @return $this The QueryBuilder instance.
	 */
	public function insert(string $table, array $data)
	{
		$this->queryType = QueryTypes::INSERT;
		$columns = implode(', ', array_keys($data));
		$placeholders = implode(', ', array_fill(0, count($data), '?'));
		$this->queryParts['insert'] = "INSERT INTO $table ($columns) VALUES ($placeholders)";
		$this->params = array_values($data);
		return $this;
	}

	/**
	 * Sets the query type to UPDATE and constructs the UPDATE query.
	 *
	 * @param string $table The name of the table to update.
	 * @param array $data An associative array of column names and values to update.
	 * @return $this The QueryBuilder instance.
	 */
	public function update(string $table, array $data)
	{
		$this->queryType = QueryTypes::UPDATE;
		$set = [];
		foreach ($data as $column => $value) {
			$set[] = "$column = ?";
			$this->params[] = $value;
		}
		$set = implode(', ', $set);
		$this->queryParts['update'] = "UPDATE $table SET $set";
		return $this;
	}

	/**
	 * Sets the query type to DELETE and constructs the DELETE query.
	 *
	 * @param string $table The name of the table to delete from.
	 * @return $this The QueryBuilder instance.
	 */
	public function delete(string $table, bool $ignoreFrom = false)
	{
		$this->queryType = QueryTypes::DELETE;
		$this->queryParts['delete'] = "DELETE FROM $table";
		$this->ignoreFrom = $ignoreFrom;
		return $this;
	}

	/**
	 * Adds a JOIN clause to the query.
	 *
	 * @param string $table The name of the table to join.
	 * @param string $condition The join condition.
	 * @param string $type The type of join (INNER, LEFT, RIGHT).
	 * @return $this The QueryBuilder instance.
	 */
	public function join(string $table, $condition, $type = 'INNER')
	{
		$this->queryParts['join'][] = " $type JOIN $table ON $condition";
		return $this;
	}

	/**
	 * Adds a WHERE clause to the query.
	 *
	 * @param string $column The column to filter by.
	 * @param mixed $value The value to filter by.
	 * @param string $condition The condition to use (AND, OR).
	 * @param string $operator The operator to use (=, <, >, etc.).
	 * @return $this The QueryBuilder instance.
	 */
	public function where(string $column, $value, $condition = 'AND', $operator = '=')
	{
		$this->queryParts['where'][] = ($this->queryParts['where'] ? " $condition " : " WHERE ") . "$column $operator ?";
		$this->params[] = $value;
		return $this;
	}

	/**
	 * Adds a WHERE clause to the query using the LIKE operator.
	 *
	 * @param string $column The column to filter by.
	 * @param mixed $value The value to filter by.
	 * @param string $operator The condition to use (AND, OR).
	 * @param string $likeType The type of LIKE operation to use.
	 * @return $this The QueryBuilder instance.
	 */
	public function whereLike(string $column, $value, $operator = 'AND', $likeType = self::LIKE_CONTAINS)
	{
		return $this->where($column, str_replace("@", $value, $likeType), $operator, 'LIKE');
	}

	/**
	 * Adds a WHERE clause to the query using the NOT operator.
	 *
	 * @param string $column The column to filter by.
	 * @param mixed $value The value to filter by.
	 * @return $this The QueryBuilder instance.
	 */
	public function not(string $column, $value)
	{
		return $this->where($column, $value, 'AND NOT');
	}

	/**
	 * Adds a WHERE clause to the query using the OR operator.
	 *
	 * @param string $column The column to filter by.
	 * @param mixed $value The value to filter by.
	 * @param string $operator The operator to use (=, <, >, etc.).
	 * @return $this The QueryBuilder instance.
	 */
	public function orWhere(string $column, $value, $operator = '=')
	{
		return $this->where($column, $value, 'OR', $operator);
	}

	/**
	 * Adds a GROUP BY clause to the query.
	 *
	 * @param array $columns The columns to group by.
	 * @return $this The QueryBuilder instance.
	 */
	public function groupBy(array $columns)
	{
		$this->queryParts['group'] = 'GROUP BY ' . implode(', ', $columns);
		return $this;
	}

	/**
	 * Adds a HAVING clause to the query.
	 *
	 * @param string $condition The condition for the HAVING clause.
	 * @return $this The QueryBuilder instance.
	 */
	public function having(string $condition)
	{
		$this->queryParts['having'] = 'HAVING ' . $condition;
		return $this;
	}

	/**
	 * Adds an ORDER BY clause to the query.
	 *
	 * @param array $columns An array of column names to order by.
	 * @param string $direction The direction to order by (ASC, DESC).
	 * @return $this The QueryBuilder instance.
	 */
	public function orderBy(array $columns, $direction = 'ASC')
	{
		$column = implode(', ', $columns);
		$this->queryParts['order'] = " ORDER BY $column $direction";
		return $this;
	}

	/**
	 * Adds a LIMIT clause to the query.
	 *
	 * @param int $limit The number of rows to limit the query to.
	 * @return $this The QueryBuilder instance.
	 */
	public function limit(int $limit)
	{
		$this->queryParts['limit'] = " LIMIT $limit";
		return $this;
	}

	/**
	 * Adds an OFFSET clause to the query.
	 *
	 * @param int $offset The number of rows to offset the query by.
	 * @return $this The QueryBuilder instance.
	 */
	public function offset(int $offset)
	{
		$this->queryParts['offset'] = " OFFSET $offset";
		return $this;
	}

	/**
	 * Executes the query and returns the result.
	 *
	 * @return DatabaseResult The result of the query.
	 */
	public function execute(): DatabaseResult
	{
		$database = Database::DB();
		$result = null;
		
		switch ($this->queryType) {
			case QueryTypes::SELECT:
				$result = $database->select($this->getQuery(), $this->getParams());
				break;
			case QueryTypes::INSERT:
				$result = $database->insert($this->getQuery(), $this->getParams());
				break;
			case QueryTypes::UPDATE:
				$result = $database->update($this->getQuery(), $this->getParams());
				break;
			case QueryTypes::DELETE:
				if (!$this->ignoreFrom && count($this->queryParts['where']) == 0) {
					throw new LogicException("DELETE query without WHERE clause is not allowed unless explicitly ignored.");
				}
				$result = $database->delete($this->getQuery(), $this->getParams());
				break;
			default:
				throw new BadFunctionCallException("Unsupported query type");
				break;
		}

		$this->setDefaults();
		return $result;
	}

	/**
	 * Gets the query string.
	 *
	 * @return string The query string.
	 */
	public function getQuery(): string
	{
		$query = '';
		switch ($this->queryType) {
			case QueryTypes::SELECT:
				$query .= $this->queryParts['select'] . $this->addToQuery($this->queryParts['from']) . $this->addToQuery($this->queryParts['join']) . $this->addToQuery($this->queryParts['where']) . $this->addToQuery($this->queryParts['group']) . $this->addToQuery($this->queryParts['having']) . $this->queryParts['order'] . $this->queryParts['limit'] . $this->queryParts['offset'];
				break;
			case QueryTypes::INSERT:
				$query .= $this->queryParts['insert'];
				break;
			case QueryTypes::UPDATE:
				$query .= $this->queryParts['update'] . ' ' . implode(' ', $this->queryParts['where']);
				break;
			case QueryTypes::DELETE:
				$query .= $this->queryParts['delete'] . ' ' . implode(' ', $this->queryParts['where']);
				break;
		}
		return $query;
	}

	/**
	 * Gets the query parameters.
	 *
	 * @return array The query parameters.
	 */
	public function getParams()
	{
		return $this->params;
	}

	function addToQuery($value): string
	{
		if (is_string($value) && $value !== '') {
			return ' ' . $value;
		} elseif (is_array($value) && count($value) > 0) {
			return ' ' . implode(' ', $value);
		} else {
			return '';
		}
	}
}
