<?php

namespace Reviews;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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
			abort(422, "The reviewable_id does not exist.");
		}

		$user = auth()->user();

		// Reject multiple reviews from a signle user
		if ($user->hasAlreadyReviewed($reviewable))
		{
			return abort(403, 'It has already been reviewed by the user.');
		}

		$review = $user->reviews()
			->make($request->all())
			->reviewable()
			->associate($reviewable);

		$review->save();

		return $review;
	}

	public function update($id, Request $request)
	{
		$user = auth()->user();

		$review = $this->findReviewByUserOrFail($id, $user);

		$this->validator($request->all())->validate();

		$review->update(
			$request->all()
		);

		return $review;
	}

	public function destroy($id, Request $request)
	{
		$user = auth()->user();

		$review = $this->findReviewByUserOrFail($id, $user);

		$review->delete();
	}

	protected function findReviewByUserOrFail($id, $user)
	{
		return $user->reviews()->anyApprovalStatus()->findOrFail($id);
	}
}
