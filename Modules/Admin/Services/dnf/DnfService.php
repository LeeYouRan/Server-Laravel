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


    private function handleRequest($urlKey, $method, $params)
    {
        $url = env("DNFGM_URL") . env("DNFGM_{$urlKey}");
        $response = $method === 'GET' ? HttpRequest::get($url, $params) : HttpRequest::post($url, $params);
        $decodedResponse = json_decode($response, true);
        if ($response !== false) {
            return $this->apiSuccess($this->getter($decodedResponse, 'msg'), $this->getter($decodedResponse, 'data'), $this->getter($decodedResponse, 'code'));
        } else {
            return $this->apiError($this->getter($decodedResponse, 'msg'), $this->getter($decodedResponse, 'code'));
        }
    }

    public function subList()
    {
        $params = request()->all();
        return $this->handleRequest('SUB_URL', 'GET', $params);
    }

    public function propList()
    {
        $params = request()->all();
        return $this->handleRequest('PROP_URL', 'GET', $params);
    }

    public function propNum()
    {
        $params = request()->all();
        return $this->handleRequest('SEND_URL', 'POST', $params);
    }

}
