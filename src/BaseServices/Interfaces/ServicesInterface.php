<?php
namespace BaseServices\Interfaces;

/**
 *
 * @author lin
 *        
 */
interface ServicesInterface
{

    /**
     * 初始化
     *
     * @return ServicesInterface
     */
    public static function bootstrap(): ServicesInterface;

    /**
     * 执行
     */
    public function run(): void;
}

