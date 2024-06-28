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

    /**
     * @param $line1 string 水印标题 "慧川广告运营管理系统"
     * @param $line2 string 水印时间 date("Y年m月d日 H时i分s秒")
     * @param $width int 背景图片宽度 800
     * @param $height int 背景图片高度 600
     * @param $fontColorHex string 水印文字颜色 "#FF6666"
     * @param $fontPath string 字体文件路径 public_path() . '/font.ttf'
     * @param $fontSize int 初始化字体大小 20
     * @param $angle int 文字倾斜角度 30
     * @param $spacing int 文字之间的间距 40
     * @param $saveFolderPath string 水印临时保存路径  public_path() . "/uploads/"
     * @return array
     */
    public static function createDense($line1 = "慧川广告运营管理系统", $line2 = "", $fontColorHex = '#FF6666', $fontPath = '', $saveFolderPath = '', $width = 800, $height = 600, $fontSize = 20, $angle = 30, $spacing = 40)
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

        // 计算每个水印块的宽高，减少间距以增加密集度
        $blockWidth = $textWidth1 + $spacing * 3;
        $blockHeight = $textHeight1 + $textHeight2 + $spacing * 3;

        // 计算水印块在画布上均匀分布的行列数
        $rows = ceil(($height + $blockHeight) / $blockHeight);
        $cols = ceil(($width + $blockWidth) / $blockWidth);

        // 确保每个水印块都在画布内
        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                // 计算当前水印块的左上角位置，使其按照倾斜角度排列
                $x = $col * $blockWidth - ($row * $blockHeight * tan(deg2rad($angle)));
                $y1 = $row * $blockHeight;
                $y2 = $y1 + $textHeight2 + $spacing;

                // 计算文字四个角的坐标，确保文字不超出画布边界
                $points1 = imagettfbbox($fontSize, $angle, $fontPath, $line1);
                $points2 = imagettfbbox($fontSize, $angle, $fontPath, $line2);
                $xPoints1 = array($points1[0] + $x, $points1[2] + $x, $points1[4] + $x, $points1[6] + $x);
                $yPoints1 = array($points1[1] + $y1, $points1[3] + $y1, $points1[5] + $y1, $points1[7] + $y1);
                $xPoints2 = array($points2[0] + $x, $points2[2] + $x, $points2[4] + $x, $points2[6] + $x);
                $yPoints2 = array($points2[1] + $y2, $points2[3] + $y2, $points2[5] + $y2, $points2[7] + $y2);

                // 确保所有点都在画布内
                $valid = true;
                foreach ($xPoints1 as $xp) {
                    if ($xp < 0 || $xp > $width) {
                        $valid = false;
                        break;
                    }
                }
                foreach ($yPoints1 as $yp) {
                    if ($yp < 0 || $yp > $height) {
                        $valid = false;
                        break;
                    }
                }
                foreach ($xPoints2 as $xp) {
                    if ($xp < 0 || $xp > $width) {
                        $valid = false;
                        break;
                    }
                }
                foreach ($yPoints2 as $yp) {
                    if ($yp < 0 || $yp > $height) {
                        $valid = false;
                        break;
                    }
                }

                // 如果所有点都在画布内，则绘制文字
                if ($valid) {
                    imagettftext($image, $fontSize, $angle, $x, $y1, $fontColor, $fontPath, $line1);
                    imagettftext($image, $fontSize, $angle, $x, $y2, $fontColor, $fontPath, $line2);
                }
            }
        }


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
