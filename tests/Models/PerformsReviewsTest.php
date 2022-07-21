<?php

namespace Reviews\Tests;

use Reviews\Tests\Database\Factories\ReviewFactory;
use Reviews\Tests\Database\Factories\UserFactory;

class PerformsReviewsTest extends TestCase
{
	/** @test */
	public function it_deletes_its_reviews_when_is_deleted()
	{
		$user = UserFactory::new()->create();

		$user->reviews()->saveMany(
			ReviewFactory::times(3)
				->approved()
				->make()
		);

		$user->delete();

		$this->assertEquals(0, $user->reviews()->count());
	}
}