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
use Mallria\Shop\Models\PostTranslationModel;
use Mallria\Shop\Models\TranslatablePostModel;
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
        Log::info('Fetching supported locales...');

        $big_model_name = 'qwen-max-latest';

        // 获取支持的语言区域
        $language = null;
        $supported_locales = LaravelLocalization::getSupportedLocales();
        // 遍历支持的语言，匹配 locale，找到对应的语言名称
        foreach ($supported_locales as $locale => $supported_locale) {
            if ($locale === $this->locale) {
                $language = $supported_locale['name'];
                break;
            }
        }

        Log::info('$language: ' . $language);

        // 如果没有找到对应的语言，标记为作业失败
        if (empty($language)) {
            // 标记作业为失败，并附加失败原因
            $this->fail(new Exception('未找到指定的语言'));
            return;
        }

        // 查找需要翻译的帖子
        $post_model = TranslatablePostModel::where('id', $this->post_id)->first();

//        Log::info('$post_model: ' . json_encode($post_model->toArray()));

        if (!empty($post_model)) {
            // 初始化 ChatGPT 模型
            $chatModel = new ChatGPT(model: $big_model_name);
            // 初始化翻译代理
            $postTranslateAgent = new PostTranslateAgent($chatModel);

            $post_model->setDefaultLocale($this->defaultLocale);

            /**
             * @var PostTranslationModel $default_post_translation_model
             */
            $default_post_translation_model = $post_model->getTranslation($this->defaultLocale);

            // 调用翻译代理执行翻译操作
            Log::info('翻译文本: ' . $default_post_translation_model->content);
            $result = $postTranslateAgent->translateForLocale($language, $default_post_translation_model->content);

            //记录ai请求记录
            AiLogModel::create([
                'big_model_name' => $big_model_name,
                'model_type' => $post_model->getMorphClass(),
                'model_id' => $post_model->getKey(),
                'response' => $result,
            ]);

            Log::info('result: ' . $result);

            // 在调用 json_decode 之前
//            $result = preg_replace('/^```json\s*|\s*```$/', '', $result);
            // 尝试解析 JSON
//            $result = json_decode($result, true);

            preg_match_all('/```json\s*(.*?)\s*```/ms', $result, $matches);
            if (!empty($matches[1])) {
                // 取最后一个匹配的字符串
                $lastMatch = end($matches[1]);
                // 尝试解析 JSON
                $result = json_decode($lastMatch, true);
            }

            // 记录解析后的结果
            Log::info('Decoded result: ' . json_encode($result));

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
            Log::info('$post_translation: ' . json_encode($post_translation_model->toArray()));
            $post_translation_model->content = $result['text'];
            $post_translation_model->save();
            $post_model->save();

            // 使用正则表达式从翻译文本中提取所有标签
            $tags = [];
            preg_match_all('/#(\S+)/', $result['text'], $matches);
            if (!empty($matches[1])) {
                $tags = $matches[1];
            }

            Log::info('$tags: ' . json_encode($tags));
            // 如果有标签，进行同步操作
            if (!empty($tags)) {
                /**
                 * @var PostTranslationModel $post_translation_model
                 */
                $post_translation_model = $post_model->getTranslation($this->locale);
                $post_translation_model->syncTags($tags);
            }
        }
    }
}
