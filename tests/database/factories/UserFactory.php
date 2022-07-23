<?php

namespace Mtvs\Reviews\Tests\Database\Factories;

use Orchestra\Testbench\Factories\UserFactory as Base;
use Mtvs\Reviews\Tests\Models\User;

class UserFactory extends Base
{
	protected $model = User::class;
}