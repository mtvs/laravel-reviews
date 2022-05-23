<?php

namespace Reviews;

class ReviewsRoutes
{
	public function reviews()
	{
		return function ($options = []) {
			$namespace = class_exists(
				$this->prependGroupNamespace('ReviewsController')
			) ? null : 'App\Http\Controllers';

			$this->group(['namespace' => $namespace], function () use($options) {
				if ($options['index'] ?? true) {
					$this->get('/{type}/{key}/reviews', 'ReviewsController@index')
						->name('reviews.index');
				}

				if ($options['store'] ?? true) {
					$this->post('/reviews', 'ReviewsController@store')
						->name('reviews.store');
				}

				if ($options['update'] ?? true) {
					$this->put('/review/{id}', 'ReviewsController@update')
						->name('reviews.update');
				}

				if ($options['delete'] ?? true) {
					$this->delete('/review/{id}', 'ReviewsController@delete')
						->name('reviews.delete');
				}
			});
		};
	}
}
