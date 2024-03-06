<?php
namespace App\Core\Framework\Interfaces;

interface Printable
{
	public function print(): void;

	public function println(): void;

	public function safePrint(): void;
}