<?php

namespace app\api\controller;

use app\api\model\UserAddr as UserAddrModel;
use think\Validate;

class UserAddr extends Controller
{
    /**
     * 删除
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function del()
    {
        $id = $this->request->param('id');
        $user = $this->getUser();
        $user_id = $user['user_id'];
        if($id) UserAddrModel::where(['user_id'=>$user_id,'id'=>$id])
            ->update(['is_delete'=>1]);
        return $this->renderSuccess();
    }

    /**
     * 详情
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        $id = $this->request->param('id');
        $user = $this->getUser();
        $user_id = $user['user_id'];
        $where = [];
        $where['is_delete'] = 0;
        $where['user_id'] = $user_id;
        if($id)
        {
            $where['id'] = $id;
        }
        else
        {
            $where['is_default'] = 1;
        }
        $data = UserAddrModel::where($where)->find();
        return $this->renderSuccess($data);
    }

    /**
     * 列表
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list()
    {
        $type = $this->request->param('type');
        $user = $this->getUser();
        $where = [];
        $where['user_id'] = $user['user_id'];
        if($type) $where['type'] = $type;
        $data = UserAddrModel::where($where)
            ->where('is_delete',0)->order('id','DESC')->select();
        return $this->renderSuccess($data);
    }

    /**
     * 添加地址
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $params = $this->request->param();
        $val = new Validate([
            'province_id'   => 'require',
            'city_id'       => 'require',
            'area_id'       => 'require',
            'addr'          => 'require',
            'is_default'    => 'require',
        ],[
            'province_id.require'   => '请选择省',
            'city_id.require'       => '请选择市',
            'area_id.require'       => '请选择区',
            'addr.require'          => '请填写详细地址',
            'is_default.require'    => '默认值不能为空',
        ]);
        if(!$val->check($params))
        {
            return $this->renderError($val->getError());
        }
        $user = $this->getUser();
        $params['user_id'] = $user['user_id'];
        unset($params['token']);

        $model = new UserAddrModel();
        $model->save($params);


        return $this->renderSuccess();
    }
}