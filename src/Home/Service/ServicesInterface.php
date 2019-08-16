<?php
namespace Home\Service;

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
     * 开始运行
     */
    public function run(): void;
}

