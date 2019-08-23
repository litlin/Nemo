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
        $this->display("<title>试试</title><p>are you ready?</p>");
    }
}

