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

namespace Modules\Admin\Services\excel;

use Modules\Admin\Services\BaseApiService;
use Services\Watermark;
use Services\WatermarkImg;
use App\Jobs\GenerateExcelJob;
use Illuminate\Http\Request;

class ExportService extends BaseApiService
{
    /**
     * 导出Excel
     * @return void
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function export(){
        date_default_timezone_set("Asia/Shanghai");

        $objPhpExcel = new \PHPExcel();

        // 设置 Excel 文件属性
        $objPhpExcel->getProperties()->setCreator("Me")
            ->setLastModifiedBy("Me")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        // 设置活动工作表
        $objPhpExcel->setActiveSheetIndex(0);
        $sheet = $objPhpExcel->getActiveSheet();

        // 填充示例数据
        $sheet->setCellValue('A1', 'Locked Cell');
        $sheet->setCellValue('B1', 'Unlocked Cell');
        $sheet->setCellValue('C1', 'Locked Cell');
        $sheet->setCellValue('D1', 'Locked Cell');
        $sheet->setCellValue('E1', 'Locked Cell');
        $sheet->setCellValue('F1', 'Locked Cell');
        $sheet->setCellValue('G1', 'Locked Cell');
        $sheet->setCellValue('H1', 'Locked Cell');
        for ($i = 2; $i <= 100; $i++) {
            $sheet->setCellValue('A' . $i, 'Locked ' . $i);
            $sheet->setCellValue('B' . $i, 'Unlocked ' . $i);
            $sheet->setCellValue('C' . $i, 'Locked ' . $i);
            $sheet->setCellValue('D' . $i, 'Locked ' . $i);
            $sheet->setCellValue('E' . $i, 'Locked ' . $i);
            $sheet->setCellValue('F' . $i, 'Locked ' . $i);
            $sheet->setCellValue('G' . $i, 'Locked ' . $i);
            $sheet->setCellValue('H' . $i, 'Locked ' . $i);
        }

        // 保护整个工作表
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('123456');

        // 解锁特定列（例如，列 B）
        foreach (range(1, 7) as $row) {
            $sheet->getStyle('B' . $row)->getProtection()->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        }

        $filePath = public_path() . "/uploads/" . "protected_excel_test.xlsx";
        $objWriter = \PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
        $objWriter->save($filePath);

        $water = new Watermark($filePath);
        //生成水印图片
        [$status,$msg,$imgPath] = WatermarkImg::create(["某某广告运营管理系统", date("Y年m月d日 H时i分s秒")],'#FED8D8',public_path() . '/font.ttf',public_path() . "/uploads/");
        if(!$status){
            die($msg);
        }
        $num = $water->addImage($imgPath);
        $water->getSheet(1)->setBgImg($num);
        $water->close();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="protected_excel_test.xlsx"');
        header('Cache-Control: max-age=0');

        readfile($filePath);

        unlink($filePath);
        unlink($imgPath);
        exit;
    }

    /**
     * php artisan make:job GenerateExcelJob
     *
     * & "E:\Software\phpStudy_V8\Extensions\php\php7.4.3nts\php.exe" "E:\Software\phpStudy_V8\Extensions\composer2.5.8\composer.phar" config -g repo.packagist composer https://mirrors.aliyun.com/composer/
     *
     * & "E:\Software\phpStudy_V8\Extensions\php\php7.4.3nts\php.exe" "E:\Software\phpStudy_V8\Extensions\composer2.5.8\composer.phar" require maatwebsite/excel
     *
     * E:\Software\phpStudy_V8\Extensions\php\php7.4.3nts\php artisan migrate
     *
     * E:\Software\phpStudy_V8\Extensions\php\php7.4.3nts\php artisan queue:work
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportMulti(Request $request)
    {
        ini_set('memory_limit', '8096M');
        // 调度队列作业
        $defaultLines = $request->input('lines', 20000); // 默认20000行
        $defaultName = $request->input('name', 'protected_excel_'.$defaultLines . '.xlsx'); // 默认20000行
        GenerateExcelJob::dispatch($defaultLines,$defaultName);

        return response()->json(['message' => 'Excel generation job has been dispatched']);
    }

    public function downloadMulti(Request $request)
    {
        ini_set('memory_limit', '8096M');
        $defaultLines = $request->input('lines', 1000); // 默认20000行
        $defaultName = $request->input('name', 'protected_excel_'.$defaultLines . '.xlsx'); // 默认20000行
        $filePath = storage_path("app/public/$defaultName");

        if (file_exists($filePath)) {
            return response()->download($filePath)->deleteFileAfterSend(true);
        } else {
            return response()->json(['message' => 'File not found'], 404);
        }
    }

}
