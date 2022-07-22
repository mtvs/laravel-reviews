<div class="card" id="reviews">
	<div class="card-body">
		<x-ratings 
			:average="$reviewable->ratings_avg"
			:count="$reviewable->ratings_count"
			:ratios="$reviewable->ratingRatios()"/>

		<reviews 
			reviewable-slug="{{ $reviewable->getRouteType() }}"
			reviewable-type="{{ get_class($reviewable) }}"
			reviewable-id="{{ $reviewable->getKey() }}" 
			:user="{{ json_encode(auth()->user()) }}"
			:user-review="{{ json_encode($userReview()) }}"
		></reviews>
	</div>
</div>