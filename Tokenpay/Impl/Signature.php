<?php
declare(strict_types=1);

namespace App\Pay\Tokenpay\Impl;

/**
 * Class Signature
 * @package App\Pay\Tokenpay\Impl
 */
class Signature implements \App\Pay\Signature
{

    /**
     * 生成签名
     * @param array $data
     * @param string $key
     * @return string
     */
    public static function generateSignature(array $data, string $key): string
    {
        // 1. 过滤掉空值和Signature字段本身
        $filteredData = [];
        foreach ($data as $k => $v) {
            if ($v !== null && $v !== '' && $k !== 'Signature' && $k !== 'signature') {
                $filteredData[$k] = $v;
            }
        }
        
        // 2. 按照ASCII码排序（ksort默认就是ASCII排序）
        ksort($filteredData);
        
        // 3. 拼接字符串
        $signStr = '';
        foreach ($filteredData as $k => $v) {
            $signStr .= $k . '=' . $v . '&';
        }
        $signStr = rtrim($signStr, '&');
        
        // 4. 拼接密钥
        $signStr .= $key;
        
        // 5. 计算MD5（小写）
        return strtolower(md5($signStr));
    }

    /**
     * 验证回调签名
     * @inheritDoc
     */
    public function verification(array $data, array $config): bool
    {
        // 获取接收到的签名（兼容大小写）
        $receivedSign = $data['Signature'] ?? $data['signature'] ?? '';
        
        // 移除签名字段
        unset($data['Signature']);
        unset($data['signature']);
        
        // 生成签名
        $generatedSign = self::generateSignature($data, $config['key']);
        
        // 对比签名（不区分大小写）
        return strtolower($receivedSign) === strtolower($generatedSign);
    }
}
