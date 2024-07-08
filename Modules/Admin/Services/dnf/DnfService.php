<?php
// +----------------------------------------------------------------------
// | Name: 管理系统 [ 为了快速搭建软件应用而生的，希望能够帮助到大家提高开发效率。 ]
// +----------------------------------------------------------------------
// | Copyright: (c) 2021~2022 https://www.liyouran.top All rights reserved.
// +----------------------------------------------------------------------
// | Licensed: 这是一个自由软件，允许对程序代码进行修改，但希望您留下原有的注释。
// +----------------------------------------------------------------------
// | Author: Winston <liyouran@live.com>
// +----------------------------------------------------------------------
// | Version: V1
// +----------------------------------------------------------------------

/**
 * @Name DNF服务
 * @Description
 * @Auther Winston
 * @Date 2021/12/26 11:15
 */

namespace Modules\Admin\Services\dnf;

use Modules\Admin\Services\BaseApiService;
use Modules\Common\Exceptions\MessageData;
use Services\HttpRequest;

class DnfService extends BaseApiService
{
    // 常量定义：最大限制和默认延时
    protected const MAX_LIMIT = 99999;

    protected const DEFAULT_DELAY = 1000000 / 200; // 5ms delay

    protected const TIME_OUT_LIMIT = 60000; //1min

    // 一级类型数组
    protected static $firstTypeOptions =
        [
            '1' => '材料',
            '39' => '装备',
            '23' => '消耗品',
            '31' => '时装礼包',
        ];

    // 二级类型数组
    protected static $secondTypeOptions =
        [
            '4' => '普通',
            '43' => '辅助装备',
            '42' => '首饰',
            '41' => '防具',
            '40' => '武器',
            '38' => '防具',
            '12' => '武器',
            '36' => '首饰',
            '13' => '布甲',
            '14' => '皮甲',
            '15' => '轻甲',
            '16' => '重甲',
            '17' => '板甲',
            '37' => '辅助装备',
            '19' => '项链',
            '20' => '手镯',
            '21' => '戒指',
            '22' => '特殊装备',
            '24' => '能量核心',
            '25' => '道具',
            '26' => '时装',
            '27' => '宠物',
            '28' => '药剂',
            '29' => '徽章',
            '30' => '卡片',
            '32' => '武器皮肤',
            '33' => '光环',
            '34' => '称号',
            '35' => '符石',
        ];

    // 等级数组
    protected static $gradeOptions =
        [
            '65' => '65',
            '60' => '60',
            '55' => '55',
            '50' => '50',
            '45' => '45',
            '40' => '40',
            '35' => '35',
            '30' => '30',
            '25' => '25',
            '20' => '20',
            '15' => '15',
            '10' => '10',
            '5' => '5',
            '1' => '1',
        ];

    //页码数组
    protected static $limitOptions =
        [
            '10' => '10',
            '20' => '20',
            '30' => '30',
            '40' => '40',
            '50' => '50',
            '60' => '60',
            '70' => '70',
            '80' => '80',
            '90' => '90',
            '100' => '100',
            '500' => '500',
            '1000' => '1000',
        ];

    /**
     * 处理HTTP请求的私有方法。
     *
     * @param string $urlKey API路径的键
     * @param string $method HTTP方法（GET或POST）
     * @param array $params 请求参数
     * @return mixed API响应结果
     */
    private function handleRequest($urlKey, $method, $params = [])
    {
        // 获取当前时间戳
        $start = $this->getCurrentMillisecondsTimestamp();
        $call = $this->getter($params, 'call');
        $filter = $this->getter($params, 'filter');
        if ($filter) {
            $params['page'] = 1;
            $params['limit'] = self::MAX_LIMIT;
        }

        // 构建URL
        $url = env("DNFGM_URL") . env("DNFGM_{$urlKey}");
        // 根据HTTP方法进行请求
        $response = $method === 'GET' ? HttpRequest::get($url, $params) : HttpRequest::post($url, $params);

        // 请求失败时返回错误
        if ($response === false) {
            return $this->apiError(MessageData::BAD_REQUEST);
        }

        // 解析响应数据
        $decodedResponse = json_decode($response, true);

        // 处理过滤逻辑
        if (!$call && $filter) {
            $decodedResponse['data'] =  $this->searchTitlesWithMultipleChars($this->getter($decodedResponse, 'data'), $filter);
            $decodedResponse['count'] = count($this->getter($decodedResponse, 'data'));
        }

        // 请求成功，返回成功响应
        if($call){
            return $this->getter($decodedResponse,'data');
        }

        // 获取响应消息、数据和状态码
        $msg = $this->getter($decodedResponse, 'msg', MessageData::Ok);
        $data = $this->getter($decodedResponse, 'count') ? ['list' => $this->getter($decodedResponse, 'data'), 'count' => $this->getter($decodedResponse, 'count')] : $this->getter($decodedResponse, 'data');
        $code = $this->getter($decodedResponse, 'code');
        $end = $this->getCurrentMillisecondsTimestamp();
        $calc = $this->calculateTimeDifference($start, $end);

        // 返回成功响应
        return $this->apiSuccess($msg . $calc, $data, $code);
    }

    /**
     * 根据多个字符搜索标题的私有方法。
     *
     * @param array $dataArray 数据数组
     * @param string $searchTerms 搜索字符
     * @return array 过滤后的数据数组
     */
    private function searchTitlesWithMultipleChars(array $dataArray, string $searchTerms)
    {
        $return = [];
        $searchTermsArray = preg_split('//u', $searchTerms, -1, PREG_SPLIT_NO_EMPTY);
        if ($dataArray) {
            foreach ($dataArray as $item) {
                $title = $this->getter($item, 'title');
                $matchFound = false;
                foreach ($searchTermsArray as $char) {
                    if (stripos($title, $char) !== false) {
                        $matchFound = true;
                        break;
                    }
                }
                if ($matchFound) {
                    $return[] = $item;
                }
            }
        }
        return $return;
    }

    /**
     * 返回枚举选项的公共方法。
     *
     * @return mixed API响应结果
     */
    public function enum()
    {
        $data = [
            'firstTypeOptions' => self::$firstTypeOptions,
            'secondTypeOptions' => self::$secondTypeOptions,
            'gradeOptions' => self::$gradeOptions,
            'limitOptions' => self::$limitOptions,
        ];
        return $this->apiSuccess(MessageData::Ok, $data);
    }

    /**
     * 获取子列表的公共方法。
     *
     * @return mixed API响应结果
     */
    public function subList()
    {
        $params = request()->all();
        return $this->handleRequest('SUB_URL', 'GET', $params);
    }

    /**
     * 获取道具列表的公共方法。
     *
     * @return mixed API响应结果
     */
    public function propList()
    {
        $params = request()->all();
        return $this->handleRequest('PROP_URL', 'GET', $params);
    }

    /**
     * 获取道具数量的公共方法。
     *
     * @return mixed API响应结果
     */
    public function propNum()
    {
        $params = request()->all();
        return $this->handleRequest('SEND_URL', 'POST', $params);
    }

    /**
     * 批量发送物品的公共方法。
     *
     * @return mixed API响应结果
     */
    public function multiSend()
    {
        $start = $this->getCurrentMillisecondsTimestamp();
        $params = request()->all();
        $typeId = $params['type_id'] ?? null;
        $filter = $params['filter'] ?? '';
        $params['call'] = true;

        // 检查typeId是否存在
        if (!$typeId) {
            return $this->apiError('Type ID is required.');
        }

        // 获取物品列表
        $items = $this->handleRequest('PROP_URL', 'GET', $params);

        // 过滤物品列表
        if ($filter) {
            $items = $this->searchTitlesWithMultipleChars($items, $filter);
        }

        $responses = [];
        // 遍历物品列表并发送请求
        foreach ($items as $item) {
            $sendParams = [
                'id' => $item['id'],
                'num' => in_array($typeId, [1, 23]) ? ($params['num'] ?? self::MAX_LIMIT) : ($params['num'] ?? 1),
            ];
            $responses[] = $this->handleRequest('SEND_URL', 'POST', array_merge($params, $sendParams));
            if (!$this->break($start)) {
                return $this->apiError(MessageData::TIME_OUT);
            }
            usleep(self::DEFAULT_DELAY);
        }
        $end = $this->getCurrentMillisecondsTimestamp();
        $calc = $this->calculateTimeDifference($start, $end);
        return $this->apiSuccess(MessageData::Ok . $calc, $responses);
    }

    /**
     * 批量默认发送物品的公共方法。
     *
     * @return mixed API响应结果
     */
    public function multiDefaultSend()
    {
        $start = $this->getCurrentMillisecondsTimestamp();
        $params = request()->all();
        $params['call'] = true;
        $params['page'] = 1;
        $params['limit'] = self::MAX_LIMIT;

        $responses = [];
        // 依次调用multiDefaultSendCall方法并检查超时
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 1, 'start' => $start]));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 39, 'start' => $start, 'title' => '释魂']));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 39, 'start' => $start, 'title' => '镇魂']));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 39, 'start' => $start, 'title' => '源助力']));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 23, 'start' => $start]));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 31, 'start' => $start, 'title' => '全职业']));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 31, 'start' => $start, 'title' => '全体']));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 31, 'start' => $start, 'title' => '天空套第一期']));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 31, 'start' => $start, 'title' => '全套']));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 31, 'start' => $start, 'second_type_id' => 26]));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 31, 'start' => $start, 'second_type_id' => 32]));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 31, 'start' => $start, 'second_type_id' => 33]));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $responses[] = $this->multiDefaultSendCall(array_merge($params, ['type_id' => 31, 'start' => $start, 'second_type_id' => 34]));
        if (!$this->break($start)) {
            return $this->apiError(MessageData::TIME_OUT);
        }
        $end = $this->getCurrentMillisecondsTimestamp();
        $calc = $this->calculateTimeDifference($start, $end);
        return $this->apiSuccess(MessageData::Ok . $calc, $responses);
    }

    /**
     * 检查是否超时的保护方法。
     *
     * @param int $startTimestamp 开始时间戳
     * @param int|null $endTimestamp 结束时间戳
     * @return bool 是否在限定时间内
     * @throws \Exception 参数无效时抛出异常
     */
    protected function break($startTimestamp, $endTimestamp = null)
    {
        if (!is_numeric($startTimestamp)) {
            throw new \Exception('The start timestamp must be a valid number.');
        }
        if ($endTimestamp !== null && !is_numeric($endTimestamp)) {
            throw new \Exception('The end timestamp must be a valid number.');
        }
        if (!$endTimestamp) {
            $endTimestamp = $this->getCurrentMillisecondsTimestamp();
        }
        return $endTimestamp - $startTimestamp < self::TIME_OUT_LIMIT;
    }


    /**
     * 执行批量默认发送操作的公共方法。
     *
     * @param array $params 请求参数
     * @return mixed API响应结果
     */
    public function multiDefaultSendCall(array $params)
    {
        $responses = [];
        $start = $params['start'] ?? $this->getCurrentMillisecondsTimestamp();
        $typeId = $params['type_id'] ?? null;
        $filter = $params['filter'] ?? '';
        $params['call'] = true;

        // 检查typeId是否存在
        if (!$typeId) {
            return $this->apiError('Type ID is required.');
        }

        // 获取物品列表
        $items = $this->handleRequest('PROP_URL', 'GET', $params);

        // 过滤物品列表
        if ($filter) {
            $items = $this->searchTitlesWithMultipleChars($items, $filter);
        }

        // 遍历物品列表并发送请求
        foreach ($items as $item) {
            if($item['id'] !== 25){
                $sendParams = [
                    'id' => $item['id'],
                    'num' => in_array($typeId, [1, 23]) ? ($params['num'] ?? self::MAX_LIMIT) : ($params['num'] ?? 1),
                ];
                $responses[] = $this->handleRequest('SEND_URL', 'POST', array_merge($params, $sendParams));
                if (!$this->break($start)) {
                    return $this->apiError(MessageData::TIME_OUT);
                }
                usleep(self::DEFAULT_DELAY);
            }
        }
        return $responses;
    }

    /**
     * 获取毫秒时间戳
     * @return float
     */
    protected function getCurrentMillisecondsTimestamp() {
        // 获取当前时间戳（包括微秒），并转换为毫秒
        return round(microtime(true) * 1000);
    }

    /**
     * 计算耗时
     * @param $startTimestamp
     * @param $endTimestamp
     * @return string
     */
    private function calculateTimeDifference($startTimestamp, $endTimestamp) {
        // 计算时间差
        $difference = abs($endTimestamp - $startTimestamp);

        // 转换为分钟、秒、毫秒
        $minutes = intdiv($difference, 60000);
        $difference %= 60000;
        $seconds = intdiv($difference, 1000);
        $milliseconds = $difference % 1000;

        // 构建返回字符串
        $result = '';
        if ($minutes > 0) {
            $result .= $minutes . '分钟';
        }
        if ($seconds > 0) {
            $result .= $seconds . '秒';
        }
        if ($milliseconds > 0) {
            $result .= $milliseconds . '毫秒';
        }

        // 如果所有维度都是0，返回'0毫秒'
        if ($result === '') {
            $result = '0毫秒';
        }

        return '，耗时：' . $result;
    }

}
