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
        for ($i = 2; $i <= 7; $i++) {
            $sheet->setCellValue('A' . $i, 'Locked ' . $i);
            $sheet->setCellValue('B' . $i, 'Unlocked ' . $i);
        }

        // 保护整个工作表
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('123456');

        // 解锁特定列（例如，列 B）
        foreach (range(1, 7) as $row) {
            $sheet->getStyle('B' . $row)->getProtection()->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        }

        $filePath = public_path() . "/uploads/" . "protected_excel.xlsx";
        $objWriter = \PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
        $objWriter->save($filePath);

        $water = new Watermark($filePath);
        //生成水印图片
        [$status,$msg,$imgPath] = WatermarkImg::createDense("慧川广告运营管理系统",date("Y年m月d日 H时i分s秒"),'#FED8D8',public_path() . '/font.ttf',public_path() . "/uploads/");
        if(!$status){
            die($msg);
        }
        $num = $water->addImage($imgPath);
        $water->getSheet(1)->setBgImg($num);
        $water->close();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="protected_excel.xlsx"');
        header('Cache-Control: max-age=0');

        readfile($filePath);

        unlink($filePath);
        unlink($imgPath);
        exit;
    }

}
