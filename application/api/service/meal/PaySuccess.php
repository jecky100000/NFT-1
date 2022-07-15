<?php

namespace app\api\service\meal;

use app\api\model\Setting;
use app\api\model\User;
use app\api\service\Basics;
use app\api\model\User as UserModel;
use app\api\model\PackageOrder as OrderModel;
use app\api\service\SmsService;
use think\Db;
use think\Log;

class PaySuccess extends Basics
{
    // 订单模型
    public $model;


    /**
     * 构造函数
     * PaySuccess constructor.
     * @param $orderNo
     * @throws \think\exception\DbException
     */
    public function __construct($orderNo)
    {
        // 实例化订单模型
        $this->model = OrderModel::getPayDetail($orderNo);
        $this->wxappId = $this->model['wxapp_id'];
    }

    /**
     * 获取订单详情
     * @return OrderModel|null
     */
    public function getOrderInfo()
    {
        return $this->model;
    }

    /**
     * 订单支付成功业务处理
     * @param int $payType 支付类型
     * @param array $payData 支付回调数据
     * @return bool
     */
    public function onPaySuccess($payType, $payData)
    {
        log_write_pay('---------------- onPaySuccess-----------------------');
        return $this->model->transaction(function () use ($payType, $payData) {
            // 更新订单状态
            $this->model->save([
                'pay_statu'        => 2,
                'pay_time'   => time(),
                'wx_no'         => $payData['transaction_id']
            ]);

            // 短信发送-回调出发
            // 短信发送时间小于等于当前时间
            if($this->model['sen_time'] <= time())
            {
                $config = Setting::getValues($this->wxappId);
                SmsService::dataSend($this->model,$config);
            }


            return true;
        });
    }

}