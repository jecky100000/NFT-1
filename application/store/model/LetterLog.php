<?php

namespace app\store\model;

use app\common\model\BaseModel;

class LetterLog extends BaseModel
{
    protected $table = 'yc_letter_log';

    /**
     * 发送用户
     * @return \think\model\relation\HasOne
     */
    public function formuser()
    {
        return $this->hasOne(User::class,'user_id','form_user_id')
            ->field('user_id,nickName,mobile');
    }


    /**
     * 接收用户
     * @return \think\model\relation\HasOne
     */
    public function touser()
    {
        return $this->hasOne(User::class,'user_id','to_user_id')
            ->field('user_id,nickName,mobile');
    }
}