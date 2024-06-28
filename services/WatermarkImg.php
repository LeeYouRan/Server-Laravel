<?php

namespace Services;

/**
 * 生成水印图片
 *
 * @example 使用示例：
 *          WatermarkImg::create("慧川广告运营管理系统",date("Y年m月d日 H时i分s秒"),'#FF6666',SpringConstant::WEBAPP_PATH . '/font.ttf',SpringConstant::WEBAPP_PATH . "/uploads/");
 */
class WatermarkImg
{

    /**
     * @param $line1 string 水印标题 "慧川广告运营管理系统"
     * @param $line2 string 水印时间 date("Y年m月d日 H时i分s秒")
     * @param $width int 背景图片宽度 400
     * @param $height int 背景图片高度 300
     * @param $fontColorHex string 水印文字颜色 "#FF6666"
     * @param $fontPath string 字体文件路径 public_path() . '/font.ttf'
     * @param $fontSize int 初始化字体大小 10
     * @param $angle int 文字倾斜角度 30
     * @param $spacing int 文字之间的间距 20
     * @param $saveFolderPath string 水印临时保存路径  public_path() . "/uploads/"
     * @return array
     */
    public static function create($line1 = "慧川广告运营管理系统", $line2 = "", $fontColorHex = '#FF6666', $fontPath = '', $saveFolderPath = '', $width = 400, $height = 300, $fontSize = 10, $angle = 30, $spacing = 20)
    {
        // 获取水印时间
        if (!$line2) {
            $line2 = date("Y年m月d日 H时i分s秒");
        }
        // 创建一个透明图像
        $image = imagecreatetruecolor($width, $height);

        // 设置背景为透明并开启 alpha 通道
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127); // 透明度设为127，即半透明
        imagefill($image, 0, 0, $transparent);

        // 设置文字颜色
        $fontColor = sscanf($fontColorHex, "#%02x%02x%02x");
        $fontColor = imagecolorallocate($image, ...$fontColor);

        // 选择字体文件，确保字体文件路径正确
        if (!file_exists($fontPath)) {
            return [false, "生成水印图片所需字体文件不存在", $fontPath];
        }

        // 动态调整字体大小，确保两行文字都能适应图片
        do {
            $fontSize--;
            $textBox1 = imagettfbbox($fontSize, $angle, $fontPath, $line1);
            $textBox2 = imagettfbbox($fontSize, $angle, $fontPath, $line2);
            $textWidth1 = max([$textBox1[2] - $textBox1[0], $textBox1[4] - $textBox1[6]]);
            $textHeight1 = abs($textBox1[7] - $textBox1[1]);
            $textWidth2 = max([$textBox2[2] - $textBox2[0], $textBox2[4] - $textBox2[6]]);
            $textHeight2 = abs($textBox2[7] - $textBox2[1]);
        } while ($textWidth1 > $width || $textHeight1 + $textHeight2 > $height);

        // 计算文字位置，使文字的头部对齐
        $x = ($width - max($textWidth1, $textWidth2)) / 2;
        $y1 = ($height - ($textHeight1 + $textHeight2 + $spacing)) / 2 + $textHeight1;
        $y2 = $y1 + $textHeight2 + $spacing;

        // 倾斜度显示文字
        imagettftext($image, $fontSize, $angle, $x, $y1, $fontColor, $fontPath, $line1);
        imagettftext($image, $fontSize, $angle, $x, $y2, $fontColor, $fontPath, $line2);

        if (!$saveFolderPath) {
            $saveFolderPath = public_path() . "/uploads/";
        }

        // 指定保存图片的路径
        $timestamp = date("YmdHis");
        $fileName = "watermark_{$timestamp}.png";
        $savePath = $saveFolderPath . $fileName;

        // 保存图片为PNG到指定路径
        imagepng($image, $savePath);

        // 释放内存
        imagedestroy($image);

        return [true, '', $savePath];
    }


}
