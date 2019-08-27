<?php
namespace BaseServices\Services;

use BaseServices\Interfaces\ServicesInterface;

/**
 *
 * @author lin
 *        
 */
class BaseService implements ServicesInterface
{

    private $method, $params = array();

    /**
     */
    public function __construct($moduleController, string $action, array $args = array())
    {
        $method = new \ReflectionMethod($moduleController, $action);
        if ($method->isPublic()) {
            $this->params = [];
            if (! empty($args[0])) {
                if (count($args) == $method->getNumberOfParameters()) {
                    $i = 0;
                    foreach ($method->getParameters() as $p) {
                        $this->params[$p->name] = $args[$i ++];
                    }
                } elseif (count($args) == $method->getNumberOfRequiredParameters()) {
                    $i = 0;
                    foreach ($method->getParameters() as $p) {
                        if (! $p->isDefaultValueAvailable())
                            $this->params[$p->name] = $args[$i ++];
                    }
                } elseif ($action === 'index') {
                    $this->params = $args;
                } else {
                    self::showError("too many params");
                }
            }
            $this->method = $method;

            // $viewPath = "src" . DIRECTORY_SEPARATOR . $m . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . $action . ".html";
        } else {
            self::showError("only public action can be called .");
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \BaseServices\Interfaces\ServicesInterface::run()
     */
    public function run(): void
    {
        try {
            $result = $this->method->invokeArgs($this->method->getDeclaringClass()
                ->newInstance(), $this->params);
            if (! empty($result)) {
                print $result;
            }
        } catch (\Exception $e) {
            self::showError($e);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \BaseServices\Interfaces\ServicesInterface::bootstrap()
     */
    public static function bootstrap(): ServicesInterface
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (preg_match('/^[A-Za-z0-9\/]+$/', $uri)) {
            if (preg_match('/' . basename(getcwd()) . '/', $uri))
                $uri = substr($uri, strlen(basename(getcwd())));

            $uria = explode("/", trim($uri, "\/"));

            if (preg_match('/^[A-Z][a-z]+$/', $uria[0])) {
                $m = $uria[0];
                array_shift($uria);
                if (! empty($uria) && preg_match('/^[A-Z][a-z]+$/', $uria[0])) {
                    $c = $uria[0];
                    array_shift($uria);
                } else {
                    $c = $m;
                }
            } else {
                $m = $c = 'Home';
            }
            $mc = $m . "\\Controller\\" . $c . "Controller";
            if (class_exists($mc)) {
                if (! empty($uria) && method_exists($mc, $action = $uria[0]) && preg_match('/^[a-z][a-zA-Z]*$/', $action)) {
                    array_shift($uria);
                } elseif (method_exists($mc, $action = "index")) {
                    ;
                } else {
                    self::showError("no method can be called.");
                }
                return new BaseService($mc, $action, $uria);
            } else {
                self::showError("$c controller in $m moudle not exists");
            }
        }
        self::showError("无法定位");
    }

    private static function showError($param)
    {
        print "<!DOCTYPE html>
                    <html>
                    <head>
                    <meta charset=\"UTF-8\">
                    <title>有错误发生</title>
                    <style type=\"text/css\">
                        body {
                            padding-top: 60px;
                            padding-bottom: 60px;
                            padding-left:2cm;
                            padding-right:2cm;
                            background-color: #A39480;
                            text-align:center
                        }
                    </style>
                    </head>
                    <body>
                        <div>
                         $param
                        </div>
                    </body>
                    </html>";
        die();
    }
}

