<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use App\Common\Tool\Functions;
use SsdPHP\Core\Model;

class Admin extends ReturnVal {

    public static $AdminConfig=array(
        'UserDataKey'=>'UserInfo',
    );

    //0-正常 1-禁用
    private $status = array(
        1=>'正常',
        2=>'禁用',
    );

    //0-正常 1-禁用
    private $sex = array(
        1=>'男',
        2=>'女',
    );

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * 后台用户登录
     * @param $loginData
     * @return array
     */
    public function Login($loginData){
        if(empty($loginData['username']) || empty($loginData['password'])){

            return -1;
        }
        $loginData['password'] = md5($loginData['password'].$loginData['username']);
        $data = $this->selectOne(['username'=>$loginData['username']]);
        if(
            !empty($data['uid'])
            && $data['username'] == $loginData['username']
            && $data['password'] == $loginData['password']
        ){
            return $data;
        }
        return -2;
    }


    /**
     * 添加
     * @param $data
     * @return array
     */
    public function add($data)
    {
        $retval = $this->getReturnVal();
        if(empty($data) || empty($data['username']) || empty($data['password'])){
            $retval['ret']=0;
            $retval['code']=0;
            return $retval;
        }

        if(isset($data['uid'])){
            unset($data['uid']);
        }


        $data['password']=md5($data['password']);
        $data['reg_time']=time();
        $data['reg_ip']=ip2long($_SERVER['REMOTE_ADDR']);
        $id = $this->insert($data);


        if(!empty($id)){
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1004;
        return $retval;
    }
    /**
     * 修改
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $retval = $this->getReturnVal();
        if(empty($data) || empty($data['uid'])){
            $retval['ret']=0;
            $retval['code']=1005;
            return $retval;
        }
        $insertData=array(
            'username'=>$data['username'],
            'nickname'=>$data['nickname'],
            'status'=>$data['status'],
            'sex'=>$data['sex'],
            'link_qq'=>!empty($data['link_qq'])?$data['link_qq']:"",
            'link_phone'=>!empty($data['link_phone'])?$data['link_phone']:"",
            'update_time'=>time(),
        );
        if(!empty($data['password'])){
            $insertData['password'] = md5($data['password']);
            $insertData['pwd_str'] =Functions::opensslEncrypt($data['password']);
        }
        $id = $this->update(array("uid"=>intval($data['uid'])),$insertData);

        if(!empty($id)){
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1005;
        return $retval;
    }

    /**
     * 通过uid获取后台用户信息
     * @param $uid
     * @param array $feild
     * @return array|mixed
     */
    public function getInfoByUid($uid,$feild=array("*")){

        if(empty($uid)){
            return array();
        }
        $ret = $this->selectOne(array("uid"=>$uid),$feild);
        return !empty($ret)?$ret:array();
    }
}