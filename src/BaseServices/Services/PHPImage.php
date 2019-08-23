<?php
namespace BaseServices\Services;

use Exception;

/**
 *
 * @author lin
 *        
 */
class PHPImage
{

    /**
     *
     * @var array
     * @access private
     */
    private $_images = array();

    private $imSources, $type = 'jpeg';

    private $dstW = 14, $dstH = 14;

    /**
     * 新建实例时检查该类操作需要使用的函数是否存在
     *
     * @throws Exception
     */
    public function __construct()
    {
        $required_functions = array(
            'getimagesize',
            'imagecreatefromstring',
            'imagecreatetruecolor',
            'imagecolortransparent',
            'imagecolorallocatealpha',
            'imagealphablending',
            'imagesavealpha',
            'imagesx',
            'imagesy',
            'imagecopyresampled'
        );

        foreach ($required_functions as $function) {
            function_exists($function) || $this->throwException("缺少 $function  函数,检查 gd 库是否启用.");
            // trigger_error("The class was unable to find the $function function, which is part of the GD library. Ensure that the system has the GD library installed and that PHP has access to it through a PHP interface, such as PHP's GD module. Since this function was not found, the library will be unable to create ICO files.");
        }
    }

    /**
     *
     * @param string $file
     *            文件路径
     * @throws Exception
     * @return PHPImage
     */
    public function setImageFile($file)
    {
        file_exists($file) || $this->throwException("文件 $file 不存在.");
        ($size = getimagesize($file)) && ($file_data = file_get_contents($file)) && ($im = imagecreatefromstring($file_data)) || $this->throwException("读取文件 $file 失败!");
        $this->imSources = $im;
        $this->type = str_replace(chr(47), '', strrchr($size['mime'], chr(47)));

        return $this;
    }

    /**
     *
     * @param
     *            gd resource $resource
     * @throws Exception
     * @return PHPImage
     */
    public function setImageSources($resource)
    {
        is_resource($resource) && (get_resource_type($resource) === 'gd') || $this->throwException('给定资源不符合要求');
        $this->imSources = $resource;
        return $this;
    }

    /**
     * 截取指定位置大小的图片,保存为指定大小的gd image resource
     *
     * @param number $srcX
     *            指定位置的x起点
     * @param number $srcY
     *            指定位置的y起点
     * @param number $dstW
     *            保存目标的宽度
     * @param number $dstH
     *            保存目标的高度
     * @param number $srcW
     *            源宽度
     * @param number $srcH
     *            源高度
     * @throws Exception
     * @return PHPImage
     */
    public function getImage($srcX = 0, $srcY = 0, $dstW = 0, $dstH = 0, $srcW = 0, $srcH = 0)
    {
        empty($this->imSources) && $this->throwException('请先添加操作的图片对象');
        $srcW = ($srcW === 0) ? imagesx($this->imSources) : $srcW;
        $srcH = ($srcH === 0) ? imagesy($this->imSources) : $srcH;

        $this->dstH = ($dstH === 0) ? $this->dstH : $dstH;
        $this->dstW = ($dstW === 0) ? $this->dstW : $dstW;
        $newIm = imagecreatetruecolor($this->dstW, $this->dstH);
        imagecolortransparent($newIm, imagecolorallocatealpha($newIm, 0, 0, 0, 127));
        imagealphablending($newIm, false);
        imagesavealpha($newIm, true);
        imagecopyresampled($newIm, $this->imSources, 0, 0, $srcX, $srcY, $this->dstW, $this->dstH, $srcW, $srcH) || $this->throwException('图片操作失败!');
        $this->imSources = $newIm;

        return $this;
    }

    /**
     * 显示图像
     *
     * @throws Exception
     */
    public function showImage()
    {
        empty($this->imSources) && $this->throwException('请先添加操作的图片对象');

        header('Content-Type: image/' . $this->type);
        $f = 'image' . $this->type;
        function_exists($f) && $f($this->imSources, null, 100) || $this->throwException('发生了一些错误!');
        // imagegd($this->imSources);
        // echo "<img src=$this->imSources>";
        return true;
    }

    /**
     * 保存文件
     *
     * @param string $file
     *            文件路径
     * @throws Exception
     * @return boolean
     */
    public function save($file)
    {
        empty($this->imSources) && $this->throwException('请先添加操作的图片对象');

        if (false === (($fh = fopen($file, 'w')) && (fclose($fh))))
            return false;
        $f = 'image' . $this->type;
        function_exists($f) && $f($this->imSources, $file) || $this->throwException('发生了一些错误!');

        imagedestroy($this->imSources);
        return true;
    }

    /**
     * 将设置好的gd image resource 转换为ico格式,存储到指定的文件中
     *
     * @param string $file
     *            保存文件路径
     * @param number $srcW
     * @param number $srcH
     * @return boolean
     */
    public function saveICO($file)
    {
        $this->_addImageData($this->imSources);
        if (false === ($data = $this->_getIcoData()))
            return false;
        if (false === ($fh = fopen($file, 'w')))
            return false;

        if (false === (fwrite($fh, $data))) {
            fclose($fh);
            return false;
        }

        fclose($fh);

        return true;
    }

    /**
     * 调整图片大小，将结果显示在网页中
     *
     * @throws Exception
     */
    public function modifyJPG()
    {
        empty($this->imSources) && $this->throwException('请先添加操作的图片对象');
        /**
         * 图片按比例调整大小的原理：
         * 1、比较原图大小是否小于等于目标大小，如果是则直接采用原图宽高
         * 2、如果原图大小超过目标大小，则对比原图宽高大小
         * 3、如：宽>高，则宽=目标宽, 高=目标宽的比例 * 原高
         * 4、如：高>宽，则高=目标高，宽=目标高的比例 * 原宽
         */
        $max_width = 500;
        $max_height = 500;

        $width = imagesx($this->imSources);
        $height = imagesy($this->imSources);

        $x_ratio = $max_width / $width;
        $y_ratio = $max_height / $height;

        if (($width <= $max_width) && ($height <= $max_height)) {
            $tn_width = $width;
            $tn_height = $height;
        } elseif (($x_ratio * $height) < $max_height) {
            $tn_height = ceil($x_ratio * $height);
            $tn_width = $max_width;
        } else {
            $tn_width = ceil($y_ratio * $width);
            $tn_height = $max_height;
        }

        $dst = imagecreatetruecolor($tn_width, $tn_height); // 新建一个真彩色图像
        imagecopyresampled($dst, $this->imSources, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height); // 重采样拷贝部分图像并调整大小
        header('Content-Type: image/jpeg');
        imagejpeg($dst, null, 100);
        imagedestroy($dst);
    }

    /**
     * 生成ico图像格式，添加数据
     *
     * @return boolean Ambigous unknown>
     */
    private function _getIcoData()
    {
        if (! is_array($this->_images) || empty($this->_images))
            return false;

        $data = pack('vvv', 0, 1, count($this->_images));
        $pixel_data = '';

        $icon_dir_entry_size = 16;

        $offset = 6 + ($icon_dir_entry_size * count($this->_images));

        foreach ($this->_images as $image) {
            $data .= pack('CCCCvvVV', $image['width'], $image['height'], $image['color_palette_colors'], 0, 1, $image['bits_per_pixel'], $image['size'], $offset);
            $pixel_data .= $image['data'];

            $offset += $image['size'];
        }

        $data .= $pixel_data;

        return $data;
    }

    /**
     * 将gd image resource 转换为BMP栅格图像格式
     *
     * @param
     *            gd image resource $im
     */
    private function _addImageData($im)
    {
        $width = imagesx($im);
        $height = imagesy($im);

        $pixel_data = array();

        $opacity_data = array();
        $current_opacity_val = 0;

        for ($y = $height - 1; $y >= 0; $y --) {
            for ($x = 0; $x < $width; $x ++) {
                $color = imagecolorat($im, $x, $y);

                $alpha = ($color & 0x7F000000) >> 24;
                $alpha = (1 - ($alpha / 127)) * 255;

                $color &= 0xFFFFFF;
                $color |= 0xFF000000 & ($alpha << 24);

                $pixel_data[] = $color;

                $opacity = ($alpha <= 127) ? 1 : 0;

                $current_opacity_val = ($current_opacity_val << 1) | $opacity;

                if ((($x + 1) % 32) == 0) {
                    $opacity_data[] = $current_opacity_val;
                    $current_opacity_val = 0;
                }
            }

            if (($x % 32) > 0) {
                while (($x ++ % 32) > 0)
                    $current_opacity_val = $current_opacity_val << 1;

                $opacity_data[] = $current_opacity_val;
                $current_opacity_val = 0;
            }
        }

        $image_header_size = 40;
        $color_mask_size = $width * $height * 4;
        $opacity_mask_size = (ceil($width / 32) * 4) * $height;

        $data = pack('VVVvvVVVVVV', 40, $width, ($height * 2), 1, 32, 0, 0, 0, 0, 0, 0);

        foreach ($pixel_data as $color)
            $data .= pack('V', $color);

        foreach ($opacity_data as $opacity)
            $data .= pack('N', $opacity);

        $image = array(
            'width' => $width,
            'height' => $height,
            'color_palette_colors' => 0,
            'bits_per_pixel' => 32,
            'size' => $image_header_size + $color_mask_size + $opacity_mask_size,
            'data' => $data
        );

        $this->_images[] = $image;
    }

    protected function throwException($e)
    {
        throw new Exception($e);
    }
}

