<?php

namespace Brucelwayne\SEO\Jobs;

use Brucelwayne\AI\Models\JobableModel;
use Brucelwayne\AI\Traits\HasAIJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Mallria\Core\Models\PageModel;
use Mallria\Shop\Models\TransPostModel;
use Mallria\Shop\Models\TransProductModel;

abstract class AbstractSEOJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 子类必须定义的作业名称。
     */
    protected const NAME = 'seo-job';

    /**
     * @var TransProductModel|TransPostModel|PageModel|Model
     */
    protected $model;
    protected string $locale;
    protected string $url;
    protected string $searchEngine;
    protected ?string $prompt;

    public function __construct($model, string $locale, string $url, string $searchEngine = 'google', ?string $prompt = null)
    {
        $this->model = $model;
        $this->locale = $locale;
        $this->url = $url;
        $this->searchEngine = strtolower($searchEngine);
        $this->prompt = $prompt;
    }

    /**
     * 定义一个抽象方法，所有子类必须实现。
     *
     * @return void
     */
    abstract public function handle();

    function recordFailedJob($jobId, $failedJobId)
    {
        $jobable = $this->getJobable($this->model);
        if (!empty($jobable)) {
            $jobable->update([
                'job_id' => $jobId,
                'failed_job_id' => $failedJobId,
            ]);
        }
    }

    function getJobable($model): ?JobableModel
    {
        if (empty($model)) {
            return null;
        }
        return $model->job()->where('job_name', static::getName())->first();
    }

    /**
     * 获取作业名称。
     *
     * @return string
     */
    public static function getName(): string
    {
        return static::NAME;
    }

    /**
     * 格式化异常消息以包括 model 和 jobModel 信息。
     *
     * @param string $message
     * @return string
     */
    protected function formatExceptionMessage(string $message): string
    {
        return sprintf(
            "%s\nModel: %s",
            $message,
            json_encode($this->model->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * 运行数据库事务并捕获异常。
     *
     * @param callable $callback
     * @return void
     */
    protected function runTransaction(callable $callback)
    {
        DB::transaction($callback);
    }

    /**
     * 删除与作业相关的数据库记录。
     *
     * @param HasAIJob $model
     * @return bool
     */
    protected function deleteJob()
    {
        $jobable = $this->getJobable($this->model);
        if (!empty($jobable)) {
            return $jobable->delete();
        }
        return false;
    }
}
