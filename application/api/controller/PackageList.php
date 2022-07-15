<?php

namespace app\api\controller;

use app\api\controller\Controller;
use app\api\model\PackageOrder;

use app\api\server\Help;
use app\api\service\Payment;
use app\common\enum\OrderType;

class PackageList extends Controller
{
    /**
     * 套餐列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list()
    {
//        $user = $this->getUser();
        $re = [];
//        $type = $this->request->post('type');

        $where = [];
        $where['status'] = 0;
        $where['is_delete'] = 0;
        $where['type'] = 2;

        $model = new \app\api\model\PackageList();
        $re = $model->where($where)->order('sort','DESC')->select();

        return $this->renderSuccess($re);
    }

    /**
     * 套餐下单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function order()
    {
        $user = $this->getUser();
        $id = $this->request->post('id');
        $wxapp_id = $this->request->param('wxapp_id');

        $where = [];
        $where['status'] = 0;
        $where['is_delete'] = 0;
        $where['id'] = $id;
        $user_id = $user['user_id'];
        // 查询套餐信息
        $packageData = \app\api\model\PackageList::where($where)
            ->find();
        if(!$packageData) $this->renderError('套餐不存在');

        $model = new PackageOrder();
        $order_on = createOrderNo();
        $id = $model->addData($packageData,$user_id,$wxapp_id,$order_on);
        if(!$id) $this->renderError('订单创建失败');

        // 微信支付信息
        $payment = Payment::orderPayment($user,[
            'id'        => $id,
            'order_on'  => $order_on,
            'price'     => $packageData['price']
        ],OrderType::USER_MEAL);
        log_write_pay($payment);

        return $this->renderSuccess($payment);

    }
}