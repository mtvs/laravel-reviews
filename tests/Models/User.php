<?php

namespace Mtvs\Reviews\Tests\Models;

use Illuminate\Foundation\Auth\User as Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mtvs\Reviews\PerformsReviews;
use Mtvs\Reviews\Tests\Database\Factories\UserFactory;

class User extends Base 
{
	use HasFactory, PerformsReviews;

	protected static function newFactory()
	{
		return new UserFactory;;
	}
}
