<template>
	<div>
		<div class="mb-1">
			<rating-stars :score="review.rating" />
		</div>
		
		<h4>
			{{ review.title }}
		</h4>

		<div v-html="body"></div>
	</div>
</template>

<script>
	export default {
		props: [
			'review',
		],

		computed: {
			body() {
				var escaped = _.escape(this.review.body)

				return escaped.replace(/(?:\r\n|\r|\n)/g, '<br/>')
			}
		}
	}
</script>