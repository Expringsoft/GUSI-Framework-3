<?php

namespace App\Core\Framework\Abstracts;

use App\Core\Application\Configuration;
use App\Core\Framework\Enumerables\Channels;
use App\Core\Server\Actions;
use App\Core\Framework\Enumerables\RenderOptions;
use App\Core\Framework\Interfaces\Controllable;
use App\Core\Framework\Abstracts\Channel;
use App\Core\Exceptions\ViewException;
use App\Core\Server\Logger;
use InvalidArgumentException;
use PDOException;

abstract class Controller extends Channel implements Controllable
{
	private $View;
	private $Params;

	/**
	 * Controller constructor.
	 *
	 * @param string $Method The name of the method to be executed. Default is 'Main'.
	 * @param array $args An array of arguments to be passed to the method.
	 */
	public function __construct(string $Method = 'Main', $args = [])
	{
		$RenderOption = RenderOptions::DEFAULT;
		try {
			$this->$Method(...$args);
		} catch (ViewException $ve) {
			$RenderOption = RenderOptions::ERROR;
			$Message = $ve->getMessage() . ".\nStack:\n" . $ve->getTraceAsString() . ".\n■ Line: " . $ve->getLine() . ', on: ' . $ve->getFile();
			$thCode = $ve->getCode();
			if (Configuration::LOG_ERRORS) {
				Logger::LogError(self::class, "[{$thCode}]: {$Message}");
			}
		} catch (\Throwable $th) {
			$RenderOption = RenderOptions::ERROR;
			$Message = $th->getMessage() . ".\nStack:\n" . $th->getTraceAsString() . ".\n■ Line: " . $th->getLine() . ', on: ' . $th->getFile();
			$thCode = null;
			if ($th instanceof PDOException) {
				$thCode = $th->errorInfo[1];
			} else {
				$thCode = $th->getCode();
			}
			if (Configuration::LOG_ERRORS) {
				Logger::LogError(self::class, "[{$thCode}]: {$Message}");
			}
		} finally {
			$this->renderView($RenderOption);
		}
	}

	abstract static function getParentModule(): string;

	abstract static function getModuleChannel(): Channels;

	public abstract function Main(...$args);

	/**
	 * Sets the view to be rendered.
	 *
	 * @param string $_ViewURL The URL of the view to be rendered.
	 * @param array|null $_Params The parameters to be passed to the view.
	 * @return void
	 */
	public function setView($_ViewURL = null, ?array $_Params = null)
	{
		$this->View = $_ViewURL;
		$this->Params = $_Params;
	}

	/**
	 * Renders the view based on the provided RenderOptions.
	 *
	 * @param RenderOptions $RenderOption The RenderOptions to determine how the view should be rendered.
	 * @param int $code The HTTP status code to be used in case of an error.
	 * @return void
	 * @throws InvalidArgumentException If an invalid RenderOption is provided.
	 */
	public function renderView(RenderOptions $RenderOption = RenderOptions::DEFAULT, int $code = 500)
	{
		switch ($RenderOption) {
			case RenderOptions::DEFAULT:
				if ($this->View) {
					Actions::requireView($this->View, $this->Params);
				}
				break;
			case RenderOptions::NOT_FOUND:
				Actions::renderNotFound();
				break;
			case RenderOptions::ERROR:
				Actions::renderError($code);
				break;
			default:
				throw new InvalidArgumentException('Invalid Render Option', 1);
				break;
		}
	}

	/**
	 * Sets a header to be sent with the response.
	 *
	 * @param string $name The name of the header.
	 * @param string $value The value of the header.
	 * @return void
	 */
	public function setHeader($name, $value)
	{
		header($name . ': ' . $value);
	}
}
