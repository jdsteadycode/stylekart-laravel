<?php

// folder path
namespace App\Events;

// DeliveryJob Model class path
use App\Models\DeliveryJob;

// Traits path
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReturnJobAccepted
{
    use Dispatchable, SerializesModels;

    // job variable
    public $job;

    public function __construct(DeliveryJob $job)
    {

        // log the action
        logger()->info("[app\Events\ReturnJobAccepted@__construct] Return Job Accepted Event triggered! (instantiated)");

        // set the job
        $this->job = $job;
    }
}
