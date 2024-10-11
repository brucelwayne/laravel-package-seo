<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('get_tags_from_string')) {
    /**
     * 从给定的字符串中提取所有标签
     *
     * @param string $content 包含标签的内容
     * @return array 提取的标签数组
     */
    function get_tags_from_string(string $content): array
    {
        $tags = [];
        // 匹配@@TAGS_START@@和@@TAGS_END@@之间的内容
        preg_match('/@@TAGS_START@@\s*(.*?)\s*@@TAGS_END@@/s', $content, $matches);

        if (!empty($matches[1])) {
            // 使用正则表达式匹配所有以 # 开头的标签，标签内不允许有空格或符号
            preg_match_all('/#([\p{L}\p{N}]+)/u', $matches[1], $tagMatches);

            if (!empty($tagMatches[1])) {
                // 将匹配到的标签存入 $tags 数组，截取并过滤
                $tags = array_filter(array_map(function ($tag) {
                    return substr(trim($tag), 0, 32); // 截取标签长度至32字符
                }, $tagMatches[1]));
            }
        }

        return $tags;
    }
}

if (!function_exists('get_json_result_from_ai_response')) {
    /**
     * 从AI的响应中提取并解析JSON。
     *
     * 该方法会尝试从AI的响应中提取优化后的内容和标签，并根据需要将标签附加到优化后的文本中。
     *
     * @param string $content AI的响应内容。
     * @param bool $withTags 是否在优化后的文本后附加标签。
     * @return array|null 返回解析后的JSON数据（数组格式），如果无法解析则返回null。
     */
    function get_json_result_from_ai_response($content, $withTags = true): ?array
    {
        $result = [];

        // 匹配优化后的文本部分
        preg_match('/@@OPTIMIZED_TEXT_START@@\s*(.*?)\s*@@OPTIMIZED_TEXT_END@@/s', $content, $textMatches);

        if (!empty($textMatches[1])) {
            $result['text'] = trim($textMatches[1]); // 提取优化后的文本
        } else {
            Log::error('get_json_result_from_ai_response 未找到优化后的文本。');
            return null;
        }

        // 获取标签
        $tags = get_tags_from_string($content);
        $result['tags'] = $tags;

        // 如果 $withTags 为 true，则将标签作为字符串添加到文本后面
        if ($withTags) {
            $tagsString = ' ' . implode(' ', array_map(function ($tag) {
                    return '#' . $tag;
                }, $tags));

            // 将标签附加到文本后面
            $result['text'] .= $tagsString;
        }

        // 构建返回格式
        return [
            'status' => 'success',
            'text' => $result['text'],
            'tags' => $result['tags'],
            'message' => '优化成功'
        ];
    }

}

