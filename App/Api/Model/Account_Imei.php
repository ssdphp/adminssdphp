<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use App\Api\Plugin\Curl;
use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\SsdPHP;

class Account_Imei extends Model {
	
	/**
     * 检查qq号是否已经注册
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $Unionid
     * @return array
     */
    public function CheckUserExistByQQ($qq){
        if(empty($qq)){
            return [];
        }
		$cond =['qq'=>$qq,'login_type'=>2];
        $ret = $this->selectOne($cond,["*"]);
		if(!empty($ret)){
			return $ret;
		}
		return [];
    }
	/**
     * 检查qq号是否已经注册
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $Unionid
     * @return array
     */
    public function CheckUserExistByIMEI($qq){
        if(empty($qq)){
            return [];
        }
		$cond =['imei'=>$qq];
        $ret = $this->selectOne($cond,["*"]);
		if(!empty($ret)){
			return $ret;
		}
		return [];
    }
	/**
     * 检查qq号是否已经注册
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $Unionid
     * @return array
     */
    public function CheckUserExistByPhone($qq){
        if(empty($qq)){
            return [];
        }
		$cond =['phone'=>$qq];
        $ret = $this->selectOne($cond,["*"]);
		if(!empty($ret)){
			return $ret;
		}
		return [];
    }
	/**
     * 检查微信号是否已经注册
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $Unionid
     * @return array
     */
    public function CheckUserExistByWX($qq){
        if(empty($qq)){
            return [];
        }
		$cond =['qq'=>$qq,'login_type'=>1];
        $ret = $this->selectOne($cond,["*"]);
		if(!empty($ret)){
			return $ret;
		}
		return [];
    }

    public function autoCheckAccount($phone,$login_key){
        if(empty($phone) || empty($login_key)){
            return [];
        }
        $cond =['phone'=>$phone,'login_key'=>$login_key];
        $ret = $this->selectOne($cond,["*"]);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }

    public function autoImeiCheckAccount($phone,$login_key){
        if(empty($phone) || empty($login_key)){
            return [];
        }
        $cond =['phone'=>$phone,'login_key'=>$login_key];
        $ret = $this->selectOne($cond,["*"]);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }
	
	/**
     * qq登录注册
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $regData
     * @return array
     */
	public function ByQQReg($regData){

        $regData['qq']         = Input::request('openid','');
        $regData['name']       = Input::request('nickname','');
        $regData['header_img'] = Input::request('figureurl_qq_1','');
        $regData['create_time']= time();
        if(empty($regData['qq']) || empty($regData['name']) || empty($regData['header_img'])){
            return array();
        }
		$u_ret = $this->CheckUserExistByQQ($regData['qq']);
        //{{{登录过
        if(!empty($u_ret['id'])){
            return array(
                'uid'       =>$u_ret['id'],
                'qq'        =>$regData['qq'],
                'name'      =>$regData['name'],
                'status'    =>$u_ret['status'],
                'jifen'     =>$u_ret['jifen'],
                'header_img'=>$regData['header_img'],
            );
        }
        //}}}
		$uid = $this->insert($regData);
		if($uid  < 1){
			return array();
		}
		return array(
		    'uid'       =>$uid,
		    'qq'        =>$regData['qq'],
		    'name'      =>$regData['name'],
            'status'    =>1,
            'jifen'     =>0,
		    'header_img'=>$regData['header_img'],
        );
	}

    /**
     * 登录处理，微信登录，QQ登录
     * @param array $data 基础数据
     * @return array
     */
	public function ForAutoLogin($data=array()){
	    //qq登录处理
	    if($data['login_type'] == 2){
	        return $this->ByQQReg($data);
        }
        //微信登录处理
	    if($data['login_type'] == 1){
            return $this->WxLogin($data);
        }

    }
	
	/**
     * 通过帐号id获取帐号基础信息
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $regData
     * @return array
     */
	public function ByUidGetInfo($id,$feild=["*"]){
		if(empty($id)){
            return [];
        }
		$cond =['id'=>$id];
        $ret = $this->selectOne($cond,$feild);
		if(!empty($ret)){
			return $ret;
		}
		return [];
	}

    /**
     * 更新用户信息
     */
	public function updateInfo($condition,$item){

	    return $this->update($condition,$item);
    }

    /**
     * 微信Sdk 通过code快捷登录
     */
    public function WxLogin($data){
        //微信获取accesstoken的ID
        $code   = Input::request('openid',"");
        $p      = Input::request('rj',0,'intval');
        if(empty($code)){
            return array();
        }
        $pconfig = "OPEN";
        if($p == 1){
            $pconfig="OPENHUOSAHN";
        }
        $config = Config::getField("Weixin",$pconfig);
        $wx_ret = $this->WxByCodeGetAccessToken($code,$config['Appid'],$config['Secret']);
        if($wx_ret['retcode']<1 || empty($wx_ret['retval']['unionid'])){
            return array();
        }
        $AccountInfo = $this->CheckWeixinUserExistByUnionid($wx_ret['retval']['unionid']);
        if(empty($AccountInfo['id'])){//1.未注册。获取用户微信基础用户信息进行注册
            $wx_userinfo = $this->ByAccessTokenGetWeixinUserInfo(
                $wx_ret['retval']['unionid'],
                $wx_ret['retval']['access_token']
            );
            if($wx_userinfo['retcode']<1){
                return array();
            }
            $data['qq']             = $wx_userinfo['retval']['unionid'];
            $data['login_type']     = 1;
            $data['name']           = !empty($wx_userinfo['retval']['nickname'])?$wx_userinfo['retval']['nickname']:"";
            $data['header_img']     = !empty($wx_userinfo['retval']['headimgurl'])?str_replace("/0","/0",$wx_userinfo['retval']['headimgurl']):"";
            $data['create_time']    = time();

            $uid = $this->insert($data);
            if($uid  < 1){
                return array();
            }
            return array(
                'uid'       =>$uid,
                'qq'        =>$data['qq'],
                'name'      =>$data['name'],
                'status'    =>1,
                'jifen'     =>0,
                'header_img'=>$data['header_img'],
            );
        }
        //登录过
        return array(
            'uid'       =>$AccountInfo['id'],
            'qq'        =>$AccountInfo['qq'],
            'name'      =>$AccountInfo['name'],
            'status'    =>$AccountInfo['status'],
            'jifen'     =>$AccountInfo['jifen'],
            'header_img'=>$AccountInfo['header_img'],
        );

    }


    /**
     * 通过微信的unionid检查是否已经是注册过了
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $Unionid
     * @return array
     */
    public function CheckWeixinUserExistByUnionid($Unionid){
        if(empty($Unionid)){
            return [];
        }
        $cond =['qq'=>$Unionid,"login_type"=>1];
        $ret = $this->selectOne($cond,["*"]);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }

    /**
     * 通过code获取access_token
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $code 填写第一步获取的code参数
     * @param $appid 应用唯一标识，在微信开放平台提交应用审核通过后获得
     * @param $secret 应用密钥AppSecret，在微信开放平台提交应用审核通过后获得
     * @param $grant_type 填authorization_code
     * @return array   success =[
    "access_token"=>"ACCESS_TOKEN",接口调用凭证
    "expires_in"=>7200, 	access_token接口调用凭证超时时间，单位（秒）
    "refresh_token"=>"REFRESH_TOKEN", 用户刷新access_token
    "openid"=>"OPENID", 授权用户唯一标识
    "scope"=>"SCOPE", 用户授权的作用域，使用逗号（,）分隔
    "unionid"=>"o6_bmasdasdsad6_2sgVt7hMZOPfL"  当且仅当该移动应用已获得该用户的userinfo授权时，才会出现该字段
    ]
     *                  error=["errcode"=>40029,"errmsg"=>"invalid code"]
     */
    public function WxByCodeGetAccessToken($code,$appid,$secret,$grant_type='authorization_code'){

        if(empty($code) || empty($appid) || empty($secret) || empty($grant_type)){
            return ['retcode'=>-1];
        }
        $URL = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type={$grant_type}";

        $Ret = Functions::RequestCurl($URL,null,[],"GET");
        if(empty($Ret)){
            return [
                'retcode'=>-1,
                'retval'=>null,
                'errcode'=>'40029',
                'errmsg'=>'invalid code',
            ];
        }
        $Ret = json_decode($Ret,true);
        if(empty($Ret) || empty($Ret['unionid'])){

            return [
                'retcode'=>-1,
                'retval'=>null,
                'errcode'=>'40029',
                'errmsg'=>'invalid code',
            ];
        }
        return [
            'retcode'=>1,
            'retval'=>$Ret,
        ];
    }

    /**
     * 通过微信openid|unionid获取微信用户信息
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $openid
     * @param $access_token
     * @return array {
    "openid":"OPENID", 普通用户的标识，对当前开发者帐号唯一
    "nickname":"NICKNAME", 普通用户昵称
    "sex":1, 普通用户性别，1为男性，2为女性
    "province":"PROVINCE",  普通用户个人资料填写的省份
    "city":"CITY", 普通用户个人资料填写的城市
    "country":"COUNTRY", 国家，如中国为CN
    "headimgurl": "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0", 用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空
    "privilege":[ 用户特权信息，json数组，如微信沃卡用户为（chinaunicom）
    "PRIVILEGE1",
    "PRIVILEGE2"
    ],
    "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL" 用户统一标识。针对一个微信开放平台帐号下的应用，同一用户的unionid是唯一的。
    }
     */
    public function ByAccessTokenGetWeixinUserInfo($openid,$access_token){
        $URL = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}";
        if(empty($openid) || empty($access_token)){
            return ['retcode'=>-1];
        }
        $Ret = Functions::RequestCurl($URL,null,[],"GET");
        if(empty($Ret)){
            return [
                'retcode'=>-1,
                'retval'=>null,
                'errcode'=>'40030',
                'errmsg'=>'invalid access_token',
            ];
        }
        $Ret = json_decode($Ret,true);
        if(empty($Ret) || empty($Ret['unionid'])){

            return [
                'retcode'=>-1,
                'retval'=>null,
                'errcode'=>'40030',
                'errmsg'=>'invalid access_token',
            ];
        }
        return [
            'retcode'=>1,
            'retval'=>$Ret,
        ];
    }


    /**
     * phone登录注册
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $regData
     * @return array
     */
    public function ByPhoneReg($regData){



        $regData['create_time'] =$ctime= time();
        if(empty($regData['phone'])){
            return array();
        }
        $randImg = "/assets/home/img/header/".rand(1,12).".jpg";
        $regData['header_img']= $randImg;
        $u_ret = $this->CheckUserExistByPhone($regData['phone']);
        $imei_ret = $this->CheckUserExistByIMEI($regData['imei']);
        //{{{登录过
        if(!empty($u_ret['id'])){
            $d = array(
                'login_key'=>$regData['login_key'],
            );

            $s = $this->updateInfo(array('id'=>$u_ret['id']),$d);

            return array(
                'uid'               =>$u_ret['id'],
                'no'                =>$u_ret['create_time'].str_pad($u_ret['id'], 8, "0", STR_PAD_LEFT),
                'phone'             =>!empty($u_ret['phone'])?$u_ret['phone']:"游客",
                'imei'              =>$u_ret['imei']??"",
                'isbind'            =>1,
                'status'            =>$u_ret['status'],
                'vip_expire_time'   =>$u_ret['vip_expire_time'],
                'header_img'=>Functions::getImgUrl(!empty($u_ret['header_img'])?$u_ret['header_img']:"{$randImg}"),
            );
        }
        //}}}

        $regData['isbind'] = 1;
        //当前imei没有绑定过
        if(!empty($imei_ret['id'])){

            unset($regData['create_time']);
            $regData['update_time']=time();
            $s = $this->updateInfo(array('id'=>$imei_ret['id']),$regData);
            $uid = $imei_ret['id'];

        }else{
            $uid = $this->insert($regData);
            if($uid  < 1){
                return array();
            }
        }
        return array(
            'uid'       =>$uid,
            'no'        =>$ctime.str_pad($uid, 8, "0", STR_PAD_LEFT),
            'phone'                 =>$regData['phone'],
            'imei'                  =>$regData['imei']??"",
            'isbind'                =>1,
            'status'                =>1,
            'vip_expire_time'       => !empty($imei_ret['vip_expire_time']) ? $imei_ret['vip_expire_time']:0,
            'header_img'=>Functions::getImgUrl(!empty($regData['header_img'])?$regData['header_img']:"{$randImg}"),
        );
    }
    /**
     * phone登录注册
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $regData
     * @return array
     */
    public function ByImeiReg($regData){



        $regData['create_time']= time();
        if(empty($regData['imei'])){
            return array();
        }
        $randImg = "/assets/home/img/header/".rand(1,12).".jpg";
        $regData['header_img']= $randImg;
        $u_ret = $this->CheckUserExistByIMEI($regData['imei']);
        //{{{登录过
        if(!empty($u_ret['id'])){
            $s = $this->updateInfo(array('id'=>$u_ret['id']),array(
                'login_key'=>$regData['login_key'],
                'update_time'=>time(),
            ));

            return array(
                'uid'       =>$u_ret['id'],
                'no'       =>$u_ret['create_time'].str_pad($u_ret['id'], 8, "0", STR_PAD_LEFT),
                'phone'        =>!empty($u_ret['phone'])?$u_ret['phone']:"游客",
                'imei'        =>$u_ret['imei'],
                'isbind'        =>$u_ret['isbind'],
                'lingqu_status' =>$u_ret['lingqu_status'],
                'status'    =>$u_ret['status'],
                'vip_expire_time'     =>$u_ret['vip_expire_time'],
                'header_img'=>Functions::getImgUrl(!empty($u_ret['header_img'])?$u_ret['header_img']:"{$randImg}"),
            );
        }
        //}}}
        $uid = $this->insert($regData);
        if($uid  < 1){
            return array();
        }
        return array(
            'uid'       =>$uid,
            'no'        =>$regData['create_time'].str_pad($uid, 8, "0", STR_PAD_LEFT),
            'phone'        =>!empty($regData['phone'])?$regData['phone']:"游客",
            'status'    =>1,
            'imei'        =>$regData['imei'],
            'isbind'        =>2,
            'lingqu_status' =>2,
            'vip_expire_time'     =>0,
            'header_img'=>Functions::getImgUrl(!empty($regData['header_img'])?$regData['header_img']:"{$randImg}"),
        );
    }
}