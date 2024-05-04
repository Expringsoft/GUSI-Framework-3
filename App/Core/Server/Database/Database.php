<?php

namespace App\Core\Server\Database;

use App\Core\Exceptions\DatabaseException;
use App\Core\Framework\Classes\QueryBuilder;
use App\Core\Framework\Classes\Strings;
use App\Core\Framework\Structures\DatabaseResult;
use App\Core\Server\Actions;
use App\Core\Server\Logger;
use PDOException;
use PDO;
use PDOStatement;

class Database
{
	private PDO $connection;

	/**
	 * Database constructor.
	 * Creates a new database connection.
	 * @throws DatabaseException If the connection to the database fails.
	 */
	public function __construct()
	{
		$Host = "localhost";
		$User = "root";
		$Password = "";
		$Database = "gusi-framework";
		$Charset = "UTF8";
		$DSN = 'mysql:host=' . $Host . ';dbname=' . $Database . ';charset=' . $Charset;
		
		$options = [
			PDO::ATTR_EMULATE_PREPARES   => false,
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		];
		try {
			$this->connection = new PDO($DSN, $User, $Password, $options);
			return $this->connection;
		} catch (PDOException $e) {
			throw new DatabaseException($e->getMessage(), $e->errorInfo[1], $e->getPrevious());
		}
	}

	/**
	 * Executes a SELECT statement on the database.
	 *
	 * @param string $sql The SQL statement to execute.
	 * @param array $params An optional array of parameters to bind to the SQL statement.
	 * @return DatabaseResult The result of the SELECT operation including the success status, error code, message, number of affected rows, and the fetched data.
	 */
	public function select(string $sql, array $params = []): DatabaseResult
	{
		$Operation = new DatabaseResult(false, "-1", Actions::printLocalized(Strings::DATABASE_STATEMENT_NOT_PERFORMED), 0);
		try {
        	$stmt = $this->executeQuery($sql, $params);
			$Operation->fetch = $stmt->fetchAll();
			$Operation->rowCount = $stmt->rowCount();
			$Operation->result = true;
			$Operation->code = $stmt->errorCode();
			$Operation->message = Actions::printLocalized(Strings::DATABASE_SELECT_SUCCESS);
		} catch (PDOException $e) {
			$Operation->code = $e->errorInfo[1] ?? $e->getCode();
			$Operation->message = $e->getMessage();
			Logger::LogError($this::class, $Operation->message);
		} finally {
			return $Operation;
		}
	}

	/**
	 * Executes an SQL INSERT statement and returns the result.
	 *
	 * @param string $sql The SQL statement to execute.
	 * @param array $params The parameters to bind to the SQL statement.
	 * @return DatabaseResult The result of the INSERT operation including the success status, error code, message, and last inserted id.
	 */
	public function insert(string $sql, array $params = []): DatabaseResult
	{
		$Operation = new DatabaseResult(false, "-1", Actions::printLocalized(Strings::DATABASE_STATEMENT_NOT_PERFORMED), 0);
		try {
        	$stmt = $this->executeQuery($sql, $params);
			$Operation->rowCount = $stmt->rowCount();
			$Operation->result = true;
			$Operation->code = $stmt->errorCode();
			$Operation->message = Actions::printLocalized(Strings::DATABASE_INSERT_SUCCESS);
			$Operation->lastInsertId = $this->connection->lastInsertId();
		} catch (PDOException $e) {
			$Operation->code = $e->errorInfo[1] ?? $e->getCode();
			$Operation->message = $e->getMessage();
			Logger::LogError($this::class, $Operation->message);
		} finally {
			return $Operation;
		}
	}

	/**
	 * Executes an SQL update statement and returns the result.
	 *
	 * @param string $sql The SQL update statement to execute.
	 * @param array $params An optional array of parameters to bind to the SQL statement.
	 * @return DatabaseResult The result of the UPDATE operation including the success status, error code, message, and number of affected rows.
	 */
	public function update(string $sql, array $params = []): DatabaseResult
	{
		$Operation = new DatabaseResult(false, "-1", Actions::printLocalized(Strings::DATABASE_STATEMENT_NOT_PERFORMED), 0);
		try {
        	$stmt = $this->executeQuery($sql, $params);
			$Operation->rowCount = $stmt->rowCount();
			$Operation->result = true;
			$Operation->code = $stmt->errorCode();
			$Operation->message = Actions::printLocalized(Strings::DATABASE_UPDATE_SUCCESS);
		} catch (PDOException $e) {
			$Operation->code = $e->errorInfo[1] ?? $e->getCode();
			$Operation->message = $e->getMessage();
			Logger::LogError($this::class, $Operation->message);
		} finally {
			return $Operation;
		}
	}

	/**
	 * Deletes records from the database based on the provided SQL statement and parameters.
	 *
	 * @param string $sql The SQL statement to execute.
	 * @param array $params An optional array of parameters to bind to the SQL statement.
	 * @return DatabaseResult The result of the delete operation, including the success status, error code, message, and number of affected rows.
	 */
	public function delete(string $sql, array $params = []): DatabaseResult
	{
		$Operation = new DatabaseResult(false, "-1", Actions::printLocalized(Strings::DATABASE_STATEMENT_NOT_PERFORMED), 0);
		try {
        	$stmt = $this->executeQuery($sql, $params);
			$Operation->rowCount = $stmt->rowCount();
			$Operation->result = true;
			$Operation->code = $stmt->errorCode();
			$Operation->message = Actions::printLocalized(Strings::DATABASE_DELETE_SUCCESS);
		} catch (PDOException $e) {
			$Operation->code = $e->errorInfo[1] ?? $e->getCode();
			$Operation->message = $e->getMessage();
			Logger::LogError($this::class, $Operation->message);
		} finally {
			return $Operation;
		}
	}

	/**
	 * Executes a custom SQL statement and returns the result.
	 *
	 * @param string $sql The SQL statement to execute.
	 * @param array $params An optional array of parameters to bind to the SQL statement.
	 * @return PDOStatement The result of the custom SQL statement.
	 */
	public function executeQuery(string $sql, array $params = []): PDOStatement
	{
		$stmt = $this->connection->prepare($sql);
		$stmt->execute($params);
		return $stmt;
	}

	/**
	 * Executes a transaction with the given statements.
	 *
	 * @param array $TransactionSTMTs An bidimensional array of [statement, params] to be executed in the transaction.
	 * @return DatabaseResult The result of the transaction.
	 */
	public function transaction($TransactionSTMTs = []): DatabaseResult
	{
		$Operation = new DatabaseResult(false, "-1", Actions::printLocalized(Strings::DATABASE_STATEMENT_NOT_PERFORMED), 0);
		try {
			$AffectedRows = 0;
			$this->connection->beginTransaction();
			foreach ($TransactionSTMTs as $STMT) {
				$Query = $this->executeQuery($STMT[0], $STMT[1]);
				$AffectedRows += $Query->rowCount();
			}
			$this->connection->commit();
			$Operation->result = true;
			$Operation->rowCount = $AffectedRows;
			$Operation->code = "00000";
			$Operation->message = Actions::printLocalized(Strings::DATABASE_OPERATION_SUCCESS);
		} catch (PDOException $e) {
			$this->connection->rollBack();
			$Operation->code = $e->errorInfo[1] ?? $e->getCode();
			$Operation->message = $e->getMessage();
			Logger::LogError($this::class, $Operation->message);
		} finally {
			return $Operation;
		}
	}

	/**
	 * Executes a stored procedure with the given parameters.
	 *
	 * @param string $procedure The name of the stored procedure to execute.
	 * @param array $params An optional array of parameters to bind to the stored procedure.
	 * @return DatabaseResult The result of the stored procedure execution.
	 */
	public function executeProcedure(string $procedure, array $params = []): DatabaseResult
	{
		$Operation = new DatabaseResult(false, "-1", Actions::printLocalized(Strings::DATABASE_STATEMENT_NOT_PERFORMED), 0);
		try {
			$Query = $this->connection->prepare("CALL $procedure");
			$Query->execute($params);
			$Operation->fetch = $Query->fetchAll();
			$Operation->rowCount = $Query->rowCount();
			$Operation->result = true;
			$Operation->code = $Query->errorCode();
			$Operation->message = Actions::printLocalized(Strings::DATABASE_OPERATION_SUCCESS);
		} catch (PDOException $e) {
			$Operation->code = $e->errorInfo[1] ?? $e->getCode();
			$Operation->message = $e->getMessage();
			Logger::LogError($this::class, $Operation->message);
		} finally {
			return $Operation;
		}
	}

	/**
	 * Creates a new instance of the Database class.
	 *
	 * @return Database The new Database instance.
	 */
	public static function DB(): Database
	{
		return new Database();
	}

	/**
	 * Gets the database connection.
	 *
	 * @return mixed The database connection.
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Creates a new instance of the QueryBuilder class.
	 *
	 * @return QueryBuilder The new QueryBuilder instance.
	 */
	public static function perform()
	{
		return new QueryBuilder();
	}

	/**
	 * Closes the database connection.
	 */
	public function close()
	{
		$this->connection = null;
	}
}
