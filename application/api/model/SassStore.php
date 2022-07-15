<?php

namespace app\api\model;

use app\common\exception\BaseException;
use app\common\model\BaseModel;

class SassStore extends BaseModel
{
    protected $table = 'yc_sass_store';

    /**
     * 验证商户剩余次数
     * @param $user_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function isNum($user_id)
    {
        $data = SassStore::where([
            'user_id'=>$user_id
        ])->where('num','>',0)->find();
        if(!$data) throw new BaseException(['code'=>0,'msg'=>'剩余次数不足,请充值']);
        return $data;
    }

    /**
     * 减商户次数
     * @param $user_id
     */
    public static function jianNum($user_id)
    {
        SassStore::where([
            'user_id'=>$user_id
        ])->inc('num',-1)->update();
    }
}