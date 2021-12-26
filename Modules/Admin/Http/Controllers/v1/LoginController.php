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
 * @Name 用户登录
 * @Description
 * @Auther Winston
 * @Date 2021/12/26 13:10
 */

namespace Modules\Admin\Http\Controllers\v1;


use Modules\Admin\Http\Requests\LoginRequest;
use Modules\Admin\Services\auth\LoginService;
class LoginController extends BaseApiController
{
    /**
     * @name 用户登录
     * @description
     * @author Winston
     * @date 2021/12/26 11:15
     * @param data  Array 用户信息
     * @param data.username String 账号
     * @param data.password String 密码
     * @return JSON
     **/

    /**
     * @OA\Post(path="/api/v1/admin/login/login",
     *   tags={"用户登录"},
     *   summary="用户登录",
     *   @OA\Parameter(name="apikey", in="header", description="apiKey", @OA\Schema(type="string")),
     *   @OA\Parameter(name="username", in="query", description="用户名", @OA\Schema(type="string")),
     *   @OA\Parameter(name="password", in="query", description="密码", @OA\Schema(type="string")),
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function login(LoginRequest $request)
    {
        return (new LoginService())->login($request->only(['username','password']));
    }
}
