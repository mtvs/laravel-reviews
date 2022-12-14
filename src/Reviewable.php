<?php

namespace Mtvs\Reviews;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

trait Reviewable
{
	/**
	 * Boot the Reviewable trait.
	 * 
	 * @return void
	 **/ 
	public static function bootReviewable()
	{
		static::deleted(function ($model) {
			if (! $model->exists) {
				$model->reviews()->forceDelete();
			}
		});
	}

	/**
	 * Get the name of the reviewable set.
	 * 
	 * Each reviewable model is considered a set and the set's name is the
	 * plural form of the model name.
	 * 
	 * @return string
	 **/
	public function getSetName()
	{
		$name = (new \ReflectionClass(get_called_class()))
			->getShortName();

		return Str::plural(Str::snake($name, '-'));
	}

	public function getType()
	{
		return get_called_class();
	}	

	/**
	 * Return the relation to the reviews model
	 * 
	 * @return MorphMany
	 **/
	public function reviews()
	{
		return $this->morphMany(config('reviews.model'), 'reviewable');
	}

	/**
	 * Return a set containing the ratio of each rating score to the total.
	 * 
	 * @return array
	 **/
	public function ratingRatios()
	{
		$ratios = [];

		$total = $this->reviews()->count();

		$this->reviews()->groupBy('rating')
			->selectRaw('rating, count(*) as count')
			->get()
			->each(function($group) use (&$ratios, $total) {
				$ratios[$group->rating] = round($group->count / $total * 100);
			});

		foreach(range(1, config('reviews.rating_max')) as $rating) {
			if (! array_key_exists($rating, $ratios)) {
				$ratios[$rating] = 0;
			}
		}

		ksort($ratios);

		return $ratios;
	}

	public function getRatingRatiosAttribute()
	{
		return $this->ratingRatios();
	}

	/**
	 * Apply an order-by clause on the query to sort the results based on the
	 * Bayesian average of their ratings.
	 * 
	 * @param Builder $query
	 * @return void
	 **/
	public function scopeHighestRated($query)
	{
		$totalAverage = $this->reviews()->getRelated()
			->where('reviewable_type', get_called_class())
			->whereHas('reviewable')
			->avg('rating');

		$confidenceNumber = $this->bayesianConfidenceNumber();

		$query->withRatings()
			->orderByRaw(
				'(IFNULL(ratings_avg, 0) * ratings_count + ? * ?)'.
				'/ (ratings_count + ?) DESC', 
				[$totalAverage, $confidenceNumber, $confidenceNumber]
			);
	}

	/**
	 * Calculate the confidence number that is used in the Bayesian formula.
	 * 
	 * @return float
	 **/
	protected function bayesianConfidenceNumber()
	{
		return $this->reviews()->getRelated()
			->where('reviewable_type', get_called_class())
			->whereHas('reviewable')
			->count() / $this->count();;
	}

	/**
	 * Eager load the ratings values: the average and the count
	 * 
	 * @param Builder $query
	 * @return void
	 **/
	public function scopeWithRatings($query)
	{
		$query->withAvg('reviews as ratings_avg', 'rating')
			->withCount('reviews as ratings_count');
	}

	/**
	 * Lazy load the ratings values: the average and the count
	 **/ 
	public function loadRatings()
	{
		$this->ratings_avg = $this->reviews()->avg('rating');

		$this->ratings_count = $this->reviews()->count();
	}
}
