<?php
namespace Home\Controller;

use BaseServices\Services\AbstractController;
use BaseServices\Struct\TableStruct;
use BaseServices\Prepare\Prepare;
use BaseServices\Services\PHPImage;

/**
 *
 * @author lin
 *        
 */
class HomeController extends AbstractController
{

    public function index()
    {
        $this->addData("link", "css/icon.css")
            ->addData("title", "欢迎!")
            ->addData('p', 'are you ready?');
        $this->addData("table", TableStruct::format(array(
            [
                "日期",
                Prepare::id("date")
            ],
            [
                "时间",
                Prepare::cla("time")
            ]
        ), [
            [
                'style="color:blue; text-align:center"' => array(
                    [
                        date('Y-m-d'),
                        Prepare::cla("time")
                    ],
                    date('H:i:s')
                )
            ],
            array(
                Prepare::b(date('Y-m-d')),
                [
                    date('H:i:s', time() - 10 * 60 * 60),
                    'style="font-style:italic;color:red"'
                ]
            )
        ]), Prepare::cla("table"));
        $this->addData('div', Prepare::p("", Prepare::cla("icon-off")));
        $this->addData('style', '.time{background-color: #CCEED0;}');
        $this->display();
        var_dump(func_get_args());
    }

    public function img()
    {
        $this->addData("p", "展示图像");
        $image = new PHPImage();
        $image->setImageFile("public/img/09.jpg")->getImage(460, 287, 100, 100, 140, 135);
        $this->addData("div", $image->showImage());
//         $this->addData('div', '<img src="img/09.jpg"  alt="一些图标" />');
        $this->display();
    }
}

