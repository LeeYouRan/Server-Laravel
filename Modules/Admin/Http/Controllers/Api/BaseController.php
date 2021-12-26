<?php

namespace Modules\Admin\Http\Controllers\Api;

use Illuminate\Routing\Controller;
/**
 * @OA\Info(
 *     title="Auth api",
 *     version="0.0.1"
 * )
 */
class BaseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/api/index",
     *     @OA\Response(response="200", description="Display a listing of projects.")
     * )
     */
    public function index()
    {
        dd('Routing test success.');
    }

}
