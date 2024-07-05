<?php

namespace Services;

/**
 * Class HttpRequest
 * 用于发送HTTP请求
 *
 * @version 1.0
 *
 * @example
 * 使用示例
 * $responseGet = HttpRequest::get('https://api.example.com/data');
 * if ($responseGet !== false) {
 *      echo "GET Response:\n" . $responseGet;
 * } else {
 *      echo "GET Request failed.";
 * }
 *
 * $responsePost = HttpRequest::post('https://api.example.com/submit', ['key' => 'value']);
 * if ($responsePost !== false) {
 *      echo "\nPOST Response:\n" . $responsePost;
 * } else {
 *      echo "\nPOST Request failed.";
 * }
 *
 * @package Services
 */
class HttpRequest
{
    /**
     * 发送GET请求
     *
     * @param string $url 请求的URL
     * @param array $params 查询参数
     * @return string|false 请求响应，失败时返回false
     */
    public static function get(string $url, array $params = [])
    {
        $query = http_build_query($params);
        $fullUrl = $url . '?' . $query;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 注意：在生产环境中应验证SSL证书

        $response = curl_exec($ch);
        curl_close($ch);

        return $response ?: false;
    }

    /**
     * 发送POST请求
     *
     * @param string $url 请求的URL
     * @param array $data POST数据
     * @param array $headers 自定义头部信息，默认包含Content-Type为application/x-www-form-urlencoded
     * @return string|false 请求响应，失败时返回false
     */
    public static function post(string $url, array $data = [], array $headers = [])
    {
        $defaults = [
            'Content-Type: application/x-www-form-urlencoded',
        ];
        $headers = array_merge($defaults, $headers);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 注意：在生产环境中应验证SSL证书

        $response = curl_exec($ch);
        curl_close($ch);

        return $response ?: false;
    }
}
