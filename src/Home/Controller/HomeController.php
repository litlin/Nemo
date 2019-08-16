<?php
namespace Home\Controller;

use Home\Service\AbstractController;

/**
 *
 * @author lin
 *        
 */
class HomeController extends AbstractController
{

    /**
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->display( "<title>试试</title><p>are you ready?</p>");
    }
}

