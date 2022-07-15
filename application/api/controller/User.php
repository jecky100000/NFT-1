<?php

namespace app\api\controller;

use app\api\model\LetterLog;
use app\api\model\SmsCode;
use app\api\model\User as UserModel;
use app\api\model\Wxapp;
use app\common\exception\BaseException;
use app\common\library\wechat\WxUser;
use think\Log;
use think\Validate;

/**
 * 用户管理
 * Class User
 * @package app\api
 */
class User extends Controller
{
    /**
     * 短信发送
     * @return array
     */
    public function sms()
    {
        $params = $this->request->param();
        $val = new Validate([
            'mobile'                => 'require',
        ],[
            'mobile.require'        => '手机号不能为空',
        ]);
        if(!$val->check($params))
        {
            return $this->renderError($val->getError());
        }

        try {
            SmsCode::sms($params['mobile'],$params['wxapp_id']);
        }catch (\Exception $e)
        {
            return $this->renderError($e->getMessage());
        }

        return $this->renderSuccess();
    }

    /**
     * 手机号用户注册
     * @return array
     * @throws \think\exception\DbException
     */
    public function mobile_login()
    {
        $user = [];
        try {
            $user = $this->getUser();
        }catch (\Exception $e)
        {

        }

        $params = $this->request->param();
        $params['user'] = $user;
        $val = new Validate([
            'mobile'                => 'require',
            'code'                  => 'require',
            'password'              => 'require',
        ],[
            'mobile.require'        => '手机号不能为空',
            'code.require'          => '验证码不能为空',
            'password.require'      => '密码不能为空',
        ]);
        if(!$val->check($params))
        {
            return $this->renderError($val->getError());
        }
        $code = $params['code'];
        // 短信验证
        $smsModle = new SmsCode();
        $smsModle->ver($params);

        $model = new UserModel;
        return $this->renderSuccess([
            'user_id' => $model->mobile_login($params,1),   // 注册
            'token' => $model->getToken()
        ]);
    }

    /**
     * 手机号用户重置密码
     * @return array
     * @throws \think\exception\DbException
     */
    public function mobile_reset_pas()
    {
        $params = $this->request->param();
        $val = new Validate([
            'mobile'                => 'require',
            'code'                  => 'require',
            'password'              => 'require',
        ],[
            'mobile.require'        => '手机号不能为空',
            'code.require'          => '验证码不能为空',
            'password.require'      => '密码不能为空',
        ]);
        if(!$val->check($params))
        {
            return $this->renderError($val->getError());
        }
        $code = $params['code'];
        $model = $params['mobile'];
        $password = $params['password'];
        // 短信验证
        $smsModle = new SmsCode();
        $smsModle->ver($params);

        // 查询用户
        $user = UserModel::detail(['mobile'=>$model]);
        if(!$user) return $this->renderError('用户不存在');
        // 修改用户
        $user->password = md5($password);
        $user->save();

        $model = new UserModel;
        return $this->renderSuccess([
            'user_id' => $model->mobile_login($params,4),   // 重置密码
            'token' => $model->getToken()
        ]);
    }

    /**
     * 手机号用户登陆
     * @return array
     * @throws \think\exception\DbException
     */
    public function mobile_login_d()
    {
        $params = $this->request->param();
        $val = new Validate([
            'mobile'                => 'require',
            'password'              => 'require',
        ],[
            'mobile.require'        => '手机号不能为空',
            'password.require'      => '密码不能为空',
        ]);
        if(!$val->check($params))
        {
            return $this->renderError($val->getError());
        }
        $model = new UserModel;
        return $this->renderSuccess([
            'user_id' => $model->mobile_login($params,2),   // 账号密码登录
            'token' => $model->getToken()
        ]);
    }

    /**
     * 手机号验证码登录
     * @return array
     * @throws \think\exception\DbException
     */
    public function mobile_code_login()
    {
        $params = $this->request->param();
        $val = new Validate([
            'mobile'                => 'require',
            'code'                  => 'require',
        ],[
            'mobile.require'        => '手机号不能为空',
            'code.require'          => '验证码不能为空',
        ]);

        if(!$val->check($params))
        {
            return $this->renderError($val->getError());
        }

        // 短信验证
        $smsModle = new SmsCode();
        $smsModle->ver($params);

        $model = new UserModel;
        return $this->renderSuccess([
            'user_id' => $model->mobile_login($params,3),   // 验证码登录
            'token' => $model->getToken()
        ]);
    }

    /**
     * 微信手机号获取
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function mobile()
    {
        $user = $this->getUser();
        $code = $this->request->param('code');
        if(!$code) $this->renderError('code不能为空');
        // 获取当前小程序信息
        $wxConfig = Wxapp::getWxappCache();
        // 验证appid和appsecret是否填写
        if (empty($wxConfig['app_id']) || empty($wxConfig['app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-小程序设置] 填写appid 和 appsecret']);
        }
        // 微信登录 (获取session_key)
        $WxUser = new WxUser($wxConfig['app_id'], $wxConfig['app_secret']);
        $re = $WxUser->getUserPhone($code);
        try {
            $mobile = $re['phone_info']['phoneNumber'];
            $user->mobile = $mobile;
            $user->save();
        }catch (\Exception $e)
        {
            return $this->renderError('获取失败');
        }

        return $this->renderSuccess($re);
    }

    /**
     * 用户自动登录
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login()
    {
        $model = new UserModel;
        return $this->renderSuccess([
            'user_id' => $model->login($this->request->post()),
            'token' => $model->getToken()
        ]);
    }

    /**
     * 当前用户详情
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        $mobile = $userInfo['mobile'];

        $letter = new LetterLog();
        $userInfo['iswritenum'] = $letter->isWriteNum($mobile);

        return $this->renderSuccess(compact('userInfo'));
    }

}
