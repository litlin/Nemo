<?php
namespace Home\Service;

/**
 *
 * @author lin
 *        
 */
interface ControllerInterface
{

    public function index();

    public function display(string $data = ""):void;
}

