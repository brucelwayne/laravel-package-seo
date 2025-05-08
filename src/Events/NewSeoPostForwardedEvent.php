<?php

namespace Brucelwayne\SEO\Events;

use Brucelwayne\AI\Jobs\AbstractAIJob;
use Brucelwayne\AI\Jobs\PostTranslateJob;
use Brucelwayne\AI\Traits\HasAIJob;
use Brucelwayne\SEO\Models\SeoPostModel;
use Illuminate\Bus\Dispatcher;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mallria\Main\Traits\HasMallriaTranslatable;
use Mallria\Shop\Models\TransPostModel;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class NewSeoPostForwardedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param SeoPostModel $seo_post
     * @param TransPostModel $post
     */
    public function __construct(public $seo_post, public $post, public $locale = 'zh')
    {
        $supported_locales = LaravelLocalization::getSupportedLocales();
        foreach ($supported_locales as $localCode => $localValue) {
            if ($localCode === $locale) {
                continue;
            }
            /** @var HasMallriaTranslatable|HasAIJob $translationModel */
            $translationModel = $post->getTranslationOrNew($localCode);
            $translationModel->save();

            $existingJob = $translationModel->jobs()->where('job_name', PostTranslateJob::getName())->first();
            if (!$existingJob) {
                if (empty($translationModel->job)) {

//                        $job = new $this->translateJobClassName($model, $fromLocale, $toLocale, $prompt);
//                        $job->handle();

                    $job = (new PostTranslateJob($post, $locale, $localCode, ''))
                        ->onQueue('ai')
                        ->delay(10);
                    $jobId = $this->dispatchJob($job);
                    $translationModel->jobs()->create([
                        'job_name' => PostTranslateJob::getName(),
                        'job_id' => $jobId,
                    ]);
                }
            }
//            TranslatePostJob::dispatch(post_id: $this->post->getKey(), locale: $_locale);
        }
    }

    private function dispatchJob(AbstractAIJob $job): string
    {
        return app(Dispatcher::class)->dispatch($job);
    }
}
