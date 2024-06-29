<?php

namespace Services;

/**
 * 生成水印图片
 *
 * @example 使用示例：
 *          WatermarkImg::create(['Sample Watermark Line 1', 'Sample Watermark Line 2'],'#FF6666','/path/to/font.ttf','/path/to/save/folder',3500,2400,20,30,1,130);
 */
class WatermarkImg
{

    /**
     * 创建水印图片
     *
     * @param array $lines 需要显示的水印文字行数组
     * @param string $fontColorHex 水印文字的颜色（十六进制表示）
     * @param string $fontPath 字体文件的路径
     * @param string $saveFolderPath 保存水印图片的文件夹路径
     * @param int $width 生成水印图片的宽度
     * @param int $height 生成水印图片的高度
     * @param int $fontSize 水印文字的初始字体大小
     * @param int $angle 水印文字的旋转角度
     * @param int $spacingDiagonal 倾斜方向上的间距
     * @param int $spacingVertical 垂直方向上的间距
     *
     * @return array 返回包含生成结果的数组：成功与否、错误信息、保存路径
     *
     * @example
     * create(
     *     ['Sample Watermark Line 1', 'Sample Watermark Line 2'],
     *     '#FF6666',
     *     '/path/to/font.ttf',
     *     '/path/to/save/folder',
     *     3500,
     *     2400,
     *     20,
     *     30,
     *     1,
     *     130
     * );
     */
    public static function create(
        $lines = [],
        $fontColorHex = '#FF6666',
        $fontPath = '',
        $saveFolderPath = '',
        $width = 3500,
        $height = 2400,
        $fontSize = 20,
        $angle = 30,
        $spacingDiagonal = 1, // 倾斜方向上的间距
        $spacingVertical = 130 // 垂直方向上的间距
    ) {
        // 确保提供的 lines 数组不为空
        if (empty($lines)) {
            return [false, "水印文本参数不能为空", null];
        }

        // 创建一个透明图像
        $image = imagecreatetruecolor($width, $height);

        // 设置背景为透明并开启 alpha 通道
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $transparent);

        // 设置文字颜色
        $fontColor = sscanf($fontColorHex, "#%02x%02x%02x");
        $fontColor = imagecolorallocate($image, ...$fontColor);

        // 检查字体文件是否存在
        if (!file_exists($fontPath)) {
            return [false, "生成水印图片所需字体文件不存在", $fontPath];
        }

        // 动态调整字体大小，确保两行文字都能适应图片
        do {
            $fontSize--;
            $textBox = [];
            $textWidth = 0;
            $textHeight = 0;
            foreach ($lines as $line) {
                // 计算每行文字的边界框，并更新文本宽度和高度
                $textBox[] = imagettfbbox($fontSize, $angle, $fontPath, $line);
                $textWidth = max($textWidth, $textBox[count($textBox) - 1][2] - $textBox[count($textBox) - 1][0]);
                $textHeight += abs($textBox[count($textBox) - 1][7] - $textBox[count($textBox) - 1][1]);
            }
        } while ($textWidth > $width || $textHeight * count($lines) > $height);

        // 计算每个水印块的宽高
        $blockWidth = $textWidth + $spacingDiagonal;
        $blockHeight = $textHeight * count($lines) + $spacingDiagonal;

        // 计算水印块在画布上均匀分布的行列数
        $rows = ceil(($height + $blockHeight) / ($blockHeight + $spacingVertical));
        $cols = ceil(($width + $blockWidth) / ($blockWidth));

        // 确保每个水印块都在画布内
        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                // 计算当前水印块的左上角位置，使其按照倾斜角度排列
                $x = $col * $blockWidth - ($row * $blockHeight * tan(deg2rad($angle)));
                $y = $row * ($blockHeight + $spacingVertical);

                // 计算文字四个角的坐标，确保文字不超出画布边界
                $valid = true;
                foreach ($textBox as $box) {
                    $xPoints = array($box[0] + $x, $box[2] + $x, $box[4] + $x, $box[6] + $x);
                    $yPoints = array($box[1] + $y, $box[3] + $y, $box[5] + $y, $box[7] + $y);
                    foreach ($xPoints as $xp) {
                        if ($xp < 0 || $xp > $width) {
                            $valid = false;
                            break;
                        }
                    }
                    foreach ($yPoints as $yp) {
                        if ($yp < 0 || $yp > $height) {
                            $valid = false;
                            break;
                        }
                    }
                    if (!$valid) break;
                    $y += $textHeight; // 下一行文字的 y 坐标
                }

                // 如果所有点都在画布内，则绘制文字
                if ($valid) {
                    $y = $row * ($blockHeight + $spacingVertical); // 重置 y 坐标
                    foreach ($lines as $line) {
                        imagettftext($image, $fontSize, $angle, $x, $y, $fontColor, $fontPath, $line);
                        $y += $textHeight;
                    }
                }
            }
        }

        // 设置默认保存路径
        if (!$saveFolderPath) {
            $saveFolderPath = public_path() . "/uploads/";
        }

        // 确保保存目录存在
        if (!file_exists($saveFolderPath) && !mkdir($saveFolderPath, 0777, true)) {
            return [false, "水印图片临时保存目录不存在且无法创建", $saveFolderPath];
        }

        // 指定保存图片的路径
        $timestamp = date("YmdHis");
        $fileName = "watermark_{$timestamp}.png";
        $savePath = $saveFolderPath . $fileName;

        // 保存图片为PNG到指定路径
        if (!imagepng($image, $savePath)) {
            return [false, "无法保存水印图片", $savePath];
        }

        // 释放内存
        imagedestroy($image);

        return [true, '', $savePath];
    }


}
