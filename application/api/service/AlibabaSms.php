<?php

namespace app\api\service;

use app\api\model\Setting;
use app\common\library\sms\Driver as SmsDriver;
use think\Exception;

class AlibabaSms
{
    /**
     * 注册短信发送
     * @param $code
     * @param $accept_phone
     * @return bool
     * @throws Exception
     */
    public static function register($code,$accept_phone)
    {
        $re = Setting::getItem('sms');
        $engine = isset($re['engine'])?$re['engine']:'';
        $aliyun = isset($engine['aliyun'])?$engine['aliyun']:'';
        $AccessKeyId = 'LTAI4G1vC8etiXUNjuwpdCbc';
        $AccessKeySecret = 'Jc4QgBt5RNKYBqZ0RJd1N5UGHRSzXm';
        $sign = '注册验证';
        $register = isset($aliyun['register'])?$aliyun['register']:'';
        $template_code = 'SMS_235818408';
        $msg_type = 'register';

        if(!$AccessKeyId || !$AccessKeySecret || !$sign || !$template_code)
        {
            throw new Exception('短信发送未配置,请管理员');
        }

        $SmsDriver = self::init($AccessKeyId, $AccessKeySecret, $sign, $msg_type, $template_code, $accept_phone);

        $templateParams = [
            'code'  => $code,
        ];

        return self::sms($SmsDriver,$msg_type,$templateParams);

    }

    /**
     * 配置初始化
     * @param $AccessKeyId
     * @param $AccessKeySecret
     * @param $sign
     * @param $msg_type
     * @param $template_code
     * @param $accept_phone
     * @return SmsDriver
     * @throws Exception
     */
    private static function init($AccessKeyId, $AccessKeySecret, $sign, $msg_type, $template_code, $accept_phone)
    {
        $SmsDriver = new SmsDriver([
            'default' => 'aliyun',
            'engine' => [
                'aliyun' => [
                    'AccessKeyId' => $AccessKeyId,
                    'AccessKeySecret' => $AccessKeySecret,
                    'sign' => $sign,
                    $msg_type => compact('template_code', 'accept_phone'),
                ],
            ],
        ]);
        return $SmsDriver;
    }

    /**
     * 短信发送
     * @param $SmsDriver
     * @param $msg_type
     * @param $templateParams
     * @return bool
     * @throws Exception
     */
    private static function sms($SmsDriver,$msg_type,$templateParams)
    {

        if ($SmsDriver->sendSms($msg_type, $templateParams, true)) {
            return true;
        }
        throw new Exception($SmsDriver->getError());
    }
}