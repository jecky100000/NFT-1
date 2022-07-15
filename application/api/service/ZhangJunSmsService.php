<?php

namespace app\api\service;

class ZhangJunSmsService
{
    private $config = [];
    private $time = '';
    private $url = '';

    public function __construct($config = [
        'id'        => '18200',
        'user'      => 'gzyckj',
        'pass'      => 'gzyc0705*'
    ])
    {
        $this->config = $config;
        $this->time = time();
        $this->url = 'http://120.77.14.55:9999/v2sms.aspx';
    }

    private function sign()
    {
        // 账号+密码+时间戳
        return md5($this->config['user'].$this->config['pass'].$this->time);
    }

    /**
     * 短信发送
     * @param $mobile
     * @param $content
     * @return mixed
     */
    public function send($mobile,$content)
    {
        $params = [
            'userid'    => $this->config['id'],
            'timestamp' => $this->time,
            'sign'      => $this->sign(),
            'mobile'    => $mobile,
            'content'   => $content,
            'sendTime'  => '',
            'action'    => 'send',
            'extno'     => '',
            'rt'        => 'json',
        ];
        $re = curlPost($this->url,$params);
        log_write_sms([$params,$re]);
        return $re;
    }
}