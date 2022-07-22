<span>
	<span class="rating-stars d-inline-flex">
		@foreach(range(1, $max()) as $n)
			<i class="{{ $starIcon($n) }}"></i>
		@endforeach
	</span>

	<span>
		{{ sprintf("(%.1f from %d reviews)", $average, $count) }}
	</span>
</span>

@if($ratios)
	<div class="card">
		<div class="card-body">
			@foreach($ratios as $rating => $ratio)
				<div class="d-flex mb-1">
					<div class="w-25">
						<span class="badge bg-primary">
							{{ $rating }} 
							<i class="icon-star"></i>
						</span>
					</div>

					<div class="progress w-75">
						<div class="progress-bar" style="width: {{ $ratio }}%;">
							{{ $ratio }}%
						</div>
					</div>			
				</div>
			@endforeach
		</div>
	</div>
@endif