<?php
namespace App\Admin\Controller;


use App\Admin\Model\Adv;
use App\Admin\Model\Ditch;
use App\Admin\Model\Ditch_Log;
use App\Admin\Model\Project;
use App\Admin\Model\Software;
use App\Admin\Model\Software_Version;
use App\Admin\Model\Upfile_Log;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Page\Factory as Page;
use App\Admin\Model\Download_click_log;

class Csoftware extends Common  {

    /**
     * 安卓渠道管理
     */
    public function c_ditch(){
        $Ditch = new Ditch();
        $Software = new Software();
        $slist = $Software->getAll();
        $_GET['rj']    = Input::get('rj',$slist[0]['appid']);
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');

        //1安卓，2-苹果，3小程序
        $_GET['os']     = Input::get('os',1,'intval');
        $list = $Ditch->getList(array('rj'=>$_GET['rj'],'os'=>$_GET['os']),$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);


        $Project = new Project();
        $this->assign(array(
            'list'=>$list,
            'slist'=>$slist,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
            'in_v'=>substr(md5(microtime(true)), 0, 11),
            'status'=>$Ditch->status,
            'oslist'=>(new Software())->os,
            'is_shangjia'=>$Ditch->is_shangjia,
            'checkup_in_v'=>$Ditch->checkup_in_v
        ))->base();
    }
    /**
     * 安卓渠道管理
     */
    public function c_app(){
        $Software = new Software();
        $list = $Software->getList(array(),1,200);

        $Page = new Page($list->totalSize,200);

        $this->assign(array(
            'list'=>$list,
            'page'=>$Page->show(),
            'status'=>$Software->status,
            'reg_type'=>$Software->reg_type,
        ))->base();
    }

    public function c_ditch_list(){

        if(!Input::isAJAX()){
            $Ditch = new Softwaretemplate();
            $data = $Ditch->getAll(array('tui_uid'=>0));
            Response::apiJsonResult($data,1);
        }
    }


    public function c_key(){
        $str = substr(md5(microtime(true)), 0, 12);

        Response::apiJsonResult([
            'key' => $str
        ],1, '内部版本生成成功');
    }

    /**
     * 添加
     */
    public function c_ditch_add(){
        $Ditch = new Ditch();
        $_GET['rj']    = Input::get('rj');
        if(Input::isAJAX()){
            $_POST = Input::post();
            $_POST['did'] = substr(md5(microtime(true)), 0, 7);
            $id = $Ditch->_add($_POST);
            if($id>0){
                Response::apiJsonResult(array('id'=>$id),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }
        $this->assign(array(
            '_GET'=>$_GET,
            'status'=>$Ditch->status,
            'is_shangjia'=>$Ditch->is_shangjia,
            'checkup_in_v'=>$Ditch->checkup_in_v,
            'haoping_status'=>$Ditch->haoping_status,
        ))->base();
    }

    /**
     * 添加
     */
    public function c_app_add(){
        $Software = new Software();
        if(Input::isAJAX()){
            $_POST = Input::post();
            $_POST['appid'] = substr(md5(microtime(true)), 0, 7);
            if(!empty($_POST['reg_type'])){
                $_POST['reg_type'] = implode(",",$_POST['reg_type']);
            }
            //define("DEBUG",1);
            $id = $Software->_add($_POST);
            if($id>0){
                Response::apiJsonResult(array('id'=>$id),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }
        $this->assign(array(
            'status'=>$Software->status,
            'push_status'=>$Software->push_status,
            'reg_type'=>$Software->reg_type,
        ))->base();
    }
/**
     * 添加
     */
    public function c_app_edit(){
        $Software = new Software();
        if(Input::isAJAX()){
            $_POST = Input::post();
            $_POST['update_time']=time();
            if(!empty($_POST['reg_type'])){
                $_POST['reg_type'] = implode(",",$_POST['reg_type']);
            }
            //define("DEBUG",1);
            $id = $Software->edit($_POST);
            if($id>0){
                Response::apiJsonResult(array('id'=>$id),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
        }
        $info = $Software->findone(array('id'=>$_GET['id']));
        $this->assign(array(
            'status'=>$Software->status,
            'info'=>$info,
            'push_status'=>$Software->push_status,
            'reg_type'=>$Software->reg_type,
        ))->base("software/app_add");
    }

    /**
     * 修改
     */
    public function c_ditch_edit(){
        $Ditch = new Ditch();

        if(Input::isAJAX()){
            $_POST = Input::post();
            $_POST['update_time']=time();
            $s = $Ditch->edit($_POST);
            if($s>0){
                if(!empty($_POST['did'])){
                    $Ditch_Log=new Ditch_Log();
                    $Ditch_Log->_add($_POST['did'],UID);
                }
                Response::apiJsonResult(array(
                    "d"=>$_POST,
                    "update_time"=>date("Y-m-d H:i",$_POST['update_time'])
                ),1,'修改成功');
            }
            Response::apiJsonResult([],0,'修改失败');
        }
        $_GET['id']=Input::get('id',0,'intval');
        $_GET['rj']=Input::get('rj');
        $_GET['os']=Input::get('os',1,'intval');
        $info = $Ditch->findone(array('id'=>$_GET['id']));



        $this->assign(array(
            'status'=>$Ditch->status,
            'is_shangjia'=>$Ditch->is_shangjia,
            '_GET'=>$_GET,
            'info'=>$info,
            'haoping_status'=>$Ditch->haoping_status,
            'haoping_failed'=>$Ditch->haoping_failed,
            'checkup_in_v'=>$Ditch->checkup_in_v,
        ))->base();
    }

    /**
     * 安卓渠道管理
     */
    public function c_ditch_log(){
        $Ditch = new Ditch_Log();
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');

        $list = $Ditch->getList(array('ditch_id'=>$_GET['ditch_id']),$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'page'=>$Page->show()
        ))->base();
    }


    /**
     * 软件版本管理
     */
    public function c_version(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['is_shangjia']    = Input::get('is_shangjia',0,'intval');


        $model = new Software_Version();
        $Software = new Software();
        $Ditch = new Ditch();
        $slist = $Software->getAll();
        $_GET['rj']    = Input::get('rj',$slist[0]['appid']);
        $cond = array(
            'rj'=>$_GET['rj']
        );

        $list = $model->getList($cond,$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);
        //print_r($list);
        $this->assign(array(
            'list'=>$list,
            'slist'=>$slist,
            '_GET'=>$_GET,
            'is_shangjia'=>$Ditch->is_shangjia,
            'os'=>$model->os,
            'page'=>$Page->show()
        ))->base();
    }
    /**
     * 添加
     */
    public function c_version_add(){
        $Software_Version = new Software_Version();
        $_GET['rj']    = Input::get('rj',"");

        if(Input::isPost()){

            $_POST=Input::post();
            if(isset($_POST['id'])){
                unset($_POST['id']);
            }
            $ret = $Software_Version->_add($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"添加成功");
            }
            Response::apiJsonResult(array(),0,"添加失败");
            return ;
        }
        $Software = new Software();
        $sinfo = $Software->findone(array('appid'=>$_GET['rj']));

        $this->assign(array(
            'status'=>$Software_Version->status,
            '_GET'=>$_GET,
            'sinfo'=>$sinfo,
            'rj'=>$_GET['rj'],
            'os'=>$Software_Version->os,
        ))->base('software/version_edit');
    }

    /**
     * 修改
     */
    public function c_version_edit(){
        $Software_Version = new Software_Version();
        $_GET['id']    = Input::get('id',"0");

        if(Input::isPost()){

            $_POST=Input::post();
            $ret = $Software_Version->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),0,"修改失败");
            return ;
        }
        $Software = new Software();
        $info = $Software_Version->findone(array('id'=>$_GET['id']));
        $sinfo = $Software->findone(array('appid'=>$info['rj']));

        $this->assign(array(
            'status'=>$Software_Version->status,
            '_GET'=>$_GET,
            'sinfo'=>$sinfo,
            'info'=>$info,
            'rj'=>$info['rj'],
            'os'=>$Software_Version->os,
        ))->base();
    }

    /**
     * 下载页点击统计
     * xu
     */
    public function c_d_click_log(){
        $_GET = Input::get();
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['type']    = Input::get('type',"");
        $cond=array();
        if($_GET['type']>0){
            $cond['type']=$_GET['type'];
        }

        if(!empty($_GET['time_start']) && !empty($_GET['time_end'])){
            $start=str_replace('-','',$_GET['time_start']);
            $end=str_replace('-','',$_GET['time_end']);
            $cond[]="create_time>=$start and create_time<=$end";
        }

        $Download_click_log=new Download_click_log();
        $list = $Download_click_log->getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"id desc");

        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'type'=>$Download_click_log->type,
            'list'=>$list,
            '_GET'=>$_GET,
            'page'=>$Page->show()
        ))->base();
    }
}