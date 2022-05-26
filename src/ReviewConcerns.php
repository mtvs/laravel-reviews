<?php

namespace Reviews;

use Mtvs\EloquentApproval\Approvable;

trait ReviewConcerns
{
	use Approvable;

	public function user()
	{
		return $this->belongsTo(config('auth.providers.users.model'));
	}
}
