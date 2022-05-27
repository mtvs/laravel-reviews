<template>
	<div>
		<div class="card" :class="'border-'+approvalContext">
			<div class="card-header d-flex justify-content-between">
				<div>
					<span class="badge" :class="'bg-'+approvalContext">
						{{ review.approval_status }}
					</span>
				</div>

				<div>
					<button type="button" class="btn btn-sm btn-link"
						 @click="$emit('edit-requested')">
	                    Edit
	                </button>

	                <button type="button" class="btn btn-sm btn-link text-danger"
	                	data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
	                	Delete
	                </button>
				</div>
			</div>

			<div class="card-body">
				<reviews-single :review="review"/>
			</div>
		</div>

		<div class="modal" tabindex="-1" id="deleteConfirmModal">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-body">
		        <p>Please confirm to delete the review.</p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" 
		        	data-bs-dismiss="modal">Cancel</button>

		        <button type="button" class="btn btn-danger"
		        	@click="deleteReview" data-bs-dismiss="modal">Confirm</button>
		      </div>
		    </div>
		  </div>
		</div>	
	</div>
</template>

<script>
	export default {
		props: [
			'review'
		],

		computed: {
			approvalContext() {
				if (this.review.approval_status == 'pending') {
					return 'warning'
				}
				else if (this.review.approval_status == 'approved') {
					return 'success'
				}
				else if (this.review.approval_status == 'rejected') {
					return 'danger'
				}
			}
		},

		methods: {
			deleteReview() {
				axios.delete(`/review/${this.review.id}`)
					.then(() => { this.$emit('review-deleted') })
			}
		}
	}
</script>