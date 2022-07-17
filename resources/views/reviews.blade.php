<div class="card">
	<div class="card-body">
		<x-ratings 
			:average="$reviewable->ratings_avg"
			:count="$reviewable->ratings_count"/>

		<reviews 
			reviewable-slug="{{ $reviewable->getRouteType() }}"
			reviewable-type="{{ get_class($reviewable) }}"
			reviewable-id="{{ $reviewable->getKey() }}" 
			:user="{{ auth()->user() }}"
			:user-review="{{ $userReview() }}"
		></reviews>
	</div>
</div>