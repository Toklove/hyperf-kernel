<?php

declare (strict_types=1);
/**
 * @copyright 深圳市易果网络科技有限公司
 * @version 1.0.0
 * @link https://dayiguo.com
 */

namespace Zunea\HyperfKernel\SMS;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Zunea\HyperfKernel\SMS\Exception\SMSException;

/**
 * 阿里云短信服务
 *
 * @property string $accessKeyId
 * @property string $accessSecret
 * @property string $regionId
 * @property string $host
 * @property string $signName
 * @author 刘兴永(aile8880@qq.com)
 * @package Zunea\HyperfKernel\SMS
 */
class AliCloudSMS implements SMSInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * AliCloudSMS constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }

    /**
     * 发送短信
     *
     * @param string $phoneNumber
     * @param string $templateCode
     * @param string $content
     * @return array
     * @throws SMSException
     */
    public function sendSMS(string $phoneNumber, string $templateCode, string $content): array
    {
        try {
            AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessSecret)
                ->regionId($this->regionId)
                ->asDefaultClient();

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host($this->host)
                ->options([
                    'query' => [
                        'RegionId'      => $this->regionId,
                        'PhoneNumbers'  => $phoneNumber,
                        'SignName'      => $this->signName,
                        'TemplateCode'  => $templateCode,
                        'TemplateParam' => $content
                    ],
                ])
                ->request()
                ->toArray();
            // 判断是否发送失败
            if (!isset($result['Code']) || $result['Code'] !== 'OK') {
                throw new SMSException(sprintf('SMS failed to send, return result: %s', $result['Message'] ?? 'null'));
            }
            return $result;
        } catch (ClientException $e) {
            throw new SMSException(sprintf('ClientException: %s', $e->getMessage()), $e->getCode());
        } catch (ServerException $e) {
            throw new SMSException(sprintf('ServerException: %s', $e->getMessage()), $e->getCode());
        }
    }
}