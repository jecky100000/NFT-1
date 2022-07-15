<?php

namespace app\store\controller\sms;

use app\store\controller\Controller;
use app\store\model\LetterLog as LetterLogModel;

class Letterlog extends Controller
{
    /**
     * 模板列表
     * @return mixed
     */
    public function index()
    {
        $params = $this->request->param();
        $mobile = isset($params['mobile'])?$params['mobile']:'';
        $form_mobile = isset($params['form_mobile'])?$params['form_mobile']:'';

        $where = [];
        if($mobile) $where['mobile'] = ['like','%'.$mobile.'%'];
        if($form_mobile) $where['form_mobile'] = ['like','%'.$form_mobile.'%'];

        $model = new LetterLogModel;
        $list = $model->order('id','DESC')
            ->with(['formuser','touser'])
            ->where($where)->paginate(15, false, [
            'query' => \request()->request()
        ]);
        $typeStr = Template::$typeStr;
        return $this->fetch('index', compact('list','typeStr'));
    }
}