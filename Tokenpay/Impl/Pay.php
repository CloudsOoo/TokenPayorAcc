<?php
declare(strict_types=1);

namespace App\Pay\Tokenpay\Impl;

use App\Entity\PayEntity;
use App\Pay\Base;
use GuzzleHttp\Exception\GuzzleException;
use Kernel\Exception\JSONException;

/**
 * Class Pay
 * @package App\Pay\Tokenpay\Impl
 */
class Pay extends Base implements \App\Pay\Pay
{

    /**
     * @return PayEntity
     * @throws JSONException
     */
    public function trade(): PayEntity
    {

        if (!$this->config['url']) {
            throw new JSONException("请配置网关地址");
        }

        if (!$this->config['key']) {
            throw new JSONException("请配置密钥");
        }

        if (!$this->config['typename']) {
            throw new JSONException("请配置货币类型");
        }

        // 构建订单参数
        $param = [
            'OutOrderId' => $this->tradeNo, // 外部订单号
            'OrderUserKey' => $this->tradeNo, // 支付用户标识
            'ActualAmount' => number_format((float)$this->amount, 2, '.', ''), // 保留两位小数
            'Currency' => $this->config['typename'], // 加密货币币种
            'NotifyUrl' => $this->callbackUrl, // 异步通知URL
            'RedirectUrl' => $this->returnUrl, // 跳转URL
        ];
        
        // 生成签名
        $param['Signature'] = Signature::generateSignature($param, $this->config['key']);

        try {
            $request = $this->http()->post(trim($this->config['url'], "/") . '/CreateOrder', [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($param),
            ]);
        } catch (GuzzleException $e) {
            throw new JSONException("网关连接失败，下单未成功：" . $e->getMessage());
        }

        $contents = $request->getBody()->getContents();
        $json = (array)json_decode((string)$contents, true);
        
        if (!isset($json['success']) || !$json['success']) {
            $errorMsg = isset($json['message']) ? $json['message'] : '未知错误';
            throw new JSONException("支付网关异常：" . $errorMsg);
        }

        $payEntity = new PayEntity();
        $payEntity->setType(self::TYPE_REDIRECT);
        $payEntity->setUrl($json['data']);
        return $payEntity;
    }
}
