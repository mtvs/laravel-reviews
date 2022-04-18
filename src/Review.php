<?php

namespace Reviews;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
	const RATING_MAX = 5;

	protected $fillable = [
		'rating', 'title', 'body','recommend',
		'reviewable_type', 'reviewable_id',
	];

	public function user()
	{
		return $this->belongsTo(config('auth.providers.users.model'));
	}
}
