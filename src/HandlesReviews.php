<?php

namespace Reviews;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

trait HandlesReviews
{
	public function store(Request $request)
	{
		$this->validator($request->all())->validate();

		$user = auth()->user();

		// Reject the reviews for the non-existing reviewables
		try {
			$reviewable = $request['reviewable_type']::findOrFail(
				$request['reviewable_id']
			);
		} catch(ModelNotFoundException $e) {
			abort(422);
		} catch(\Throwable $e) {
			abort(422);
		}

		// Reject multiple reviews from a signle user
		if ($user->hasAlreadyReviewed($reviewable))
		{
			return abort(403);
		}

		$review = $user->reviews()
			->create($request->all());

		return $review;
	}

	public function update($id, Request $request)
	{
		$this->validator($request->all())->validate();

		$user = auth()->user();

		$review = $this->findReviewByUserOrFail($id, $user);

		$review->update(
			\Arr::except($request->all(), ['reviewable_type', 'reviewable_id'])
		);

		return $review;
	}

	public function delete($id, Request $request)
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
