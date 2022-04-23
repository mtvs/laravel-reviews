<?php

namespace Reviews;

use Illuminate\Database\Eloquent\Model;
use Mtvs\EloquentApproval\Approvable;

class Review extends Model
{
	use Approvable;

	const RATING_MAX = 5;

	protected $dates = [
		'approval_at',
	];

	protected $fillable = [
		'rating', 'title', 'body','recommend',
		'reviewable_type', 'reviewable_id',
	];

	public function approvalRequired()
	{
		return [
			'title', 'body',
		];
	}

	public function user()
	{
		return $this->belongsTo(config('auth.providers.users.model'));
	}
}
