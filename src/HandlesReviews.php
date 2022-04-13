<?php

namespace Reviews;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HandlesReviews
{
	public function create($request)
	{
		// todo: Validate the input data

		$user = auth()->user();

		// Reject reviews for the non exesign reviewables
		try {
			$reviewable = $request['reviewable_type']::findOrFail(
				$request['reviewable_id']
			);
		} catch(ModelNotFoundException $e) {
			abort(422, "Unprocessable Entity");
		} catch(\Throwable $e) {
			abort(422, "Unprocessable Entity");
		}

		$review = $user->reviews()
			->create($request->all());

		return response($review);
	}
}