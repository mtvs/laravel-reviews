<?php

namespace Reviews;

use Illuminate\Http\Request;

trait IndexesReviews
{
	public function index(Request $request)
	{
		return config('reviews.model')::query()
			->where('reviewable_type', $request['reviewable_type'])
			->where('reviewable_id', $request['reviewable_id'])
			->paginate()->withQueryString();
	}
}
