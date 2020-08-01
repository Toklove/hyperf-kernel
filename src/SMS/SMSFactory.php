<?php

declare (strict_types=1);
/**
 * @copyright 深圳市易果网络科技有限公司
 * @version 1.0.0
 * @link https://dayiguo.com
 */

namespace Zunea\HyperfKernel\SMS;

use Psr\Container\ContainerInterface;

/**
 * 短信工厂
 *
 * @author 刘兴永(aile8880@qq.com)
 * @package Zunea\HyperfKernel\SMS
 */
class SMSFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * 构造函数
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * 阿里云短信服务
     *
     * @return SMSInterface
     */
    public function getAliCloudSMS(): SMSInterface
    {
        return $this->container->get(AliCloudSMS::class);
    }
}