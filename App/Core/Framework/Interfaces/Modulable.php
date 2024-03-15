<?php
namespace App\Core\Framework\Interfaces;

interface Modulable{

	public static function registerRoutes();

	public static function getFallback();
}