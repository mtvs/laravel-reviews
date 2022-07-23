<?php 

namespace Mtvs\Reviews\View\Components;

use Illuminate\View\Component;

class Reviews extends Component
{
	public $reviewable;

	public function __construct($reviewable)
	{
		$this->reviewable = $reviewable;
	}

	public function userReview()
	{
		return auth()->guest() ? null :
			auth()->user()->getReviewFor($this->reviewable);
	}

	public function render()
	{
		return view('reviews::reviews');
	}
}
