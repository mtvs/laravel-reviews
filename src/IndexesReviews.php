<?php

namespace Reviews;

trait IndexesReviews
{
	public function index($routetype, $key)
	{
		foreach (config('reviews.reviewables') as $reviewable) {
			if ((new $reviewable)->getRouteType() == $routetype) {
				$class = $reviewable;
				break;
			}
		}

		if (! isset($class)) {
			abort(404);
		}

		$reviewable = $class::findOrFail($key);

		$reviews = $reviewable->reviews()->with('user')->paginate();

		return response($reviews);
	}
}
