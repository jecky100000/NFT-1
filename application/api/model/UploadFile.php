<?php

namespace app\api\model;

use app\common\model\UploadFile as UploadFileModel;

/**
 * 文件库模型
 * Class UploadFile
 * @package app\api\model
 */
class UploadFile extends UploadFileModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'create_time',
    ];

    /**
     * 查询图片集合
     * @param $ids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function paths($ids)
    {
        $banners = [];
        $uploadFiles = UploadFile::whereIn('file_id',$ids)->select();
        if(count($uploadFiles))
        {
            foreach ($uploadFiles as $file)
            {
                $banners[] = $file['file_path'];
            }
        }
        return $banners;
    }
}
