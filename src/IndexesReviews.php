<?php

namespace Reviews;

trait IndexesReviews
{
	public function index($routetype, $key)
	{
		foreach (config('reviews.reviewables') as $reviewable) {
			if ($reviewable['route_type'] == $routetype) {
				$class = $reviewable['class'];
				$keyName = $reviewable['key_name'];
				break;
			}
		}

		try {
			$reviewable = $class::where($keyName, $key)->firstOrFail();
		} catch (\Throwable) {
			abort(404);
		}

		// todo: eager-load the user relations on the reviews
		return response($reviewable->reviews()->paginate());
	}
}
