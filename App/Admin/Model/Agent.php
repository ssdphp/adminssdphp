<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Agent extends ReturnVal {

    public static $AdminConfig=array(
        'LoginSessionKey'=>'adminsessionkey',
    );


    /**
     * 后台用户登录
     * @param $loginData
     * @return array
     */
    public function Login($loginData){
        $retval = $this->getReturnVal();
        if(empty($loginData['username']) || empty($loginData['password'])){
            $retval["status"]=0;
            $retval["code"]=1000;
            return $retval;
        }
        $loginData['password'] = md5($loginData['password']);
        $data = $this->selectOne($loginData);
        if(
            !empty($data['uid'])
            && $data['username'] == $loginData['username']
            && $data['password'] == $loginData['password']
        ){
            $retval['ret']=$data;
            return $retval;
        }
        $retval["status"]=0;
        $retval["code"]=1000;
        return $retval;
    }



    /**
     * 添加
     * @param $data
     * @return array
     */
    public function _add($data)
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

        if(isset($data['ditch_id']) ){
            $did = $data['ditch_id'];
            unset($data['ditch_id']);
        }

        $data['password']=md5($data['password']);
        $data['reg_time']=time();
        $data['reg_ip']=ip2long($_SERVER['REMOTE_ADDR']);
        $id = $this->insert($data);


        if(!empty($id)){
            if(!empty($did)){
                //添加渠道修改
                $Ditch = new Softwaretemplate();
                $dinfo = $Ditch->findOne(array('id'=>$did));
                if(empty($dinfo['tui_uid'])&&!empty($dinfo['id'])){
                    $s = $Ditch->edit(array('id'=>$did,'tui_uid'=>$id));
                }
            }
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1004;
        return $retval;
    }
    /**
     * 添加
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
        $insertData=$data;
        if(!empty($data['password'])){
            $insertData['password'] = md5($data['password']);
        }else{
            unset($insertData['password']);
        }
        unset($insertData['uid']);
        $id = $this->update(array("uid"=>intval($data['uid'])),$insertData);

        if(!empty($id)){
            $did = !empty($data['ditch_id'])?$data['ditch_id']:0;
            if(!empty($id)){
                if(!empty($did)){
                    //添加渠道修改
                    $Ditch = new Softwaretemplate();
                    $dinfo = $Ditch->findOne(array('id'=>$did));
                    if(empty($dinfo['tui_uid'])&&!empty($dinfo['id'])){
                        $s = $Ditch->edit(array('id'=>$did,'tui_uid'=>$data['uid']));
                    }
                }
                $retval['ret']=$id;
                return $retval;
            }

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