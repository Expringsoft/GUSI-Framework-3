<?php

namespace App\Core\Framework\Abstracts;

use App\Core\Application\Configuration;
use App\Core\Framework\Enumerables\Channels;
use App\Core\Server\Actions;
use App\Core\Framework\Enumerables\RenderOptions;
use App\Core\Framework\Interfaces\Controllable;
use App\Core\Framework\Abstracts\Channel;
use App\Core\Server\Logger;
use InvalidArgumentException;
use PDOException;
abstract class Controller extends Channel implements Controllable
{
	private $View;
	private $Params;

	public function __construct(string $Method = 'Main', $args = [])
	{
		$RenderOption = RenderOptions::DEFAULT;
		try {
			$this->$Method(...$args);
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

	public function setView($_ViewURL = null, ?array $_Params = null)
	{
		$this->View = $_ViewURL;
		$this->Params = $_Params;
	}

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
}
