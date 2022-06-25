<?php

namespace Reviews\View\Components;

use Illuminate\View\Component;

class Ratings extends Component
{
	const STAR_FULL_ICON = 'icon-star';
	const STAR_HALF_ICON = 'icon-star-half-alt';
	const STAR_EMPTY_ICON = 'icon-star-empty';

	public $average;
	public $count;
	public $max;
	public $ratios;

	public function __construct($average, $count, $max, $ratios = null)
	{
		$this->average = $average;
		$this->count = $count;
		$this->max = $max;
		$this->ratios = $ratios;
	}

	public function starIcon($n)
	{
		if ($n <= $this->average) {
			return static::STAR_FULL_ICON;
		}

		if (($d = $n - $this->average) < 1) {
			if ($d > 0.75) {
				return static::STAR_EMPTY_ICON;
			}
			elseif ($d < 0.25) {
				return static::STAR_FULL_ICON;
			}
			
			return static::STAR_HALF_ICON;
		}

		return static::STAR_EMPTY_ICON;
	}

	public function render()
	{
		return view('reviews::ratings');
	}
}
