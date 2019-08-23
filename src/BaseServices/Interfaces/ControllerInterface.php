<?php
namespace BaseServices\Interfaces;

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

