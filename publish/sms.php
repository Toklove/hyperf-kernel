<?php
/**
 * @copyright 深圳市易果网络科技有限公司
 * @version 1.0.0
 * @link https://dayiguo.com
 */
return [
    // 默认短信渠道
    'default'           => 'aliCloud',
    // 短信验证码缓存名
    'verify_code_cache' => 'SMSVerifyCode:%s:%s',
    // 发送频率
    'interval'          => 60,
    // 缓存有效期
    'expired'           => 30 * 60,
    // 短信渠道
    'channel'           => [
        // 阿里云短信配置
        'aliCloud' => [
            'driver'       => \Zunea\HyperfKernel\SMS\AliCloudSMS::class,
            'accessKeyId'  => env('aliCloudAccessKeyId', ''),
            'accessSecret' => env('aliCloudAccessSecret', ''),
            'regionId'     => env('aliCloudRegionId', 'cn-hangzhou'),
            'host'         => env('aliCloudHost', 'dysmsapi.aliyuncs.com'),
            'signName'     => env('aliCloudSignName', '')
        ]
    ]
];