<?php

namespace app\store\model;

use app\common\model\User as UserModel;

use app\store\model\dealer\User as DealerUserModel;
use app\store\model\user\GradeLog as GradeLogModel;
use app\store\model\user\PointsLog as PointsLogModel;
use app\store\model\user\BalanceLog as BalanceLogModel;
use app\common\enum\user\balanceLog\Scene as SceneEnum;
use app\common\enum\user\grade\log\ChangeType as ChangeTypeEnum;
use app\common\library\helper;

/**
 * 用户模型
 * Class User
 * @package app\store\model
 */
class User extends UserModel
{
    /**
     * 获取当前用户总数
     * @param null $day
     * @return int|string
     * @throws \think\Exception
     */
    public function getUserTotal($day = null)
    {
        if (!is_null($day)) {
            $startTime = strtotime($day);
            $this->where('create_time', '>=', $startTime)
                ->where('create_time', '<', $startTime + 86400);
        }
        return $this->where('is_delete', '=', '0')->count();
    }

    /**
     * 获取用户列表
     * @param string $nickName 昵称
     * @param int $gender 性别
     * @param int $grade 会员等级
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($nickName = '', $gender = -1, $grade = null)
    {
        // 检索：微信昵称
        !empty($nickName) && $this->where('nickName', 'like', "%$nickName%");
        // 检索：性别
        if ($gender !== '' && $gender > -1) {
            $this->where('gender', '=', (int)$gender);
        }
        // 检索：会员等级
        $grade > 0 && $this->where('grade_id', '=', (int)$grade);
        // 获取用户列表
        return $this->where('is_delete', '=', '0')
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
    }

    /**
     * 删除用户
     * @return bool|mixed
     */
    public function setDelete()
    {
        return $this->transaction(function () {
            // 标记为已删除
            return $this->save(['is_delete' => 1]);
        });
    }
}
