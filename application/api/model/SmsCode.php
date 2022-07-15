<?php

namespace app\api\model;

use app\api\service\AlibabaSms;
use app\api\service\SmsService;
use app\common\exception\BaseException;
use app\common\model\BaseModel;

class SmsCode extends BaseModel
{
    protected $table = 'yc_sms_code';

    /**
     * 验证验证码
     * @param $params
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function verify($params)
    {
        $mobile = $params['mobile'];
        $code = $params['code'];

        $data = $this->where([
            'mobile'    => $mobile,
            'code'      => $code,
            'status'    => 0,
        ])->whereBetween('create_time',[
            strtotime("-600 minute"),
            time()
        ])->find();
        return $data?$data:false;
    }

    /**
     * 验证后保存
     * @return void
     */
    private function verify_save($data)
    {
        $data->status = 1;
        $data->save();
    }

    /**
     * 验证
     * @param $params
     * @return void
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ver($params)
    {
        $re = $this->verify($params);
        if($re)
        {
            $this->verify_save($re);
        }
        else
        {
            throw new BaseException(['msg'=>'验证码验证失败']);
        }
    }

    /**
     * 短信发送
     * @param $mobile
     * @param $wxapp_id
     * @return void
     * @throws BaseException
     * @throws \think\Exception
     */
    public static function sms($mobile,$wxapp_id)
    {
        $count = SmsCode::where([
            'mobile'    => $mobile,
        ])->whereBetween('create_time',[
            strtotime(date('Y-m-d 00:00:00')),
            time()
        ])->count();
        if($count >= 5) throw new BaseException(['msg'  => '每日最多发5次短信']);

        $code = mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);

        // 发送短信
//        $config = Setting::getValues($wxapp_id);
//        $content = SmsService::getContent(1,$wxapp_id,$code);
//        SmsService::send($content,$mobile,$config);
        AlibabaSms::register($code,$mobile);

        // 存储
        $model = new SmsCode();
        $model->save([
            'mobile'    => $mobile,
            'code'      => $code,
            'wxapp_id'  => $wxapp_id
        ]);

    }
}