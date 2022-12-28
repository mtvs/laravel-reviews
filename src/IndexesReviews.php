<?php

namespace Mtvs\Reviews;

use Illuminate\Http\Request;

trait IndexesReviews
{
	public function index(Request $request)
	{
		$reviewableType = $request['reviewable_type'];
		$reviewableId = $request['reviewable_id'];

		if (
			! in_array($reviewableType, config('reviews.reviewables'))
			|| ! $reviewable = $reviewableType::find($reviewableId)
		) {
			return [];
		}

		$reviews = $reviewable->reviews()
			->paginate(config('reviews.per_page'))
			->withQueryString();

		return $reviews;
	}
}
