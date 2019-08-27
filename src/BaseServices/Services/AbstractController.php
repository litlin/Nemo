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

            echo $this->changeViewResult($result);
        }
    }

    private function changeViewResult(string $result): string
    {
        /*
         * 将在Controller中设置的各项内容移动到合适的位置
         * 因为涉及到两处操作，目的地添加，源删除，无法通过返回一个操作值完成
         * 引用源变量的地址来操作
         */
        $result = preg_replace("/[\t\n\r]+/", "", $result);

        preg_replace_callback_array([
            '/<(div)[^<>]*>[^\1]*(<([tT]itle)[^>]*>[^<>]*<\/\3>)[^\1]*<\/\1>/' => function ($match) use (&$result) {
                $result = preg_match('/<(title)>[^<>]*<\/\1>/', $result) ? preg_replace('/<(title)>[^<]*<\/\1>/', $match[2], $result) : str_replace('</head>', $match[2] . '</head>', $result);
                // 将Title设置到中head中，如果先有<head></head>标签，直接替换，否则凑加到head中
                $result = str_replace($match[0], str_replace($match[2], "", $match[0]), $result);
                return str_replace($match[2], "", $match[0]);
            },
            '/<(div).*?(?!>)[^\1]*(<(style)[^>]*>[^<>]*<\/\3>)[^\1]*<\/\1>/' => function ($match) use (&$result) {
                $result = str_replace('</head>', $match[2] . '</head>', $result); // 将匹配结果移动到head中
                $result = str_replace($match[0], str_replace($match[2], "", $match[0]), $result); // 删除原位置的匹配项
                return str_replace($match[2], "", $match[0]); // 返回针对匹配的操作，后续的匹配操作才能继续
            },
            '/<(div)[^<>]*>[^\1]*(<(script)[^<>]*><\/\3>)[^\1]*<\/\1>/' => function ($match) use (&$result) {
                $result = str_replace('</body>', $match[2] . '</body>', $result);
                $result = str_replace($match[0], str_replace($match[2], "", $match[0]), $result);
                return str_replace($match[2], "", $match[0]);
            },
            '/<(div)[^<>]*>[^\1]*(<link[^>]+>)[^\1]*<\/\1>/' => function ($match) use (&$result) {
                $result = str_replace('</head>', $match[2] . '</head>', $result);
                $result = str_replace($match[0], str_replace($match[2], "", $match[0]), $result);
                return str_replace($match[2], "", $match[0]);
            }
        ], $result);
        /*
         * 针对可能出现的项目运行目录不是Server根目录情况设置引用文件的路径变化
         */
        if (preg_match('/\blocalhost\b\:\d{4,5}/', $_SERVER['HTTP_HOST']) && preg_match('/' . basename(getcwd()) . '/', $_SERVER['REQUEST_URI']))
            $result = preg_replace_callback_array([
                '/<(script)[^<>]*?(?<=src)="([^<>]+)"[^<>]*><\/\1>/' => function ($match) {
                    return str_replace($match[2], basename(getcwd()) . '/' . $match[2], $match[0]);
                },
                '/<link\s*href="([^"]+)(?!>)/' => function ($match) {
                    return str_replace($match[1], basename(getcwd()) . '/' . $match[1], $match[0]);
                }
            ], $result);

        return $result;
    }

    /**
     * 自行指定视图模版
     *
     * @param string $layout
     */
    protected function setLayout(string $layout)
    {
        // if (file_exists(getcwd() . "/src/views/layout/" . $layout . ".layout.html"))
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

