<?php

namespace Reviews\Tests\Models;

use Illuminate\Foundation\Auth\User as Base;
use Reviews\PerformsReviews;

class User extends Base 
{
	use PerformsReviews;
}