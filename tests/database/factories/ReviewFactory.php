<?php

namespace Reviews\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mtvs\EloquentApproval\ApprovalFactoryStates;
use Reviews\Tests\Database\Factories\UserFactory;
use Reviews\Tests\Database\Factories\ProductFactory;
use Reviews\Tests\Models\Review;

class ReviewFactory extends Factory
{
	use ApprovalFactoryStates;

	protected $model = Review::class;

	public function definition()
	{
		return [
			'rating' => $this->faker
				->numberBetween(1, config('reviews.rating_max')),
			'title' => $this->faker->sentence,
			'body' => $this->faker->paragraph,
			'recommend' => $this->faker->boolean,
			'user_id' => function () {
				return config('auth.providers.users.model')::factory()->create();
			},
			'reviewable_type' => $reviewable_class = $this->faker->randomElements(
				config('reviews.reviewables')
			)[0],
			'reviewable_id' => function () use($reviewable_class) {
				return $reviewable_class::factory()->create();
			}
		];
	}
}