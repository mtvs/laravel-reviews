<template>
	<nav v-if="data.last_page > 1" class="d-flex justify-content-between"
		aria-label="Reviews pages">
		<ul class="pagination">
			<li class="page-item" :class="{disabled: !hasPrev}">
				<a class="page-link" @click.prevent="getPrev">
					<span aria-hidden="true">&laquo;</span>

					<span>Previous</span>
				</a>
			</li>

			<li class="page-item" :class="{disabled: !hasNext}">
				<a class="page-link" @click.prevent="getNext">
					<span>Next</span>

					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>			
		</ul>
		
		<p class="text-muted">
			From: {{ data.from }} to: {{ data.to }}, Total: {{ data.total }}
		</p>		
	</nav>
</template>

<script>
export default {
	props: [
		'data'
	],

	computed: {
		hasPrev() {
			return this.data.current_page > 1
		},

		hasNext() {
			return this.data.current_page < this.data.last_page
		}
	},

	methods: {
		getNext() {
			this.getPage(this.data.next_page_url)
		},

		getPrev() {
			this.getPage(this.data.prev_page_url)
		},

		getPage(url) {
			this.$emit('page-requested', url)
		}
	}
}
</script>