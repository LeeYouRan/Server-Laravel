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
 * @Name 导出Excel
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

    // 一级类型数组
    protected static $firstTypeOptions =
        [
            '' => '选择一级类型',
            '1' => '材料',
            '39' => '装备',
            '23' => '消耗品',
            '31' => '时装礼包',
        ];

    // 二级类型数组
    protected static $secondTypeOptions =
        [
            '' => '选择二级类型',
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
            '' => '选择等级',
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

    /**
     * 封装接口返回
     * @param $urlKey
     * @param $method
     * @param $params
     * @return \Modules\Common\Services\JSON
     * @throws \Modules\Common\Exceptions\ApiException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function handleRequest($urlKey, $method, $params)
    {
        $call = $this->getter($params,'call');
        // 构造完整URL并发起请求
        $url = env("DNFGM_URL") . env("DNFGM_{$urlKey}");
        $response = $method === 'GET' ? HttpRequest::get($url, $params) : HttpRequest::post($url, $params);
        // 处理请求失败的情况
        if ($response === false) {
            return $this->apiError(MessageData::BAD_REQUEST);
        }
        // 解码响应
        $decodedResponse = json_decode($response, true);
        // 请求成功，返回成功响应
        if($call){
            return $this->getter($decodedResponse,'data');
        }
        return $this->apiSuccess($this->getter($decodedResponse,'msg'), $this->getter($decodedResponse,'data'), $this->getter($decodedResponse,'code'));
    }

    /**
     * 根据搜索内容模糊匹配返回符合条件的数据
     * @param array $dataArray
     * @param string $searchTerm
     * @return array
     */
    function searchTitlesWithMultipleChars(array $dataArray, string $searchTerms): array {
        $return = []; // 初始化一个空数组来保存匹配的数据
        $searchTermsArray = preg_split('//u', $searchTerms, -1, PREG_SPLIT_NO_EMPTY); // 分割搜索词为单个字符数组，支持Unicode（适用于中文）
        // 遍历二维数组
        foreach ($dataArray as $item) {
            $title = $this->getter($item, 'title'); // 获取数组中的值
            $matchFound = false; // 标记是否找到匹配
            // 遍历分割后的搜索词字符
            foreach ($searchTermsArray as $char) {
                // 使用stripos进行模糊匹配，检查字符是否在$title中
                if (stripos($title, $char) !== false) {
                    $matchFound = true;
                    break; // 一旦找到匹配的字符，即可跳出循环
                }
            }
            // 如果当前item的title中包含所有搜索字符中的至少一个，则添加到结果数组中
            if ($matchFound) {
                $return[] = $item;
            }
        }
        // 返回所有匹配的数据
        return $return;
    }


    /**
     * 子列表
     * @return \Modules\Common\Services\JSON
     * @throws \Modules\Common\Exceptions\ApiException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function subList()
    {
        $params = request()->all();
        return $this->handleRequest('SUB_URL', 'GET', $params);
    }

    /**
     * 物品列表
     * @return \Modules\Common\Services\JSON
     * @throws \Modules\Common\Exceptions\ApiException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function propList()
    {
        $params = request()->all();
        return $this->handleRequest('PROP_URL', 'GET', $params);
    }

    /**
     * 物品发送
     * @return \Modules\Common\Services\JSON
     * @throws \Modules\Common\Exceptions\ApiException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function propNum()
    {
        $params = request()->all();
        return $this->handleRequest('SEND_URL', 'POST', $params);
    }

    /**
     * 批量发送物品
     * @return \Modules\Common\Services\JSON
     * @throws \Modules\Common\Exceptions\ApiException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function multiSend()
    {
        // 获取请求参数，预先设定默认值以减少getter调用
        $params = request()->all();
        $typeId = $params['type_id'] ?? null;
        $filter = $params['filter'] ?? '';
        $params['call'] = true; // 更直观的布尔值
        // 验证必要参数的存在
        if (!$typeId) {
            return $this->apiError('Type ID is required.');
        }
        // 获取物品列表
        $items = $this->handleRequest('PROP_URL', 'GET', $params);
        // 根据筛选条件过滤物品
        if ($filter) {
            $items = $this->searchTitlesAndReturnIds($items, $filter);
        }
        // 准备发送数据的数组
        $responses = [];
        foreach ($items as $item) {
            // 设置物品ID和数量
            $sendParams = [
                'id' => $item['id'],
                'num' => in_array($typeId, [1, 23]) ? ($params['num'] ?? 99999) : ($params['num'] ?? 1),
            ];
            // 发送物品
            $responses[] = $this->handleRequest('SEND_URL', 'POST', array_merge($params, $sendParams));
            // 间隔执行，防止并发问题（可根据实际情况调整）
            usleep(1000000); // 等待1秒，单位为微秒
        }
        // 返回成功的API响应
        return $this->apiSuccess(MessageData::Ok, $responses);
    }


}
