<?php

namespace app\api\model;

use think\Model;

class Province extends Model
{
    protected $table = 'yc_area';

    public function getAreaList($where)
    {
        $model = new Province();
        $data = $model->where($where)->select();
        return $data;
    }

    public function getAreaInfo($where)
    {
        $model = new Province();
        $data = $model->where($where)->find();
        return $data;
    }
}