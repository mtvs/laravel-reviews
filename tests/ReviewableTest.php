<?php

namespace Reviews\Tests;

use Reviews\Tests\Database\Factories\ProductFactory;
use Reviews\Tests\Database\Factories\ReviewFactory;
use Reviews\Tests\Models\Product;

class ReviewableTest extends TestCase
{
	/** @test */
	public function it_can_be_sorted_by_the_highest_rated()
	{
		$products = ProductFactory::times(4)->create();

		$products[0]->reviews()->saveMany(
			ReviewFactory::times(1)->approved()->make([
				'rating' => 5
			])
		);

		$products[1]->reviews()->saveMany(
			ReviewFactory::times(4)->approved()->make([
				'rating' => 5
			])
		);

		$products[2]->reviews()->saveMany(
			ReviewFactory::times(1)->approved()->make([
				'rating' => 1
			])
		);	

		$products[3]->reviews()->saveMany(
			ReviewFactory::times(4)->approved()->make([
				'rating' => 1
			])
		);					

		$highestRated = Product::query()->highestRated()->get();

		$this->assertEquals($products[1]->id, $highestRated[0]->id);
		$this->assertEquals($products[3]->id, $highestRated->last()->id);
	}

	/** @test */
	public function it_can_eager_load_the_ratings()
	{
		
		$product = ProductFactory::new()->create();

		$product->reviews()->saveMany([
			ReviewFactory::new()->approved()->make([
				'rating' => 5
			]),
			ReviewFactory::new()->approved()->make([
				'rating' => 4
			])
		]);

		$result = Product::withRatings()->find($product->id);

		$this->assertEquals(4.5, $result->ratings_avg);
		$this->assertEquals(2, $result->ratings_count);
	}

	/** @test */
	public function it_can_lazy_load_the_ratings()
	{
		$product = ProductFactory::new()->create();

		$product->reviews()->saveMany([
			ReviewFactory::new()->approved()->make([
				'rating' => 5
			]),
			ReviewFactory::new()->approved()->make([
				'rating' => 4
			]),
			ReviewFactory::new()->make([
				'rating' => 1
			])
		]);

		$this->assertNull($product->ratings_avg);
		$this->assertNull($product->ratings_count);

		$product->loadRatings();

		$this->assertEquals(4.5, $product->ratings_avg);
		$this->assertEquals(2, $product->ratings_count);
	}
}
