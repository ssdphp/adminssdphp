<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Session\Session;
use SsdPHP\SsdPHP;
use SsdPHP\Core\Config;

class Admin_Auth_Group_Access extends ReturnVal {

    //0-正常 1-禁用
    public $status = array(
        0=>'正常',
        1=>'禁用',
    );

    /**
     * 获取所有
     * @param array $cond
     * @param array $feild
     * @return mixed
     */
    public function findAll($cond=array(), $feild=array("uid",'group_id'), $left=""){

        $a = $this->select($cond,$feild,"","",$left,"Inner Join")->items;

        return $a;
    }

    /**
     * 通过uid获取权限组
     * @param $uid
     */
    public function ByUidGetAccess($uid){
        $pre = Config::getField('mysql','main')[0]["prefix"];
        $a = $this->select(array('uid'=>$uid,'status'=>0),array('*'),"","",array(
            "{$pre}admin_auth_group"=>"{$pre}admin_auth_group_access.group_id={$pre}admin_auth_group.id"
        ),"Inner Join")->items;
        if(!empty($a)){
            $ret = array('rules'=>'');
            foreach ($a as $v){
                $ret['rules'] .= $v['rules'].",";
                $ret['group_id'][$v['group_id']] = $v['group_id'];

            }

            $ret['rules'] = trim($ret['rules'],",");
            $ret['default_url'] = !empty($a[0]['default_url'])?$a[0]['default_url']:'/index/index';

            return $ret;
        }
        return array();
    }

    /**
     * 通过条件获取一条记录
     * @param array $cond
     * @param array $feild
     * @return array|mixed
     */
    public function findOne($cond=array(),$feild=['*']){
        $ret = $this->selectOne($cond,$feild);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }

    /**
     * 修改
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $retval = $this->getReturnVal();
        if(empty($data) || empty($data['id'])){
            $retval['ret']=0;
            $retval['code']=1005;
            return $retval;
        }
        $id = $this->update(array("id"=>intval($data['id'])),$data);

        if(!empty($id)){
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1005;
        return $retval;
    }

    public static $rules=array();
    public static $group_ids=array();

    public static function ISROOT(){
        $ROOT_GROUP_ID =  Config::getField('admin','ROOT_GROUP_ID',1);
        if(in_array($ROOT_GROUP_ID,self::$group_ids) || UID == $ROOT_GROUP_ID){
            return true;
        }
        return false;
    }
    /**
     * 权限检测
     * @return bool
     */
    public function AccessCheck($uid){

        //是否开启缓存
        $ACCESS_CACHE_ON    =  Config::getField('admin','ACCESS_CACHE_ON',false);
        //超级管理员账号
        $ROOT_GROUP_ID      =  Config::getField('admin','ROOT_GROUP_ID',1);
        //超级管理员权限直接通过
        if($ROOT_GROUP_ID == $uid ){
            return true;
        }
        $Admin_Auth_Group_Access = new Admin_Auth_Group_Access();
        $menu=strtolower(SsdPHP::getController()."/".SsdPHP::getAction());
        $Admin_Menu = new Admin_Menu();
        $menuInfo = $Admin_Menu->getMenuByUrl($menu);

        //开启了缓存
        if($ACCESS_CACHE_ON && !empty($adminInfo['rules'])){
            $adminConfig = Admin::$AdminConfig;
            $adminInfo = Session::get($adminConfig['UserDataKey']);

            if(empty($adminInfo['group_ids'])){
                return false;
            }
            if(empty(self::$rules)){
                self::$rules=explode(",",$adminInfo['rules']);
            }
            if(empty(self::$group_ids)){
                self::$group_ids=$adminInfo['group_ids'];
            }
            //超级管理员权限直接通过
            if(in_array($ROOT_GROUP_ID,$adminInfo['group_ids'])){
                return true;
            }
            //菜单权限
            if(in_array($menuInfo['id'],self::$rules)){
                return true;
            }
            return false;
        }
        //时时权限
        $access = $Admin_Auth_Group_Access->ByUidGetAccess($uid);
        if(empty($access['group_id'])){
            return false;
        }
        if(empty(self::$rules)){
            self::$rules=array_unique(explode(",",$access['rules']));
        }
        if(empty(self::$group_ids)){
            self::$group_ids=$access['group_id'];
        }



        //菜单权限
        if(in_array($menuInfo['id'], self::$rules)){
            return true;
        }
        return false;
    }

    /**
     * 用户是不是渠道
     * @return bool
     */
    public static function is_ditch_user(){
        $ROOT_GROUP_ID =  Config::get('DITCH_GROUP_ID');

        //超级管理员权限直接通过
        if(in_array($ROOT_GROUP_ID,self::$group_ids)){
            return true;
        }
        return false;
    }

    public function del($uid,$gid){
        $id = $this->delete(array("uid"=>$uid,'group_id'=>$gid));
        return $id;
    }
}