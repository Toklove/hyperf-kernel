<?php

declare (strict_types=1);
/**
 * @copyright 深圳市易果网络科技有限公司
 * @version 1.0.0
 * @link https://dayiguo.com
 */

namespace Zunea\HyperfKernel\Utils;
/**
 * 随机码生成
 *
 * @author 刘兴永(aile8880@qq.com)
 * @package Zunea\HyperfKernel\Utils
 */
class Random
{
    /**
     * 生成六位随机码
     *
     * @return string
     */
    public static function generatorCode6(): string
    {
        mt_srand();

        return (string)mt_rand(100000,999999);
    }
}