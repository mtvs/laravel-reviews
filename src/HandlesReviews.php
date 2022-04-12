<?php

namespace Reviews;

trait HandlesReviews
{
	public function create($request)
	{
		// todo: Validate the input data

		$review = auth()->user()->reviews()
			->create($request->all());

		return response($review);
	}
}