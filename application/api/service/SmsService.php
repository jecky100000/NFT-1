<?php

namespace app\api\service;

use app\api\model\Letter;
use app\api\model\LetterLog;
use app\api\model\LetterTem;
use think\Db;

class SmsService
{

    /**
     * 短信单价
     * 短信单条字数
     * @return array
     */
    public static function UnitNum()
    {
        $unit_price = 1.00;
        $unit_num = 66;
        return [$unit_price,$unit_num];
    }

    /**
     * 短信数量
     * 字数/单条短信字数
     * @param $str
     * @param $num
     * @return false|float
     */
    public static function SmsNum($str,$num)
    {
        $strLen = mb_strlen($str);
        return ceil($strLen/$num);
    }

    /**
     * 短信发送
     * @param $data
     */
    public static function dataSend($data,$config = '')
    {
        // 短信未完成 并且 发送时间为空或者发送时间小于等于当前时间
        if($data['is_complete'] === 0 && (!$data['sen_time'] || $data['sen_time'] <= time())) {
            $content = self::getContent(2,$data['wxapp_id'],$config['name']);
            $mobile = $data['mobile'];
            $re = self::send($content,$mobile,$config);

            // 发送日志
            LetterLog::add($content,$re,$data);

            // 判断是否完成
            if (($data['send_sum'] - 1) <= 0) {
                Db::query('update yc_letter set send_sum=send_sum-1,com_sum=com_sum+1,is_complete=1 where id=' . $data['id']);
            } else {
                Db::query('update yc_letter set send_sum=send_sum-1,com_sum=com_sum+1 where id=' . $data['id']);

            }
        }
    }

    /**
     * 内容匹配
     * @param 1 短信注册 2 消息通知
     * @param $data
     * @return array|string|string[]
     */
    public static function getContent($type,$wxapp_id,$con)
    {
//        $mo = '【猿创科技】您收到来自他/她的祝福:{$code}';
        $re = '';
        $data = LetterTem::getTem($type,$wxapp_id);
        $tem = isset($data['tem'])?$data['tem']:'';
        $replace = isset($data['replace'])?$data['replace']:'';
        if($tem)
        {
            $re = str_replace($replace,$con,$tem);
        }
        return $re;
    }

    /**
     * 短信发送
     *
     * 示例:https:{mobile}&c=【短信宝】您的验证码是9119,30秒内有效
     * @param $content
     * @param $mobile
     */
    public static function send($content,$mobile, $config = '')
    {
        // $config 为空的情况根据wxapp_id
//        $zhangjun = new ZhangJunSmsService();
//        $re = $zhangjun->send($mobile,$content);
        $user = isset($config['dxb_username'])?$config['dxb_username']:'';
        $p = isset($config['dxb_key'])?$config['dxb_key']:'';
        if($user)
        {
            $url = 'https://api.smsbao.com/sms?u='.$user.'&p='.$p.'&m='.$mobile.'&c='.$content;
            $re = curl($url);
//            var_dump($re);
        }
        else
        {
            return '配置信息不存在';
        }


        return $re;
    }
}