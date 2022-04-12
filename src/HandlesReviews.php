<?php

namespace Reviews;

trait HandlesReviews
{
	public function create($request)
	{
		// todo: Validate the input data
		
		auth()->user()->reviews()
			->create($request->all());
	}
}