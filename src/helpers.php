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
        // 使用正则表达式匹配所有以 # 开头的标签，标签内不允许有空格或其他 # 符号
        //preg_match_all('/#(\S+?)(?=#|\s|$)/u', $content, $matches);
        //preg_match_all('/#(\S+?)(?=#|\s|$)/u', $content, $matches);
        preg_match_all('/#([\p{L}\p{N}]+)/u', $content, $matches);

        // 如果有匹配的结果，将匹配的标签存入 $tags 数组
        if (!empty($matches[1])) {
            $tags = $matches[1];
            // 截取每个标签为最多 32 个字符，并去掉空标签
            $tags = array_filter(array_map(function ($tag) {
                return substr(trim($tag), 0, 32); // 截取并去除首尾空格
            }, $tags));
        }

        // 返回标签数组，找不到标签时返回空数组
        return $tags;
    }
}

if (!function_exists('get_json_result_from_ai_response')) {
    /**
     * 从AI的响应中提取并解析JSON。
     *
     * 该方法会尝试从AI的响应中提取JSON内容。如果响应中包含以 ```json 标记的代码块，
     * 它会优先提取并解析最后一个JSON代码块。如果没有这种代码块，则直接尝试将整个响应解析为JSON。
     *
     * @param string $content AI的响应内容。
     * @return array|null 返回解析后的JSON数据（数组格式），如果无法解析则返回null。
     */
    function get_json_result_from_ai_response($content): ?array
    {
        // 使用正则表达式匹配 ```json 标记的代码块，并提取中间的内容
        preg_match_all('/```json\s*(.*?)\s*```/ms', $content, $matches);

        if (!empty($matches[1])) {
            // 取最后一个匹配到的JSON代码块内容
            $lastMatch = end($matches[1]);
            $jsonString = trim($lastMatch);

            // 如果提取到的内容不是合法的JSON，则进行日志记录并返回null
            $result = json_decode($jsonString, true);
        } else {
            // 如果没有匹配到代码块，直接尝试将整个响应作为JSON解析
            $result = json_decode($content, true);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            // 将错误的响应内容和解析错误记录到日志
            Log::error('get_json_result_from_ai_response 不合法的json字符串：' . $content);
            Log::error('JSON 解析错误信息：' . json_last_error_msg());
            return null;
        }

        return $result;
    }
}

