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

    protected $data = "";

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
            $result = str_replace("{__CONTENT__}", $this->data . $data, $result);
            // header ( 'Content-Type:' . $contentType . '; charset=' . $charset );
            // header('Content-Type:text/html; charset=UTF-8');
            echo $this->changeViewResult($result);
        }
    }

    protected function addData($name, $value, $attributes = ""): AbstractController
    {
        $name = strtolower($name);
        switch ($name) {
            case "title":
                $this->data .= "<title>$value</title>";
                break;
            case "link":
                $this->data .= '<link href="' . $value . '" rel="stylesheet">';
                break;
            case "style":
                $this->data .= '<style type="text/css">' . $value . '</style>';
                break;
            case "js.file":
                $this->data .= '<script src="' . $value . '"' . $attributes . '></script> ';
                break;
            case "js.code":
                $this->data .= '<script ' . $attributes . '>' . $value . '</script> ';
                break;
            case "p":
                $this->data .= "<p $attributes>$value</p>";
                break;
            case "div":
                $this->data .= "<div $attributes>$value</div>";
                break;
            case "table":
                if ($value instanceof \ArrayIterator) {
                    $this->data .= "<table $attributes>";
                    while ($value->valid()) {
                        foreach ($value->current() as $row) {
                            $this->data .= "<tr>";
                            foreach ($row as $cell) {
                                if (is_array($cell)) {
                                    $this->data .= "<" . $value->key() . (" " . $cell[1] ?? "") . ">" . ($cell[0] ?? $cell) . "</" . $value->key() . ">";
                                } else {
                                    $this->data .= "<" . $value->key() . ">" . $cell . "</" . $value->key() . ">";
                                }
                            }
                            $this->data .= "</tr>";
                        }
                        $value->next();
                    }
                    $this->data .= "</table>";
                } else {
                    $this->data .= $value;
                }
                break;
            default:
                $this->data .= $value; // 䚱
                break;
        }
        return $this;
    }

    /**
     * 将单独设置的各项内容移动到合适的位置,美化外观
     *
     * @param string $result
     * @return string
     */
    private function changeViewResult(string $result): string
    {
        $result = preg_replace("/[\t\n\r]+/", "", $result); // 删除各类制表换行符等，不然正则很可能无法匹配

        preg_replace_callback_array([
            '/<(div)[^<>]*>[^\1]*(<([tT]itle)[^>]*>[^<>]*<\/\3>)[^\1]*<\/\1>/' => function ($match) use (&$result) {
                $result = preg_match('/<(title)>[^<>]*<\/\1>/', $result) ? preg_replace('/<(title)>[^<]*<\/\1>/', $match[2], $result) : str_replace('</head>', $match[2] . '</head>', $result);
                // 将Title设置到中head中，如果先有<head></head>标签，直接替换，否则凑加到head中
                $result = str_replace($match[0], str_replace($match[2], "", $match[0]), $result);
                return str_replace($match[2], "", $match[0]);
            },
            '/<(div)[^<>]*>[^\1]*(<link[^>]+>)[^\1]*<\/\1>/' => function ($match) use (&$result) {
                /*
                 * 针对可能出现的多个操作对象的改进
                 */
                if (preg_match_all('/<link[^>]+>/', $match[0], $mi))
                    foreach ($mi[0] as $each) {
                        $result = str_replace('</head>', $each . '</head>', $result); // 在目标位置添加
                        $result = str_replace($match[0], str_replace($each, "", $match[0]), $result); // 删除源位置内容
                        $match[0] = str_replace($each, "", $match[0]); // 记录替换操作
                    }
                return $match[0];
            },
            '/<(div).*?(?!>)[^\1]*(<(style)[^>]*>[^<>]*<\/\3>)[^\1]*<\/\1>/' => function ($match) use (&$result) {
                if (preg_match_all('/<(style)[^>]*>[^<>]*<\/\1>/', $match[0], $mi))
                    foreach ($mi[0] as $each) {
                        $result = str_replace('</head>', $each . '</head>', $result); // 在目标位置添加
                        $result = str_replace($match[0], str_replace($each, "", $match[0]), $result); // 删除源位置内容
                        $match[0] = str_replace($each, "", $match[0]); // 记录替换操作
                    }
                return $match[0]; // 返回针对匹配的操作，后续的匹配操作才能继续
            },
            '/<(div)[^<>]*>[^\1]*(<(script)[^<>]*><\/\3>)[^\1]*<\/\1>/' => function ($match) use (&$result) {
                if (preg_match_all('/<(script)[^<>]*><\/\1>/', $match[0], $mi))
                    foreach ($mi[0] as $each) {
                        $result = str_replace('</body>', $each . '</body>', $result);
                        $result = str_replace($match[0], str_replace($each, "", $match[0]), $result);
                        $match[0] = str_replace($each, "", $match[0]);
                    }
                return $match[0];
            }
        ], $result);
        /*
         * 针对可能出现的项目目录不是Server根目录情况设置引用文件的路径变化
         */
        if (preg_match('/\blocalhost\b\:\d{4,5}/', $_SERVER['HTTP_HOST']) && preg_match('/^\/' . basename(getcwd()) . '$/', $_SERVER['REQUEST_URI']))
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
    protected function setLayout(string $layout): void
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

