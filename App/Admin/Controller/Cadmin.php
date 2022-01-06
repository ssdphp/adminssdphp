<?php
namespace App\Admin\Controller;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Auth_Group;
use App\Admin\Model\Admin_Auth_Group_Access;
use App\Admin\Model\Admin_Menu;
use App\Admin\Model\Project;
use App\Admin\Model\Softwaretemplate;
use App\Common\Tool\Functions;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Page\Factory as Page;
use SsdPHP\Session\Session;
use SsdPHP\SsdPHP;

class Cadmin extends Common {

    public function c_admin_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = 20;
        $Admin = new Admin();
        $list = $Admin->_getList(['isdel'=>0], $_GET['page'],$_GET['pagesize']);
        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $Admin_Auth_Group = new Admin_Auth_Group();

        foreach ($list->items as $k=>&$v){
            $Project = new Project();

            $xm_title=$Project->_findone(array('id'=>$v['pid']),array('name'));
            $v['pid']=empty($xm_title['name'])?'未设置':$xm_title['name'];
            $access_group_ids=explode(",", $v['access_group_ids']);
            $data=array();
            foreach ($access_group_ids as $ke=>$va){
                $data[]=$Admin_Auth_Group->_findone(array('id'=>$va),array('title'));
            }
            $v['access_group_ids']=$data;
        }

        $this->assign(array(
            'list'=>!empty($list->items)?$list->items:array(),
            'sex'=>$Admin->getSex(),
            'status'=>$Admin->getStatus(),
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 后台管理用户添加
     */
    public function c_admin_add(){
        $Admin = new Admin();
        if(Input::isPost()){
            $data=array();
            $data['username']=Input::request("username");
            $data['nickname']=Input::request("nickname");
            $data['link_phone']=Input::request("link_phone");
            $pwd=Input::request("password");
            $data['password'] = md5($pwd.$data['username']);
            $data['pid']=Input::request("pid");
            $data['pwd_str']=Functions::opensslEncrypt($pwd);
            $data['create_time']=time();
            if (empty($data['username'])){
                Response::apiJsonResult(array(),2,"请填写登录账号");
            }
            if (empty($data['nickname'])){
                Response::apiJsonResult(array(),2,"请填写用户昵称");
            }
            if (empty($data['link_phone'])){
                Response::apiJsonResult(array(),2,"请填写联系手机号");
            }
            if (empty($pwd)){
                Response::apiJsonResult(array(),2,"请填写密码");
            }
            if (empty($data['pid'])){
                Response::apiJsonResult(array(),2,"请选择项目");
            }
            $gids = Input::request('group_id/a',"",'intval');
            if(!empty($gids)){
                $data['access_group_ids'] = implode(",",$gids);
            }else{
                if (empty($gids)){
                    Response::apiJsonResult(array(),2,"请选择拥有的角色权限");
                }
            }
            $info = $Admin->_findone(['username'=>$_POST["username"],'isdel'=>0],["*"]);
            if (!empty($info)){
                Response::apiJsonResult(array(),2,"添加失败,账号已存在");
            }
            $id = $Admin->_add($data);
            if($id){
                $Admin_Auth_Group_Access = new Admin_Auth_Group_Access();
                if(!empty($gids)){
                    foreach ($gids as $gid){
                        $ret = $Admin_Auth_Group_Access->_add(array(
                            'group_id'=>$gid,
                            'uid'=>$id,
                        ));
                    }
                }

                Response::apiJsonResult(array(),1,"添加成功");
            }
            Response::apiJsonResult(array(),2,"添加失败");
        }
        $Admin_Auth_Group = new Admin_Auth_Group();
        $Project = new Project();
        $a = $Admin_Auth_Group->_getList(["status"=>0],1,100);

        $p = $Project->_getList(["state"=>1],1,100);

        $this->assign(array(
            'access'=>$a->items,
            'project'=>$p->items,
            'status'=>$Admin->getStatus(),
        ))->base();
    }
    /**
     * 后台管理用户修改
     */
    public function c_admin_edit(){
        $Admin = new Admin();
        if(Input::isPost()){
            $data=array();
            $data['uid']=Input::request("uid");
            $data['username']=Input::request("username");
            $data['nickname']=Input::request("nickname");
            $data['link_phone']=Input::request("link_phone");
            $pwd=Input::request("password");
            if (!empty($pwd)) {
                $data['password']=md5($pwd.$data['username']);
                $data['pwd_str']=Functions::opensslEncrypt($pwd);
            }
            $data['pid']=Input::request("pid");
            $data['status']=Input::request("status");

            $data['update_time']=time();

            if (empty($data['username'])){
                Response::apiJsonResult(array(),2,"请填写登录账号");
            }
            if (empty($data['nickname'])){
                Response::apiJsonResult(array(),2,"请填写用户昵称");
            }
            if (empty($data['link_phone'])){
                Response::apiJsonResult(array(),2,"请填写联系手机号");
            }
            if (empty($data['pid'])){
                Response::apiJsonResult(array(),2,"请选择项目");
            }

            $gids = Input::request('group_id/a',"",'intval');
            if(!empty($gids)){
                $data['access_group_ids'] = implode(",",$gids);
                $Admin_Auth_Group_Access = new Admin_Auth_Group_Access();
                $s = $Admin_Auth_Group_Access->_del(['uid'=>$data['uid']]);
                foreach ($gids as $gid){
                    $ret = $Admin_Auth_Group_Access->_add(array(
                        'group_id'=>$gid,
                        'uid'=>$data['uid'],
                    ));
                }
            }else{
                Response::apiJsonResult(array(),0,"请选择一个角色权限");
            }

            $ret = $Admin->_updateInfo(['uid'=> $data['uid']],$data);
            if($ret){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),2,"修改失败");
        }

        $uid = Input::request("uid",0,'intval');

        $info = $Admin->getInfoByUid($uid);

        $Admin_Auth_Group = new Admin_Auth_Group();
        $Project = new Project();
        $a = $Admin_Auth_Group->_getList(["status"=>0],1,100);
        $p = $Project->_getList(["state"=>1],1,100);
        $this->assign(array(
            'info'=>$info,
            'status'=>$Admin->getStatus(),
            'access'=>$a->items,
            'project'=>$p->items,
        ))->base("admin/admin_edit");
    }
    /**
     * 删除后台用户
     */
    public function c_admin_del(){
        $Admin = new Admin();
        if(Input::isPost()){
            $data = array();
            $data['uid']=Input::request("uid",0,'intval');
            $data['isdel']=1;
            $data['update_time']=time();
            if (empty($data['uid']) || $data['uid']<1) {
                Response::apiJsonResult(array(),0,"删除失败");
            }
            $ret = $Admin->_updateInfo(['uid'=>$data['uid']],$data);
            if($ret){
                Response::apiJsonResult(array(),1,"删除成功");
            }
            Response::apiJsonResult(array(),2,"删除失败");
        }
    }

    public function c_menu(){
        $Admin_Menu = new Admin_Menu();
        $list = $Admin_Menu->getTreeAll();
        $del_list = $Admin_Menu->getAll(array('is_del'=>1));

        $this->assign(array(
            '_GET'=>$_GET,
            'list'=>$list,
            'del_list'=>$del_list,
        ))->base();
    }

    //菜单排序
    public function c_menu_order(){
        $data = Input::request('data');
        if(!empty($data)){
            $data = json_decode($data,true);
            if(!empty($data)){
                $Admin_Menu = new Admin_Menu();
                foreach ($data as $k=>$v){
                    $s=$Admin_Menu->edit(['id'=>$v['id'],'sort'=>$k,'is_del'=>0,'sort_pid'=>0]);
                    if(!empty($v['children'])){
                        foreach ($v['children'] as $_k=>$_v){
                            $s=$Admin_Menu->edit(['id'=>$_v['id'],'sort'=>$_k,'is_del'=>0,'sort_pid'=>$v['id']]);

                            if(!empty($_v['children'])){
                                foreach ($_v['children'] as $__k=>$__v){
                                    $s=$Admin_Menu->edit(['id'=>$__v['id'],'sort'=>$__k,'is_del'=>0,'sort_pid'=>$_v['id']]);
                                }
                            }
                        }
                    }
                }
                Response::apiJsonResult([],1,'排序成功');
            }
        }
        Response::apiJsonResult([],0,'排序失败');
    }
    //
    public function c_menu_info(){
        $Admin_Menu = new Admin_Menu();
        $id = Input::request('id');
        $ret = $Admin_Menu->getMenuById($id);
        Response::apiJsonResult($ret,1);
    }
    /**
     * 后台菜单添加
     */
    public function c_menu_add(){

        if(Input::isPost()){
            $Admin_Menu = new Admin_Menu();
            $ret = $Admin_Menu->_add($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"添加成功");
            }
            Response::apiJsonResult(array(),0,"添加失败");
        }else{
            $_GET['pid'] = Input::get('pid',0,'intval');
            $this->assign(array(
                '_GET'=>$_GET,
            ))->base();
        }

    }
    /**
     * 后台菜单添加
     */
    public function c_menu_edit(){

        $Admin_Menu = new Admin_Menu();
        if(Input::isPost()){
            $_POST['update_time']=time();
            $ret = $Admin_Menu->edit($_POST);
            if(Input::get('active',"") == "del"){
                if($ret>0){
                    Response::apiJsonResult(array(),1,"删除成功。");
                }
                Response::apiJsonResult(array(),0,'删除失败');
            }
            if($ret>0){
                Response::apiJsonResult($_POST,1,"修改成功");
            }
            Response::apiJsonResult(array(),0,'状态修改失败');
        }else{
            $_GET['id'] = Input::get('id',0,'intval');
            $_GET['pid'] = Input::get('pid',0,'intval');
            $info = $Admin_Menu->getMenuById($_GET['id']);
            $this->assign(array(
                '_GET'=>$_GET,
                'info'=>$info,
            ))->base('admin/menu_add');
        }

    }
    /**
     * 退出登录
     */
    public function c_loginout(){
        Session::destroy();
        if(Input::isAJAX()){
            Response::apiJsonResult([],1);
        }else{
            header("location:/public/login.html");
        }

    }
}