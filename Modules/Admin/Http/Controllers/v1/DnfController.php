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
 * @Name DNF控制器
 * @Description
 * @Auther Winston
 * @Date 2021/12/26 13:10
 */

namespace Modules\Admin\Http\Controllers\v1;

use Modules\Admin\Services\dnf\DnfService;

class DnfController extends BaseApiController
{

    /**
     * @OA\Get(path="/api/dnf/subList",
     *   tags={"DNFM-GM"},
     *   summary="获取子类列表",
     *   @OA\Parameter(name="pid", in="query", description="父级id 1", @OA\Schema(type="string")),
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function subList()
    {
        return (new DnfService())->subList();
    }

    /**
     * @OA\Get(path="/api/dnf/propList",
     *   tags={"DNFM-GM"},
     *   summary="获取物品列表",
     *   @OA\Parameter(name="page", in="query", description="页码 1", @OA\Schema(type="string")),
     *   @OA\Parameter(name="limit", in="query", description="数量 10000", @OA\Schema(type="string")),
     *   @OA\Parameter(name="title", in="query", description="名称 点券", @OA\Schema(type="string")),
     *   @OA\Parameter(name="grade", in="query", description="等级 65", @OA\Schema(type="string")),
     *   @OA\Parameter(name="type_id", in="query", description="种类id 1 材料 39 装备 23 消耗品 31 时装礼包", @OA\Schema(type="string")),
     *   @OA\Parameter(name="second_type_id", in="query", description="子类id 25", @OA\Schema(type="string")),
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function propList()
    {
        return (new DnfService())->propList();
    }

    /**
     * @OA\Post(
     *   path="/api/dnf/propNum",
     *   tags={"DNFM-GM"},
     *   summary="发送物品",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="username", type="string", format="text", description="用户名"),
     *         @OA\Property(property="code", type="string", format="text", description="邀请码"),
     *         @OA\Property(property="id", type="string", format="text", description="物品ID"),
     *         @OA\Property(property="num", type="integer", format="int32", description="物品数量")
     *       )
     *     )
     *   ),
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function propNum()
    {
        return (new DnfService())->propNum();
    }

    /**
     * @OA\Post(
     *   path="/api/dnf/multiSend",
     *   tags={"DNFM-GM"},
     *   summary="批量发送物品",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="username", type="string", format="text", description="用户名"),
     *         @OA\Property(property="code", type="string", format="text", description="邀请码"),
     *         @OA\Property(property="id", type="string", format="text", description="物品ID"),
     *         @OA\Property(property="num", type="integer", format="int32", description="物品数量"),
     *         @OA\Property(property="page", type="integer", format="int32", description="页码 1"),
     *         @OA\Property(property="limit", type="integer", format="int32", description="数量 10000"),
     *         @OA\Property(property="title", type="string", format="text", description="物品名称"),
     *         @OA\Property(property="grade", type="integer", format="int32", description="等级"),
     *         @OA\Property(property="type_id", type="integer", format="int32", description="种类id 1 材料 39 装备 23 消耗品 31 时装礼包"),
     *         @OA\Property(property="second_type_id", type="integer", format="int32", description="子类id 25"),
     *         @OA\Property(property="filter", type="string", format="text", description="物品名称过滤")
     *       )
     *     )
     *   ),
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function multiSend(){
        return (new DnfService())->multiSend();
    }

}
