<?php

namespace Reviews\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Reviews\Reviewable;

class Product extends Model
{
	use Reviewable;
}