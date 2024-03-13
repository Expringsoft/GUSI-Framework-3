<?php

namespace App\Core\Framework\Classes;

use App\Core\Application\Configuration;
use App\Core\Application\SharedConsts;
use App\Core\Framework\Classes\TestObject;
use App\Core\Framework\Structures\Operation;
use App\Core\Server\FileManager;

/**
 * Represents a unit test.
 */
class UnitTest
{
	/**
	 * The folder where the test files are located.
	 */
	private const TESTS_FOLDER = 'App/Tests/Results/';

	/**
	 * The extension for the test files.
	 */
	private const TESTS_EXTENSION = '.json';

	/**
	 * The message displayed when a test passes.
	 */
	private const MSG_RESULT_OK = 'Test passed.';

	/**
	 * The message displayed when a test fails.
	 * The placeholder %s will be replaced with the actual result.
	 */
	private const MSG_RESULT_FAIL = 'Test failed. Got: %s.';

	/**
	 * The message displayed when a test does not return an Operation object.
	 */
	private const MSG_RESULT_NO_OBJECT = 'Test did not return an Operation object.';

	/**
	 * The start time of the test.
	 */
	private $startTime;

	/**
	 * The end time of the test.
	 */
	private $endTime;

	/**
	 * The results of the test.
	 */
	private $testResults = [];

	/**
	 * The name of the test.
	 */
	private $testName;

	/**
	 * The file path of the test.
	 */
	private $testFile;

	/**
	 * The end of the filename.
	 */
	private $filenameEnd;


	/**
	 * Constructor for the UnitTest class.
	 *
	 * @param string|null $testName The name of the test. If not provided, 'UnnamedTest' will be used.
	 * @throws \Exception If unit tests are not allowed to run outside the local environment.
	 */
	public function __construct($testName = null)
	{
		if (!Configuration::LOCAL_ENVIRONMENT && !Configuration::ALLOW_TESTING_OUTSIDE_LOCAL) {
			throw new \Exception('Unit tests can only be run in local environment');
		}
		
		$this->filenameEnd = SharedConsts::UNDERSCORE .  date('Y-m-d') . SharedConsts::UNDERSCORE . time() . rand(1000,99999) . self::TESTS_EXTENSION;

		if ($testName) {
			$this->testName = $testName;
			$this->testFile = $testName . $this->filenameEnd;
		} else {
			$this->testName = 'UnnamedTest';
			$this->testFile = $this->testName . $this->filenameEnd;
		}
	}

	/**
	 * Starts the timer for the test.
	 */
	public function startTimer(): void
	{
		$this->startTime = microtime(true);
	}

	/**
	 * Ends the timer for the test.
	 */
	public function endTimer(): void
	{
		$this->endTime = microtime(true);
	}

	/**
	 * Gets the elapsed time for the test.
	 *
	 * @return float The elapsed time for the test.
	 */
	public function getElapsedTime(): float
	{
		return $this->endTime - $this->startTime;
	}

	/**
	 * Creates a new TestObject for the given subject.
	 *
	 * @param mixed $subject The subject of the test.
	 * @return TestObject The TestObject for the given subject.
	 */
	public function expect($subject): TestObject
	{
		return new TestObject($subject);
	}

	/**
	 * Runs a test with the given name and test function.
	 *
	 * @param string $name The name of the test.
	 * @param callable $test UnitTest instance.
	 */
	public function test(string $name, callable $test): void
	{
		$this->startTimer();
		$result = $test($this);
		$this->endTimer();
		$this->testResults[$name]['name'] = $name;
		$this->testResults[$name]['time'] = $this->getElapsedTime();
		if ($result instanceof Operation) {
			$this->testResults[$name]['message'] = $result->result ? $result->message . SharedConsts::SPACE . self::MSG_RESULT_OK : sprintf($result->message . SharedConsts::SPACE . self::MSG_RESULT_FAIL, $result->result . SharedConsts::SPACE . $result->data['type']);
			$this->testResults[$name]['condition'] = $result->data['condition'];
			$this->testResults[$name]['result'] = $result->result;
		} else {
			$this->testResults[$name]['message'] = self::MSG_RESULT_NO_OBJECT;
			$this->testResults[$name]['condition'] = 'No condition';
			$this->testResults[$name]['result'] = true;
		}
	}

	/**
	 * Gets the results of the test.
	 *
	 * @return array The results of the test.
	 */
	public function getResults(): array
	{
		return $this->testResults;
	}

	/**
	 * Saves the results of the test to a file.
	 */
	public function saveResults(): void
	{
		$testResults = [];
		foreach ($this->getResults() as $result) {
			$testResults[] = [
				'name' => $result['name'],
				'time' => $result['time'],
				'message' => $result['message'],
				'condition' => $result['condition'],
				'result' => $result['result']
			];
		}

		$totalTime = 0;
		foreach ($this->getResults() as $result) {
			$totalTime += $result['time'];
		}

		FileManager::appendToFile(self::TESTS_FOLDER . $this->testFile, json_encode(['generalTestName' => $this->testName, 'totalTime' => $totalTime, 'results' => $testResults]));
	}
}
