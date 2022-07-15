<?php

namespace app\api\model;

use app\common\model\Setting as SettingModel;

/**
 * 系统设置模型
 * Class Setting
 * @package app\api\model
 */
class Setting extends SettingModel
{
    /**
     * 获取积分名称
     * @return string
     */
    public static function getPointsName()
    {
        return static::getItem('points')['points_name'];
    }

    /**
     * 获取微信订阅消息设置
     */
    public static function getSubmsg()
    {
        $data = [];
        foreach (static::getItem('submsg') as $groupName => $group) {
            foreach ($group as $itemName => $item) {
                $data[$groupName][$itemName]['template_id'] = $item['template_id'];
            }
        }
        return $data;
    }

    /**
     * 查询配置
     * @param $wxapp_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getValues($wxapp_id,$key = 'store')
    {
        $setting = new Setting();
        $setting::$wxapp_id = null;
        $data = $setting->where(['wxapp_id'=>$wxapp_id,'key'=>$key])->find();
        return $data?$data['values']:'';
    }

}
