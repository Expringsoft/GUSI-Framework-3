<?php
namespace App\Core\Framework\Abstracts;

use App\Core\Framework\Abstracts\Channel;
use App\Core\Framework\Interfaces\Modulable;

abstract class Module extends Channel implements Modulable
{
	abstract static function getFallback();
}