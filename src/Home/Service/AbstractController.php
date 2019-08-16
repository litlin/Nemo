<?php
namespace Home\Service;

use Exception;

/**
 *
 * @author lin
 *        
 */
class AbstractController implements ControllerInterface
{

    protected $layout = "default";

    /**
     */
    public function __construct()
    {}

    /**
     * (non-PHPdoc)
     *
     * @see ControllerInterface::index()
     */
    public function index()
    {}

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
            $this->layout = "src/views/layout/" . $this->layout . ".layout.html";
        }
        $result = $this->getView($this->layout);

        $result = str_replace("{__CONTENT__}", $data, $result);
        // header ( 'Content-Type:' . $contentType . '; charset=' . $charset );
        echo $result;
    }

    protected function setLayout(string $layout)
    {
        if (file_exists(getcwd() . "/src/views/layout/" . $layout . ".layout.html"))
            $this->layout = $layout;
    }

    private function getView($view)
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

