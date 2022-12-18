<div class="card" id="reviews">
	<div class="card-body">
		<x-ratings 
			:average="$reviewable->ratings_avg"
			:count="$reviewable->ratings_count"
			:ratios="$reviewable->ratingRatios()"/>

		<reviews 
			reviewable-type="{{ $reviewable->getType() }}"
			reviewable-id="{{ $reviewable->getKey() }}" 
			:user="{{ json_encode(auth()->user()) }}"
			:user-review="{{ json_encode($userReview()) }}"
		></reviews>
	</div>
</div>