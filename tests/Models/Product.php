<?php

namespace Mtvs\Reviews\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\Reviews\Reviewable;
use Mtvs\Reviews\Tests\Database\Factories\ProductFactory;

class Product extends Model
{
	use HasFactory, Reviewable, SoftDeletes;

	protected static function newFactory()
	{
		return new ProductFactory;
	}
}