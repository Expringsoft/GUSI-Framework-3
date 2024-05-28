<?php
namespace App\Core\Framework\Interfaces;

use App\Core\Framework\Classes\QueryBuilder;
use App\Core\Framework\Structures\Collection;
use App\Core\Framework\Structures\DatabaseResult;

interface Modelable{
	public static function table(): string;
	public static function primaryKey(): string;
	public static function all(): Collection;
	public static function get(array $columns = ["*"]): QueryBuilder;
	public static function create(array $data): DatabaseResult;
	public static function update(array $data): QueryBuilder;
	public static function delete(): QueryBuilder;
	public function set(string $key, $value);
	public function save(): DatabaseResult;
}