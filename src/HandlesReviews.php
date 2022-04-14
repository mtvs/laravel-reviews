<?php

namespace Reviews;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

trait HandlesReviews
{
	public function create(Request $request)
	{
		// todo: Validate the input data

		$user = auth()->user();

		// Reject the reviews for the non-existing reviewables
		try {
			$reviewable = $request['reviewable_type']::findOrFail(
				$request['reviewable_id']
			);
		} catch(ModelNotFoundException $e) {
			abort(422, "Unprocessable Entity");
		} catch(\Throwable $e) {
			abort(422, "Unprocessable Entity");
		}

		// Reject multiple reviews from a signle user
		if ($user->hasAlreadyReviewed($reviewable))
		{
			return response()->json([], 403);
		}

		$review = $user->reviews()
			->create($request->all());

		return response($review);
	}

	public function update($id, Request $request)
	{
		// todo: Validate the input data

		$user = auth()->user();

		if (! $review = $user->reviews()->find($id)) 
		{
			return response('', 404);
		}

		$review->update($request->all());

		return response()->json($review);
	}
}
