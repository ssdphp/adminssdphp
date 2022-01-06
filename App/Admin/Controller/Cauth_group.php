<?php
namespace App\Admin\Controller;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Auth_Group;
use App\Admin\Model\Admin_Auth_Group_Access;
use App\Admin\Model\Admin_Menu;
use SsdPHP\Core\Config;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Session\Session;
class Cauth_group extends Common {

    public function c_list(){
        $Admin_Auth_Group = new Admin_Auth_Group();
        $list = $Admin_Auth_Group->_getList([],1,200);
        $this->assign(array(
            'status'=>$Admin_Auth_Group->getStatus(),
            'list'=>!empty($list->items)?$list->items:array()
        ))->base();
    }

    /**
     * 添加
     */
    public function c_add(){
        $Admin_Auth_Group = new Admin_Auth_Group();
        if(Input::isPost()){
            $_POST['model']="admin";
            $ret = $Admin_Auth_Group->_add($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"添加成功");
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }

        $this->assign(array(
            'status'=>$Admin_Auth_Group->getStatus(),
        ))->base();
    }
    /**
     * 修改
     */
    public function c_edit(){
        $Admin_Auth_Group = new Admin_Auth_Group();
        if(Input::isPost()){

            $ret = $Admin_Auth_Group->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),0,"修改失败");
        }
        $id = Input::request("id",0,'intval');
        $info = $Admin_Auth_Group->_findone(array('id'=>$id));
        $this->assign(array(
            'info'=>$info,
            'status'=>$Admin_Auth_Group->getStatus(),
        ))->base("auth_group/add");
    }

    /**
     * 权限设置
     */
    public function c_access(){
        $qid = Input::request('qid',0,'intval');
        if($qid<1){
            exit("错误操作!");
        }
        $Admin_Menu = new Admin_Menu();
        $Admin_Auth_Group = new Admin_Auth_Group();
        $accessInfo = $Admin_Auth_Group->_findone(array('id'=>$qid));
        $inRulue = $accessInfo['rules']?explode(",",$accessInfo['rules']):array();
        $menuList = $Admin_Menu->getAll(array('status'=>1,'is_del'=>0));

        $menu2 = $this->getChild($menuList,0,$inRulue);

        $this->assign(array(
            'accessInfo'=>$accessInfo,
            'menu_node'=>$menu2
        ))->base();
    }

    private function getChild($array,$pid=0,$inRulue){
        $data = array();
        foreach ($array as $k=>$v){        //PID符合条件的
            if($v['pid'] == $pid){            //寻找子集
                $child = $this->getChild($array,$v['id'],$inRulue);            //加入数组
                $v['child'] = $child?:array();
                $v['checked']=in_array($v['id'],$inRulue)?'checked':"";
                $data[] = $v;//加入数组中
            }
        }
        return $data;
    }
    /**
     * 保存权限设置结果
     */
    public function c_rule_save(){
        $Admin_Auth_Group = new Admin_Auth_Group();

        if(Input::isPost()){
            $id = Input::request('qid',0,'intval');
            $rules = Input::request('rule/a',"",'intval');
            if($id<1){
                Response::apiJsonResult(array(),1001);
            }
            if(!empty($rules)){
                $rules = implode(",",$rules);
            }
            $data=array(
                'rules'=>$rules,
                'id'=>$id,
            );
            $ret = $Admin_Auth_Group->edit($data);
            if(isset($ret)){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),2,"修改失败");
        }
    }


    /**
     * 成员授权
     */
    public function c_user_access(){

        $qid = Input::request('qid',0,'intval');
        if($qid < 1){
            return ;
        }

        $Admin_Auth_Group = new Admin_Auth_Group();
        $accessInfo = $Admin_Auth_Group->_findone(array('id'=>$qid));

        $Admin_Auth_Group_Access = new Admin_Auth_Group_Access();
        $pre  = Config::getField('mysql','main')[0]['prefix'];
        $list = $Admin_Auth_Group_Access->findAll(array('group_id'=>$qid),array("*"),
            array("{$pre}admin"=>"{$pre}admin_auth_group_access.uid={$pre}admin.uid")
        );

        $this->assign(array(
            'list'=>$list,
            'accessInfo'=>$accessInfo
        ))->base();
    }

    /**
     * 成员授权添加用户
     */
    public function c_user_access_add(){
        $Admin_Auth_Group_Access = new Admin_Auth_Group_Access();

        if(Input::isPost()){
            $id = Input::request('qid',0,'intval');
            $uids = Input::request('uids');
            if($id<1){
                Response::apiJsonResult(array(),1001);
            }
            if(!empty($uids)){
                $uids = explode(",",$uids);
            }
            if(empty($uids)){
                Response::apiJsonResult(array(),1001);
            }
            foreach ($uids as $uid){

                $ret = $Admin_Auth_Group_Access->_add(array(
                    'group_id'=>$id,
                    'uid'=>$uid,
                ));
            }

            Response::apiJsonResult(array(),1,1006);
        }
    }
    /**
     * 解除用户授权
     */
    public function c_user_access_del(){
        $Admin_Auth_Group_Access = new Admin_Auth_Group_Access();

        if(Input::isPost()){
            $id = Input::request('qid',0,'intval');
            $uid = Input::request('uid',0,'intval');
            if($id<1){
                Response::apiJsonResult(array(),1001);
            }
            if(empty($uid)){
                Response::apiJsonResult(array(),1001);
            }
            $ret = $Admin_Auth_Group_Access->del($uid,$id);
            $list = $Admin_Auth_Group_Access->_getList(['uid'=>$uid]);

            if (!empty($list->items)){
                $qids="";
                foreach($list->items as $v){
                    $qids .= $v['group_id'].",";
                }
                $qids = trim($qids,",");

                $Admin = new Admin();

                $s = $Admin->_updateInfo(['uid'=>$uid],['access_group_ids'=>$qids,'update_time'=>time()]);
            }
            if ($ret) {
                Response::apiJsonResult(array(),1,"删除成功");
            }
            Response::apiJsonResult(array(),0,"删除失败");

        }
    }
}