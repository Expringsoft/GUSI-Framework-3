<?php
namespace App\Core\Framework\Interfaces;

interface Localizable
{
	public function setLocale(string $Locale): void;
	public function getLocale(): string;
}