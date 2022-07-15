<?php

namespace app\api\model;

use app\api\service\Help;
use app\common\model\BaseModel;
use app\common\model\User;

class PackageOrder extends BaseModel
{
    protected $table = 'yc_package_order';

    /**
     * 创建用户
     * @return void
     */
    public function cUser()
    {
        return $this->hasOne(User::class,'user_id','user_id')
            ->field('user_id,level,commission');
    }
    /**
     * 添加订单
     * @param $packageData
     * @param $user_id
     * @param $wxapp_id
     * @param $order_on
     * @return false|int
     */
    public function addData($packageData,$user_id,$wxapp_id,$order_on)
    {
        $re = $this->insertGetId([
            'user_id'       => $user_id,
            'order_on'      => $order_on,
            'p_type'        => $packageData['type'],
            'p_value'       => $packageData['value'],
            'status'        => 1,
            'wxapp_id'      => $wxapp_id,
            'p_price'       => $packageData['price'],
            'price'         => $packageData['price'],
            'create_time'   => time(),
            'update_time'   => time()
        ]);
        return $re;
    }

    public static function getPayDetail($order_on)
    {
        $model = new Letter();
        $re = $model->where('pay_statu',1)->where('no',$order_on)->find();
        return $re;
    }
}