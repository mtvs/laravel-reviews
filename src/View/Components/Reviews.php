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

	public function userReview()
	{
		return auth()->guest() ? null :
			auth()->user()->getReviewFor($this->reviewable);
	}

	public function render()
	{
		$props = [
			'reviewable-slug' => $this->reviewable->getRouteType(),
			'reviewable-type' => get_class($this->reviewable),
			'reviewable-id' => $this->reviewable->getKey(),
			':user' => auth()->user(),
			':user-review' => $this->userReview(),
		];

		$html = "<reviews {$this->encodeProps($props)}></reviews>";

		return $html;
	}

	protected function encodeProps(array $props)
	{
		return implode(' ', array_map(
			function ($key, $value) {
				if (strpos($key, ':') === 0) {
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
