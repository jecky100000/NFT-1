<?php

namespace app\store\controller\sms;

use app\store\controller\Controller;
use app\store\model\LetterTem as LetterTemModel;

class Template extends Controller
{
    // 类型 1普通 2早安 3晚安 4吐味情话 5毒鸡汤 6学习提醒 7注册
    public static $typeStr = [
        1       =>          '普通',
        2       =>          '早安',
        3       =>          '晚安',
        4       =>          '土味情话',
        5       =>          '毒鸡汤',
        6       =>          '学习提醒',
        7       =>          '注册',
        8       =>          '注册',
    ];

    /**
     * 模板列表
     * @return mixed
     */
    public function index()
    {
        $model = new LetterTemModel;
        $list = $model->select();
        $typeStr = Template::$typeStr;
        return $this->fetch('index', compact('list','typeStr'));
    }

    /**
     * 编辑文章分类
     * @param $id
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 分类详情
        $model = LetterTemModel::get($id);
        if (!$this->request->isAjax()) {
            return $this->fetch('edit', compact('model'));
        }
        // 更新记录
        if ($model->edit($this->postData('template'))) {
            return $this->renderSuccess('更新成功', url('sms.template/index'));
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

}