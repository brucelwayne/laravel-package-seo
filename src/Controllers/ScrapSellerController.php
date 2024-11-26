<?php

namespace Brucelwayne\SEO\Controllers;

use Brucelwayne\SEO\Models\SeoPostModel;
use Brucelwayne\SEO\Models\SeoUserModel;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mallria\Core\Http\Controllers\BaseController;
use Mallria\Core\Http\Responses\ErrorJsonResponse;
use Mallria\Core\Http\Responses\SuccessJsonResponse;
use Mallria\Core\Models\User;
use Mallria\Shop\Enums\ExternalPostPlatform;

class ScrapSellerController extends BaseController
{
    public function savePost(Request $request)
    {

    }

    public function savePosts(Request $request)
    {
        $request->validate([
            'platform' => 'required|string',
            'ex_user' => 'required|string|max:32',
        ]);
        $platform = ExternalPostPlatform::from($request->post('platform'));
        $ex_user_hash = $request->post('ex_user');
        $seo_user_model = SeoUserModel::byHashOrFail($ex_user_hash);
        $seo_user_model->load(['user']);

        $posts = $request->post('posts');

        $result = [];
        if (!empty($posts)) {
            DB::beginTransaction();
            try {
                foreach ($posts as $post) {
                    $fk_post_id = $post['goods_id'];
                    $old_post = SeoPostModel::where([
                        'platform' => $platform->value,
                        'fk_id' => $fk_post_id,
                    ])->first();
                    if (!empty($old_post)) {
                        //如果更新时间不一样
                        if ($old_post['update_time'] !== $post['update_time']) {
                            $old_post->update([
                                'platform' => $platform->value,
                                'seo_user_id' => $seo_user_model->getKey(),
                                'fk_id' => $post['goods_id'],
                                'title' => $post['title'],
                                'payload' => $post,
                            ]);
                            //更新的不算了
//                            $result[] = $old_post;
                        }
                    } else {
                        //创建新的
                        $new_post = SeoPostModel::create([
                            'platform' => $platform->value,
                            'seo_user_id' => $seo_user_model->getKey(),
                            'fk_id' => $post['goods_id'],
                            'title' => mb_convert_encoding($post['title'], 'UTF-8', 'auto'),
                            'payload' => $post,
                        ]);
                        $result[] = $new_post;
                    }
                }

                $seo_user_model->update([
                    'scrap_at' => now(),
                ]);

                DB::commit();
            } catch (\Exception|\Throwable $e) {
                DB::rollBack();
                return new ErrorJsonResponse($e->getMessage());
            }
        }
        return new SuccessJsonResponse([
            'result' => $result,
            'job' => $seo_user_model,
        ]);
    }

    public function create(Request $request)
    {
        // 验证请求数据
        $request->validate([
            'platform' => 'required|string',
        ]);
        $platform = ExternalPostPlatform::from($request->post('platform'));

        if ($platform === ExternalPostPlatform::Szwego) {
            $sellers = $request->post('sellers');

            if (!empty($sellers)) {
                DB::beginTransaction();
                $result = [];
                try {
                    foreach ($sellers as $seller) {

                        $shop_name = $seller['shop_name'];
                        $shop_id = $seller['shop_id'];
                        $user_icon = $seller['user_icon'];
                        $shop_url = $seller['shop_url'];

                        if ($shop_name === '我的相册') {
                            continue;
                        }

                        // 检查 seller 表中是否有相同的平台 ID 和外部 ID
                        $existingSeller = SeoUserModel::where('platform', $platform->value)
                            ->where('fk_user_id', $shop_id)
                            ->first();

                        if ($existingSeller) {
                            continue;
                        }

                        // 如果不存在，则使用 Faker 创建一个用户
                        $faker = Faker::create();
                        // 随机生成一个密码
                        $randomPassword = Str::random(12);
                        $new_user = User::create([
                            'name' => $faker->name,
                            'email' => $faker->unique()->safeEmail,
                            'password' => bcrypt($randomPassword), // 使用随机密码
                        ]);

                        $handle_name = generate_handle_name_from_string($faker->userName);

                        $new_user->handle()->updateOrCreate([
                            'handleable_type' => $new_user->getMorphClass(),
                            'handleable_id' => $new_user->getKey(),
                        ], [
                            'name' => $handle_name,
                        ]);

                        // 根据 POST 的数据创建 seller，并关联这个新的用户 ID
                        $new_seller = SeoUserModel::create([
                            'user_id' => $new_user->id,
                            'platform' => $platform->value,
                            'fk_user_id' => $shop_id,
                            'fk_user_name' => $shop_name,
                            'fk_user_avatar' => $user_icon,
                            'scrap_user_url' => $shop_url,
                            'available' => true,
                        ]);
                        $result[] = $new_seller;
                    }
                    DB::commit();
                    return new SuccessJsonResponse([
                        'users' => $result,
                    ], '卖家创建成功！');
                } catch (\Throwable|\Exception $e) {
                    DB::rollBack();
                    return new ErrorJsonResponse('卖家创建失败，错误信息：' . $e->getMessage());
                }
            }
        }
        return new SuccessJsonResponse();
    }

    public function getJob(Request $request)
    {
        // 验证请求数据
        $request->validate([
            'platform' => 'required|string',
        ]);
        $platform = ExternalPostPlatform::from($request->post('platform'));

        $seo_job_model = SeoUserModel::where('platform', $platform->value)
            ->where('available', true)
            ->orderBy('scrap_at', 'asc')
            ->first();

        //表示下发过了
        $seo_job_model->update([
            'scrap_at' => now(),
        ]);

        return new SuccessJsonResponse([
            'job' => $seo_job_model,
        ]);
    }
}
