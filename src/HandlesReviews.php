<?php

namespace Mtvs\Reviews;

use Illuminate\Http\Request;

trait HandlesReviews
{
	public function store(Request $request)
	{
		$this->validator($request->all())->validate();

		if (! in_array(
				$request['reviewable_type'], config('reviews.reviewables')
			)) {
			abort(422, "The reviewable_type does not exist.");
		}

		if (! $reviewable = $request['reviewable_type']::find(
			$request['reviewable_id']
		)) {
			abort(422, "The reviewable model was not found.");
		}

		$user = auth()->user();

		// Reject multiple reviews from a single user
		if ($user->hasAlreadyReviewed($reviewable))
		{
			return abort(403, 'It has already been reviewed by the user.');
		}

		$review = $user->reviews()
			->make($request->all())
			->reviewable()
			->associate($reviewable);

		$review->save();

		return $review->load('user');
	}

	public function update($key, Request $request)
	{
		$review = $this->findReviewOrFail($key);

		$this->validator($request->all())->validate();

		$review->update(
			$request->all()
		);

		return $review;
	}

	public function destroy($key, Request $request)
	{
		$review = $this->findReviewOrFail($key);

		$review->delete();
	}

	protected function findReviewOrFail($key)
	{
		return auth()->user()
			->reviews()
			->anyApprovalStatus()
			->findOrFail($key);
	}
}
