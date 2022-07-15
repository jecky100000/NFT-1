<?php

namespace app\store\controller;

use app\store\model\Printer as PrinterModel;
use app\store\model\Setting as SettingModel;
use app\common\library\sms\Driver as SmsDriver;

/**
 * 系统设置
 * Class Setting
 * @package app\store\controller
 */
class Setting extends Controller
{
    /**
     * 商城设置
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function store()
    {
        return $this->updateEvent('store');
    }

    /**
     * 上传设置
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function storage()
    {
        return $this->updateEvent('storage');
    }

    /**
     * 更新商城设置事件
     * @param $key
     * @param $vars
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    private function updateEvent($key, $vars = [])
    {
        if (!$this->request->isAjax()) {
            $vars['values'] = SettingModel::getItem($key);
            return $this->fetch($key, $vars);
        }
        $model = new SettingModel;
        if ($model->edit($key, $this->postData($key))) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

}
