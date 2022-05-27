<?php 

namespace Reviews\View\Components;

use Illuminate\View\Component;

class Reviews extends Component
{
	public $reviewable;

	public function __construct($reviewable)
	{
		$this->reviewable = $reviewable;
	}

	public function indexUrl()
	{
		return route('reviews.index',[ 
			'type' => $this->reviewable->getRouteType(),
			'key' => $this->reviewable->getRouteKey()
		]);
	}

	public function userReview()
	{
		return auth()->guest() ? null :
			auth()->user()->getReviewFor($this->reviewable);
	}

	public function render()
	{
		$props = [
			'index-url' => $this->indexUrl(),
			'reviewable-type' => get_class($this->reviewable),
			'reviewable-id' => $this->reviewable->getKey(),
			':user-review' => $this->userReview(),
		];

		$html = "<reviews {$this->encodeProps($props)}></reviews>";

		return $html;
	}

	protected function encodeProps(array $props)
	{
		return implode(' ', array_map(
			function ($key, $value) {
				if (! is_scalar($value)) {
					$value = json_encode($value);
				}

				$value = e($value);

				return "$key=\"$value\"";
			},
			array_keys($props),
			array_values($props)
		));
	}
}
