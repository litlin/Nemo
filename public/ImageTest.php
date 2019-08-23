<?php
use BaseServices\Services\PHPImage;

/**
 *
 * @author lin
 *        
 */
class ImageTest
{

    public static function read($param): void
    {
        $image = new PHPImage();
        $image->setImageFile($param)->getImage(155, 150, 50, 50, 150, 150);
        $image->showImage();
        // var_dump(getimagesize($param));
    }

    public static function cutIcon($param)
    {
        $imageclass = new PHPImage();
        $imageclass->setImageFile($param)->getImage(460, 287, 100, 100, 140, 135);
        if ($imageclass->saveICO($savePath = '/tmp/' . time() . ".ico") === true)
            var_dump("保存文件 $savePath 成功");
    }
}
chdir(dirname(__DIR__));
include_once 'vendor/autoload.php';
ImageTest::read("test/img/09.jpg");

