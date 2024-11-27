<?php

namespace Brucelwayne\SEO\Listeners;

use Brucelwayne\AI\Jobs\AbstractSEOJob;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

class FailedJobListener
{
    public function handle(JobFailed $event)
    {
        try {
            $jobId = $event->job->getJobId();
            $failedJobUuid = $event->job->uuid();

            $payload = $event->job->payload();
            $command = unserialize($payload['data']['command']);

            // 检查作业是否继承了 AbstractAIJob
            if ($command instanceof AbstractSEOJob) {
                $command->recordFailedJob($jobId, $failedJobUuid);
            }
        } catch (\Exception|\Throwable $e) {
            Log::error($e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}