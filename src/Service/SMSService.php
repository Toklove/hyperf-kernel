<?php

declare (strict_types=1);
/**
 * @copyright 深圳市易果网络科技有限公司
 * @version 1.0.0
 * @link https://dayiguo.com
 */

namespace Zunea\HyperfKernel\Service;

use Hyperf\Utils\Codec\Json;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Zunea\HyperfKernel\SMS\Exception\SMSException;
use Zunea\HyperfKernel\SMS\Exception\SMSIntervalException;
use Zunea\HyperfKernel\SMS\SMSFactory;
use Zunea\HyperfKernel\SMS\SMSInterface;

/**
 * 短信服务
 *
 * @author 刘兴永(aile8880@qq.com)
 * @package Zunea\HyperfKernel\Service
 */
class SMSService
{
    /**
     * 短信验证码存储名字
     *
     * @var string
     */
    const CACHE_NAME = 'SMSVerifyCode:%s:%s';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SMSInterface
     */
    private $SMSService;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * 构造函数
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
        $this->SMSService = $this->container->get(SMSFactory::class)->getAliCloudSMS();
        $this->cache      = $this->container->get(CacheInterface::class);
    }

    /**
     * 发送验证码
     *
     * @param string $phone 手机号码
     * @param string $scene 场景
     * @param string $code 验证码
     * @param string $templateCode 短信模板Code
     * @return string
     * @throws SMSException
     * @throws SMSIntervalException
     */
    public function sendVerifyCode(string $phone, string $scene, string $code, string $templateCode)
    {
        // 获取缓存
        $cacheName = sprintf(self::CACHE_NAME, $scene, $phone);
        try {
            if (($his = $this->cache->get($cacheName, null)) !== null) {
                // 是否开启发送频率限制
                if (($interval = config('sms.common.interval', 0)) > 0) {
                    // 判断发送频率
                    if (isset($his['setTime']) && $his['setTime'] + $interval > time()) {
                        throw new SMSIntervalException('SMS is sent too frequently');
                    }
                }
            }
            // 发送验证码
            $this->SMSService->sendSMS($phone, $templateCode, Json::encode([
                'code' => $code
            ]));
            $this->cache->set($cacheName, [
                'code'    => $code,
                'setTime' => time()
            ]);
            return $code;
        } catch (InvalidArgumentException $e) {
            throw new SMSException('Failed to send:' . $e->getMessage());
        }
    }

    /**
     * 校验验证码
     *
     * @param string $phone
     * @param string $scene
     * @param string $code
     * @return bool
     */
    public function checkVerifyCode(string $phone, string $scene, string $code): bool
    {
        // 获取缓存
        $cacheName = sprintf(self::CACHE_NAME, $scene, $phone);
        try {
            if (!$cache = $this->cache->get($cacheName)) {
                return false;
            }
            if (!isset($cache['code']) || $cache['code'] !== $code) {
                return false;
            }
            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * 销毁验证码
     *
     * @param string $phone
     * @param string $scene
     * @return bool
     */
    public function destroyVerifyCode(string $phone, string $scene): bool
    {
        // 获取缓存
        $cacheName = sprintf(self::CACHE_NAME, $scene, $phone);
        try {
            $this->cache->delete($cacheName);
            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}