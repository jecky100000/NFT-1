<?php

namespace app\api\controller;

use app\api\service\Address;

class Other extends Controller
{
    /**
     * 测试
     *
     */
    public function test()
    {

        $addr = '身份证号：51250119910927226x 收货地址张三收货地址：成都市武侯区美领馆路11号附2号 617000  136-3333-6666';
//        $re = ExService::addrAnalysis($addr);
        $re = Address::smart_parse($addr);
        dd($re);
    }

    /**
     * 地址智能解析
     * @return void
     */
    public function addr_analysis()
    {
        $addr = $this->request->param('addr');
        $re = Address::smart_parse($addr);
        return $this->renderSuccess($re);
    }
}