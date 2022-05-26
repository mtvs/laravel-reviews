<?php

namespace Reviews\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Reviews\ReviewConcerns;
use Reviews\Tests\Database\Factories\ReviewFactory;

class Review extends Model
{
	use HasFactory, ReviewConcerns;

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
}