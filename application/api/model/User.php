<?php

namespace app\api\model;

use think\Cache;
use app\api\model\dealer\Referee as RefereeModel;
use app\api\model\dealer\Setting as DealerSettingModel;
use app\common\model\User as UserModel;
use app\common\library\helper;
use app\common\library\wechat\WxUser;
use app\common\exception\BaseException;
use think\Exception;
use think\Log;

/**
 * 用户模型类
 * Class User
 * @package app\api\model
 */
class User extends UserModel
{
    private $token;

    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'open_id',
        'is_delete',
        'wxapp_id',
        'create_time',
        'update_time'
    ];

    /**
     * 验证用户剩余次数
     * @param $user_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function isNum($user_id)
    {
        $data = User::where([
            'user_id'=>$user_id
        ])->where('num','>',0)->find();
        if(!$data) throw new BaseException(['code'=>0,'msg'=>'剩余次数不足,请充值']);
        return $data;
    }

    /**
     * 减用户次数
     * @param $user_id
     */
    public static function jianNum($user_id)
    {
        User::where([
            'user_id'=>$user_id
        ])->inc('num',-1)->update();
    }

    /**
     * 获取用户信息
     * @param $token
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function getUser($token)
    {
        $re = '';
        try {
            $openId = Cache::get($token)['openid'];
            $re = self::detail(['open_id' => $openId], []);
        }catch (\Exception $e)
        {
            $mobile = Cache::get($token);
            $re = self::detail(['mobile' => $mobile], []);
        }
        if(!$re) return false;
        return $re;
    }

    /**
     * 手机号用户登陆
     * $type 1注册 2登陆
     * @param $params
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function mobile_login($params,$type)
    {
        $mobile = $params['mobile'];
        $password = isset($params['password'])?$params['password']:'';

        // 自动注册用户
        $user_id = $this->mobile_register($mobile, $password,$params,$type);

        // 生成token (session3rd)
        $this->token = $this->token($mobile);
        // 记录缓存, 7天
        Cache::set($this->token, $mobile, 86400 * 7);
        return $user_id;
    }

    /**
     * 手机号自动注册用户
     * @param $mobile
     * @param $password
     * @param $type 1注册 2账号密码登陆 3验证码登录 4重置密码
     * @return mixed
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    private function mobile_register($mobile, $password,$params,$type)
    {
        $wxapp_id =  $params['wxapp_id'];
        $user = '';

        if($type == 3)
        {
            // 查询用户是否已存在
            $user = self::detail(['mobile' => $mobile]);
        }
        else
        {
            // 查询用户是否已存在
            $user = self::detail(['mobile' => $mobile,'password'=>md5($password)]);
        }

        if($type == 2)
        {
            if(!$user) throw new BaseException(['msg' => '用户不存在或密码错误']);
            return $user['user_id'];
        }

        $model = $user ?: $this;
        if($type != 2 && $user == '')
        {
            $this->startTrans();
            try {
                // 保存/更新用户记录
                if (!$model->allowField(true)->save([
                    'mobile'        => $mobile,
                    'password'      => md5($password),
                    'wxapp_id'      => $wxapp_id,
                ])) {
                    throw new BaseException(['msg' => '注册失败']);
                }
                $this->commit();
            } catch (\Exception $e) {
                $this->rollback();
                throw new BaseException(['msg' => '手机号不能重复注册']);
            }
        }
        return $model['user_id'];
    }

    /**
     * 用户登录
     * @param array $post
     * @return string
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login($post)
    {
        // 微信登录 获取session_key
        $session = $this->wxlogin($post['code']);
        // 自动注册用户
        $refereeId = isset($post['referee_id']) ? $post['referee_id'] : null;
        $userInfo = helper::jsonDecode(htmlspecialchars_decode($post['user_info']));
        $user_id = $this->register($session['openid'], $userInfo, $refereeId);
        // 生成token (session3rd)
        $this->token = $this->token($session['openid']);
        // 记录缓存, 7天
        Cache::set($this->token, $session, 86400 * 7);
        return $user_id;
    }

    /**
     * 获取token
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 微信登录
     * @param $code
     * @return array|mixed
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    private function wxlogin($code)
    {
        // 获取当前小程序信息
        $wxConfig = Wxapp::getWxappCache();
        // 验证appid和appsecret是否填写
        if (empty($wxConfig['app_id']) || empty($wxConfig['app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-小程序设置] 填写appid 和 appsecret']);
        }
        // 微信登录 (获取session_key)
        $WxUser = new WxUser($wxConfig['app_id'], $wxConfig['app_secret']);
        if (!$session = $WxUser->sessionKey($code)) {
            throw new BaseException(['msg' => $WxUser->getError()]);
        }
        return $session;
    }

    /**
     * 生成用户认证的token
     * @param $openid
     * @return string
     */
    public function token($openid)
    {
        $wxapp_id = self::$wxapp_id;
        // 生成一个不会重复的随机字符串
        $guid = \getGuidV4();
        // 当前时间戳 (精确到毫秒)
        $timeStamp = microtime(true);
        // 自定义一个盐
        $salt = 'token_salt';
        return md5("{$wxapp_id}_{$timeStamp}_{$openid}_{$guid}_{$salt}");
    }

    /**
     * 自动注册用户
     * @param $open_id
     * @param $data
     * @param int $refereeId
     * @return mixed
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    private function register($open_id, $data, $refereeId = null)
    {
        // 查询用户是否已存在
        $user = self::detail(['open_id' => $open_id]);
        Log::error($user);
        $model = $user ?: $this;
        $this->startTrans();
        try {
            // 保存/更新用户记录
            if (!$model->allowField(true)->save(array_merge($data, [
                'open_id' => $open_id,
                'wxapp_id' => self::$wxapp_id
            ]))) {
                throw new BaseException(['msg' => '用户注册失败']);
            }
            // 记录推荐人关系
            if (!$user && $refereeId > 0) {
                RefereeModel::createRelation($model['user_id'], $refereeId);
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }
        return $model['user_id'];
    }

    /**
     * 个人中心菜单列表
     * @return array
     */
    public function getMenus()
    {
        $menus = [
            'address' => [
                'name' => '收货地址',
                'url' => 'pages/address/index',
                'icon' => 'map'
            ],
            'coupon' => [
                'name' => '领券中心',
                'url' => 'pages/coupon/coupon',
                'icon' => 'lingquan'
            ],
            'my_coupon' => [
                'name' => '我的优惠券',
                'url' => 'pages/user/coupon/coupon',
                'icon' => 'youhuiquan'
            ],
            'sharing_order' => [
                'name' => '拼团订单',
                'url' => 'pages/sharing/order/index',
                'icon' => 'pintuan'
            ],
            'my_bargain' => [
                'name' => '我的砍价',
                'url' => 'pages/bargain/index/index?tab=1',
                'icon' => 'kanjia'
            ],
            'dealer' => [
                'name' => '分销中心',
                'url' => 'pages/dealer/index/index',
                'icon' => 'fenxiaozhongxin'
            ],
            'help' => [
                'name' => '我的帮助',
                'url' => 'pages/user/help/index',
                'icon' => 'help'
            ],
        ];
        // 判断分销功能是否开启
        if (DealerSettingModel::isOpen()) {
            $menus['dealer']['name'] = DealerSettingModel::getDealerTitle();
        } else {
            unset($menus['dealer']);
        }
        return $menus;
    }

}
