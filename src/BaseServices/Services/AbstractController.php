<?php
namespace BaseServices\Services;

use Exception;
use BaseServices\Interfaces\ControllerInterface;

/**
 *
 * @author lin
 *        
 */
abstract class AbstractController implements ControllerInterface
{

    protected $layout = "default";

    /**
     * 每个Controller自行实现index方法
     *
     * @see ControllerInterface::index()
     */
    abstract public function index();

    /**
     * (non-PHPdoc)
     *
     * @see ControllerInterface::display()
     */
    public function display($data = ""): void
    {
        if (empty($this->layout) || ! file_exists(getcwd() . "/src/views/layout/" . $this->layout . ".layout.html")) {
            header('Content-Type:text/html; charset=UTF-8');
            echo '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>有错误发生</h1></div><script type="text/javascript" src="js/bootstrap.min.js"></script>';
            die();
        } else {
            $result = $this->getView("src/views/layout/" . $this->layout . ".layout.html");
            $result = str_replace("{__CONTENT__}", $data, $result);
            // header ( 'Content-Type:' . $contentType . '; charset=' . $charset );
            echo $result;
        }
    }

    protected function setLayout(string $layout)
    {
        if (file_exists(getcwd() . "/src/views/layout/" . $layout . ".layout.html"))
            $this->layout = $layout;
    }

    /**
     * 通过ob操作读取指定网页内容并以字符串格式返回
     *
     * @param string $view
     *            文件路径
     * @throws Exception
     * @return string
     */
    private function getView(string $view): string
    {
        $level = ob_get_level();

        ob_start();

        try {
            include $view;
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }

        return ob_get_clean();
    }
}

