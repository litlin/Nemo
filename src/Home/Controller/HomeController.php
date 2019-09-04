<?php
namespace Home\Controller;

use BaseServices\Services\AbstractController;
use BaseServices\Struct\TableStruct;
use BaseServices\Prepare\Prepare;

/**
 *
 * @author lin
 *        
 */
class HomeController extends AbstractController
{

    public function index()
    {
var_dump(func_get_args());
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
            array(
                [
                    date('Y-m-d'),
                    Prepare::cla("time")
                ],
                date('H:i:s')
            ),
            array(
                Prepare::b(date('Y-m-d')),
                date('H:i:s', time() - 10 * 60 * 60)
            )
        ]), Prepare::cla("table"));
        $this->addData('div', Prepare::p("", Prepare::cla("icon-off")));
        $this->addData('style', '.time{background-color: #CCEED0;}');
        $this->display();
    }
}

