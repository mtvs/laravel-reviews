<template>
	<div>
		<div class="mb-1 d-flex justify-content-between">
			<reviews-stars :score="review.rating" />

			<div class="text-muted">
				<span>{{ createdAt }}</span> |
				<span>
					{{ review.user.name }}
				</span>
			</div>
		</div>
		
		<h4>
			{{ review.title }}
		</h4>

		<div v-html="body"></div>

		<div v-if="updated" class="text-muted">
			<hr>

			Last updated at {{ updatedAt}}
		</div>
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
			},

			createdAt() {
				return this.formatDateTime(this.review.created_at)
			},

			updatedAt() {
				return this.formatDateTime(this.review.updated_at)
			},

			updated() {
				return this.review.updated_at !== this.review.created_at
			}
		},

		methods: {
			formatDateTime(value) {
				let date = new Date(value)

				return date.toLocaleString()
			}
		}
	}
</script>