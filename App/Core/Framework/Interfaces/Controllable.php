<?php
namespace App\Core\Framework\Interfaces;

interface Controllable
{
	public function __construct();
	public function Main();
	public function setView($_ViewURL = null, ?array $_Params = null);
	public function renderView();
	public static function getParentModule();
}