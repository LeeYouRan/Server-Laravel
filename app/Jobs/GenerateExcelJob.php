<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Protection;
use Services\Watermark;
use Services\WatermarkImg;
use Illuminate\Support\Facades\Storage;

class GenerateExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $defaultLines;
    protected $defaultName;

    public $timeout = 600; // 设置超时时间为10分钟
    public $tries = 5; // 设置最大重试次数为5次

    public function __construct($defaultLines, $defaultName)
    {
        $this->defaultLines = $defaultLines;
        $this->defaultName = $defaultName;
    }

    public function handle()
    {
        $defaultLines = $this->defaultLines;
        $defaultName = $this->defaultName;

        date_default_timezone_set("Asia/Shanghai");

        $objPhpExcel = new PHPExcel();

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

        // 设置列头
        $sheet->setCellValue('A1', 'Locked Cell');
        $sheet->setCellValue('B1', 'Unlocked Cell');
        $sheet->setCellValue('C1', 'Locked Cell');
        $sheet->setCellValue('D1', 'Locked Cell');
        $sheet->setCellValue('E1', 'Locked Cell');
        $sheet->setCellValue('F1', 'Locked Cell');
        $sheet->setCellValue('G1', 'Locked Cell');
        $sheet->setCellValue('H1', 'Locked Cell');

        // 分块处理数据
        $chunkSize = 1000;
        for ($offset = 2; $offset <= $defaultLines; $offset += $chunkSize) {
            $this->processChunk($sheet, $offset, min($chunkSize, $defaultLines - $offset + 1));
        }

        // 保护整个工作表
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('123456');

        // 解锁特定列（例如，列 B）
        for ($i = 1; $i <= $defaultLines; $i++) {
            $sheet->getStyle('B' . $i)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        }

        // 文件保存路径
        $filePath = storage_path("app/public/$defaultName");

        $objWriter = PHPExcel_IOFactory::createWriter($objPhpExcel, 'Excel2007');
        $objWriter->save($filePath);

        // 生成水印图片
        $water = new Watermark($filePath);
        [$status, $msg, $imgPath] = WatermarkImg::create(
            ["某某广告运营管理系统", date("Y年m月d日 H时i分s秒")],
            '#FED8D8',
            public_path() . '/font.ttf',
            storage_path("app/public/")
        );
        if (!$status) {
            die($msg);
        }
        $num = $water->addImage($imgPath);
        $water->getSheet(1)->setBgImg($num);
        $water->close();
        unlink($imgPath);
    }

    private function processChunk($sheet, $offset, $chunkSize)
    {
        for ($i = 0; $i < $chunkSize; $i++) {
            $rowIndex = $offset + $i;
            $sheet->setCellValue('A' . $rowIndex, 'Locked ' . $rowIndex);
            $sheet->setCellValue('B' . $rowIndex, 'Unlocked ' . $rowIndex);
            $sheet->setCellValue('C' . $rowIndex, 'Locked ' . $rowIndex);
            $sheet->setCellValue('D' . $rowIndex, 'Locked ' . $rowIndex);
            $sheet->setCellValue('E' . $rowIndex, 'Locked ' . $rowIndex);
            $sheet->setCellValue('F' . $rowIndex, 'Locked ' . $rowIndex);
            $sheet->setCellValue('G' . $rowIndex, 'Locked ' . $rowIndex);
            $sheet->setCellValue('H' . $rowIndex, 'Locked ' . $rowIndex);
        }
    }
}
