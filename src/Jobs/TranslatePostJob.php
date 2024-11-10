<?php

namespace Brucelwayne\SEO\Jobs;

use Brucelwayne\AI\Agents\PostTranslateAgent;
use Brucelwayne\AI\LLMs\ChatGPT;
use Brucelwayne\AI\Models\AiLogModel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mallria\Shop\Jobs\PostTagsJob;
use Mallria\Shop\Models\Translations\PostTranslationModel;
use Mallria\Shop\Models\TransPostModel;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TranslatePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param int $post_id The ID of the post to be translated.
     * @param string $locale The target locale for translation.
     */
    public function __construct(public $post_id, public $locale, public $defaultLocale = 'zh')
    {

    }

    /**
     * 处理作业逻辑
     *
     * 该方法将执行以下步骤：
     * 1. 根据提供的 post ID 查找需要翻译的帖子。
     * 2. 根据 locale 确定目标语言。
     * 3. 调用翻译服务翻译帖子内容。
     * 4. 将翻译内容保存到指定语言区域。
     * 5. 从翻译后的文本中提取标签并同步。
     */
    public function handle()
    {
        $big_model_name = config('openai.model_name');

        // 获取支持的语言区域
        $language = null;
        $supported_locales = LaravelLocalization::getSupportedLocales();
        // 遍历支持的语言，匹配 locale，找到对应的语言名称
        foreach ($supported_locales as $_locale => $supported_locale) {
            if ($_locale === $this->locale) {
                $language = $supported_locale['name'];
                break;
            }
        }

        // 如果没有找到对应的语言，标记为作业失败
        if (empty($language)) {
            // 标记作业为失败，并附加失败原因
            $this->fail(new Exception('未找到指定的语言'));
            return;
        }

        // 查找需要翻译的帖子
        $post_model = TransPostModel::where('id', $this->post_id)->first();

        if (!empty($post_model)) {

            /**
             * @var PostTranslationModel $default_post_translation_model
             */
            $default_post_translation_model = $post_model->getTranslation($this->defaultLocale);

            if (empty($default_post_translation_model->content)) {
                return;
            }

            // 初始化 ChatGPT 模型
            $chatModel = new ChatGPT(model: $big_model_name);
            // 初始化翻译代理
            $postTranslateAgent = new PostTranslateAgent($chatModel);

            $post_model->setDefaultLocale($this->defaultLocale);

            // 调用翻译代理执行翻译操作
            $response = $postTranslateAgent->translateForLocale($language, $default_post_translation_model->content);

            //记录ai请求记录
            AiLogModel::create([
                'big_model_name' => $big_model_name,
                'model_type' => $post_model->getMorphClass(),
                'model_id' => $post_model->getKey(),
                'response' => $response,
            ]);

            if (config('app.debug')) {
                Log::info('AI response：' . json_encode($response));
            }

            $result = get_json_result_from_ai_response($response);

            // 处理翻译失败或返回数据异常的情况
            if (empty($result['status'])) {
                // 标记为翻译错误
                Log::error('翻译服务未返回状态');
                $this->fail(new Exception('翻译服务未返回状态'));
                return;
            }
            if (empty($result['text'])) {
                // 标记为翻译内容为空的错误
                Log::error('翻译结果为空');
                $this->fail(new Exception('翻译结果为空'));
                return;
            }
            if ($result['status'] === 'error') {
                // 标记为翻译服务返回了错误
                Log::error('翻译服务返回错误状态');
                $this->fail(new Exception('翻译服务返回错误状态'));
                return;
            }

            // 将翻译后的文本保存到对应的语言翻译模型
            /**
             * @var PostTranslationModel $post_translation_model
             */
            $post_translation_model = $post_model->translateOrNew($this->locale);
            $post_translation_model->content = $result['text'];
            $post_translation_model->save();
            $post_model->save();

            PostTagsJob::dispatchSync(post: $post_model);

//            // 使用正则表达式从翻译文本中提取所有标签
//            $tags = $result['tags'];
////            Log::info('$tags: ' . json_encode($tags));
//            // 如果有标签，进行同步操作
//            if (!empty($tags)) {
//                $post_translation_model->syncTags($tags);
//            }
//
//            $post_model->searchable();
        }
    }
}
