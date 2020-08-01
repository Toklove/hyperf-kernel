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
 * @author 刘兴永(aile8880@qq.com)
 * @package Zunea\HyperfKernel\SMS
 */
class AliCloudSMS implements SMSInterface
{
    /**
     * 应用ID
     *
     * @var string
     */
    private $accessKeyId;

    /**
     * 应用秘钥
     *
     * @var string
     */
    private $accessSecret;

    /**
     * 地域
     *
     * @var string
     */
    private $regionId;

    /**
     * 请求节点
     *
     * @var string
     */
    private $host;

    /**
     * 短信前缀
     *
     * @var string
     */
    private $signName;

    /**
     * AliCloudSMS constructor.
     */
    public function __construct()
    {
        $this->accessKeyId  = config('sms.aliCloud.accessKeyId');
        $this->accessSecret = config('sms.aliCloud.accessSecret');
        $this->regionId     = config('sms.aliCloud.regionId');
        $this->host         = config('sms.aliCloud.host');
        $this->signName     = config('sms.aliCloud.signName');
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
                ->action('AddSmsSign')
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
            if (!isset($result['Code']) || $result['Code'] !== 'Code') {
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