<?php
/**
 * @copyright 深圳市易果网络科技有限公司
 * @version 1.0.0
 * @link https://dayiguo.com
 */
return [
    'common'   => [
        // 发送频率
        'interval' => 60,
        // 短信存储有效期
        'expired'  => 30 * 60
    ],
    'aliCloud' => [
        'accessKeyId'  => env('aliCloudAccessKeyId', ''),
        'accessSecret' => env('aliCloudAccessSecret', ''),
        'regionId'     => env('aliCloudRegionId', 'cn-hangzhou'),
        'host'         => env('aliCloudHost', 'dysmsapi.aliyuncs.com'),
        'signName'     => env('aliCloudSignName', '')
    ]
];