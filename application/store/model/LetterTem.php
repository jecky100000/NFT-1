<?php

namespace app\store\model;

use app\common\model\BaseModel;
use think\Db;

class LetterTem extends BaseModel
{
    protected $table = 'yc_letter_tem';

    /**
     * 编辑记录
     * @param $data
     * @return bool|int
     */
    public function edit($data)
    {
        return $this->allowField(true)->save($data);
    }
}