<?php
use Yansongda\Pay\Pay;

return [
    'alipay' => [
        'default' => [
            // 必填-支付宝分配的 app_id
            'app_id' => env('ALIPAY_APP_ID'),
            // 必填-应用私钥 字符串或路径
            'app_secret_cert' => env('ALIPAY_APP_SECRECT_CERT'),
            // 必填-应用公钥证书 路径
            'app_public_cert_path' => base_path('public/appCertPublicKey_2021000121617096.cer'),
            // 必填-支付宝公钥证书 路径
            'alipay_public_cert_path' => base_path('public/alipayCertPublicKey_RSA2.crt'),
            // 必填-支付宝根证书 路径
            'alipay_root_cert_path' => base_path('public/alipayRootCert.cer'),
            'return_url' => env('ALIPAY_RETURN_URL'),
            'notify_url' => env('ALIPAY_NOTIFY_URL'),
            // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
            'service_provider_id' => env('ALIPAY_SERVICE_PROVIDER_ID'),
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            // MODE_NORMAL正常模式
            // MODE_SANDBOX沙盒模式
            'mode' => Pay::MODE_SANDBOX,
        ],
    ],
    'wechat' => [
        'default' => [
            // 必填-商户号，服务商模式下为服务商商户号
            'mch_id' => '',
            // 必填-商户秘钥
            'mch_secret_key' => '',
            // 必填-商户私钥 字符串或路径
            'mch_secret_cert' => '',
            // 必填-商户公钥证书路径
            'mch_public_cert_path' => '',
            // 必填
            'notify_url' => '',
            // 选填-公众号 的 app_id
            'mp_app_id' => '',
            // 选填-小程序 的 app_id
            'mini_app_id' => '',
            // 选填-app 的 app_id
            'app_id' => '',
            // 选填-合单 app_id
            'combine_app_id' => '',
            // 选填-合单商户号
            'combine_mch_id' => '',
            // 选填-服务商模式下，子公众号 的 app_id
            'sub_mp_app_id' => '',
            // 选填-服务商模式下，子 app 的 app_id
            'sub_app_id' => '',
            // 选填-服务商模式下，子小程序 的 app_id
            'sub_mini_app_id' => '',
            // 选填-服务商模式下，子商户id
            'sub_mch_id' => '',
            // 选填-微信公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__ . '/Cert/wechatPublicKey.crt',
            ],
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
            'mode' => Pay::MODE_NORMAL,
        ],
    ],
    'http' => [ // optional
        'timeout' => env('PAY_HTTP_TIMEOUT'),
        'connect_timeout' => env('PAY_HTTP_CONNECT_TIMEOUT'),
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    // optional，默认 warning；日志路径为：sys_get_temp_dir().'/logs/yansongda.pay.log'
    'logger' => [
        'enable' => env('PAY_LOGGER_ENABLE'),
        'file' => env('PAY_LOGGER_FILE'),
        'level' => env('PAY_LOGGER_LEVEL'),
        'type' => env('PAY_LOGGER_TYPE'), // optional, 可选 daily.
        'max_file' => env('PAY_LOGGER_MAX_FILE'),
    ],
];
