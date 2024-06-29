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
 * @Name 报表控制器
 * @Description
 * @Auther Winston
 * @Date 2021/12/26 13:10
 */

namespace Modules\Admin\Http\Controllers\v1;


use Modules\Admin\Http\Requests\LoginRequest;
use Modules\Admin\Services\excel\ExportService;
use Services\WatermarkImg;

class ExcelController extends BaseApiController
{

    /**
     * @OA\Get(path="/api/excel/export",
     *   tags={"导出受保护且带水印的excel"},
     *   summary="导出受保护且带水印的excel",
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function export()
    {
        return (new ExportService())->export();
    }

    /**
     * @OA\Get(path="/api/excel/watermark",
     *   tags={"导出受保护且带水印的excel"},
     *   summary="水印图片生成",
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function watermark()
    {
        return WatermarkImg::create(["某某管理系统", date("Y年m月d日 H时i分s秒")],'#FED8D8',public_path('font.ttf'));
    }
}
