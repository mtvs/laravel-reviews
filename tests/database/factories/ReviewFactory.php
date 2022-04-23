<?php

namespace Reviews\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mtvs\EloquentApproval\ApprovableFactory;
use Reviews\Review;
use Reviews\Tests\Database\Factories\UserFactory;
use Reviews\Tests\Database\Factories\ProductFactory;
use Reviews\Tests\Models\Product;

class ReviewFactory extends Factory
{
	use ApprovableFactory;

	protected $model = Review::class;

	public function definition()
	{
		return [
			'rating' => $this->faker
				->numberBetween(1, app('review_class')::RATING_MAX),
			'title' => $this->faker->sentence,
			'body' => $this->faker->paragraph,
			'recommend' => $this->faker->boolean,
			'user_id' => function () {
				return UserFactory::new()->create();
			},
			'reviewable_type' => Product::class,
			'reviewable_id' => function () {
				return ProductFactory::new()->create();
			}
		];
	}
}