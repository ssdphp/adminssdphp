<?php
namespace App\Admin\Controller;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Menu;
use App\Admin\Model\Agent;
use App\Admin\Model\Agent_Code;
use App\Admin\Model\Softwaretemplate;
use App\Common\Tool\Functions;
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\Pulgins\Http\Response;
use SsdPHP\Pulgins\Session\Factory as Session;
use SsdPHP\Pulgins\Page\Factory as Page;
class Cagent extends Common {

    public function c_user_list(){
        $Admin = new Agent();
        $list = $Admin->getList();
        $this->assign(array(
            'list'=>!empty($list->items)?$list->items:array()
        ))->base();
    }

    /**
     * 后台管理用户添加
     */
    public function c_user_add(){

        if(Input::isPost()){
            $Admin = new Agent();
            $_POST=Input::post();
            $ret = $Admin->_add($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1002);
            }
            Response::apiJsonResult(array(),$ret['code']);
        }
        $this->base();
    }
    /**
     * 后台管理用户修改
     */
    public function c_user_edit(){
        $Admin = new Agent();
        if(Input::isPost()){

            $ret = $Admin->edit($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),$ret['code'],"修改失败");
        }
        $Ditch = new Softwaretemplate();

        $uid = Input::request("uid",0,'intval');

        $info = $Admin->getInfoByUid($uid);
        $this->assign(array(
            'info'=>$info,
        ))->base("agent/user_add");
    }
    //代理商激活码
    public function c_code(){
        $Admin_Menu = new Agent_Code();
        $_GET['uid'] = Input::get('uid',0,'intval');
        $_GET['use_status'] = Input::get('use_status',1,'intval');
        $_GET['page'] = Input::request('page',1,'intval');
        $_GET['pagesize'] = 20;
        $cond=array();
        $cond['uid']=$_GET['uid'];
        $cond['use_status']=$_GET['use_status'];

        $list = $Admin_Menu->getList($cond,$_GET['page'],$_GET['pagesize'] );

        $Page = new Page($list->totalSize,$_GET['pagesize'] );
        $this->assign(array(
            '_GET'=>$_GET,
            'list'=>$list,
            //1-未激活，2-已激活 , 3-已分配
            'use_status_list'=>array(
                1=>'未激活',
                2=>'已激活',
                3=>'已分配',
            ),
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 激活码
     */
    public function c_code_add(){


        if(Input::isPost()){
            $Admin_Menu = new Agent_Code();
            $_POST['create_time']=time();
            if(isset($_POST['id'])){
                unset($_POST['id']);
            }
            $ret = $Admin_Menu->add($_POST);
            if($ret!==false){
                Response::apiJsonResult(array(),1,"添加成功");
            }
            Response::apiJsonResult(array(),3,'添加失败');
        }else{
            $code = Functions::uniqidReal();
            $_GET['pid'] = Input::get('pid',0,'intval');
            $this->assign(array(
                '_GET'=>$_GET,
                'code'=>strtoupper($code),
            ))->base();
        }

    }


    /**
     * 后台菜单添加
     */
    public function c_code_edit(){

        $Admin_Menu = new Agent_Code();
        if(Input::isPost()){
            $_POST['update_time']=time();
            $ret = $Admin_Menu->edit($_POST);
            if($ret!==false){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),0,'修改失败');
        }else{
            $_GET['id'] = Input::get('id',0,'intval');
            $info = $Admin_Menu->findOne($_GET);

            $this->assign(array(
                '_GET'=>$_GET,
                'info'=>$info,
            ))->base('agent/code_add');
        }

    }
    /**
     * 退出登录
     */
    public function c_loginout(){
        Session::destroy();
        Response::apiJsonResult([],1);
    }
}