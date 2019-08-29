<?php
namespace Home\Controller;

use BaseServices\Services\AbstractController;
use BaseServices\Struct\TableStruct;

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
            "日期",
            "时间"
        ), [
            array(
                date('Y-m-d'),
                date('H:i:s')
            ),
            array(
                date('Y-m-d'),
                date('H:i:s', time() - 10 * 60 * 60)
            )
        ]), 'class="table"');
        $this->addData('div', '<span class="icon-off"></span>');
        $this->display();
    }
}

