<span>
	<span class="rating-stars d-inline-flex">
		@foreach(range(1, $max) as $n)
			<i class="{{ $starIcon($n) }}"></i>
		@endforeach
	</span>

	<span>
		{{ sprintf("(%.1f %s %d)", $average, __('out of'), $count) }}
	</span>
</span>