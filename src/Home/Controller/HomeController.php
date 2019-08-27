<?php
namespace Home\Controller;

use BaseServices\Services\AbstractController;

/**
 *
 * @author lin
 *        
 */
class HomeController extends AbstractController
{

    public function index()
    {
        $this->display('<link href="css/icon.css" rel="stylesheet"><script type="text/javascript" src="js/bootstrap.min.js"></script><title>试试</title><p>are you ready?</p>
<style type="text/css">.container {padding-top: 60px;padding-bottom: 40px;}</style>');
    }
}

