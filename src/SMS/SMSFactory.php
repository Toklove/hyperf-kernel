<?php

declare (strict_types=1);
/**
 * @copyright 深圳市易果网络科技有限公司
 * @version 1.0.0
 * @link https://dayiguo.com
 */

namespace Zunea\HyperfKernel\SMS;

use Psr\Container\ContainerInterface;
use Zunea\HyperfKernel\SMS\Exception\SMSException;

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
     * @param string|null $channel
     * @return mixed
     */
    public function get(string $channel = null): ?SMSInterface
    {
        $channel  = $channel === null ? config('sms.default') : $channel;
        $channels = config('sms.channel', []);
        if (!isset($channels[$channel])) {
            throw new SMSException(sprintf('SMS driver [%s] does not exist', $channel));
        }
        return make($channels[$channel]['driver'], [
            'config' => $channels[$channel]
        ]);
    }
}