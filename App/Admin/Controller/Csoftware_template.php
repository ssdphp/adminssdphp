<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/12
 * Time: 16:20
 */

namespace App\Admin\Controller;

use App\Admin\Model\Account;
use App\Admin\Model\Feedback;
use App\Admin\Model\Report;
use App\Admin\Model\Softwaretemplate;
use App\Admin\Model\Software_template;
use App\Admin\Model\User_template;
use App\Admin\Model\Content;
use App\Admin\Model\Keyword;
use App\Admin\Model\Task_Step;
use App\Admin\Model\Template_type;
use function Psy\debug;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Session\Factory as Session;
use SsdPHP\Page\Factory as Page;

class Csoftware_template extends Common
{
    //获取模板列表
    public function c_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');
        $_GET['id']       = Input::get('id',"");

        $software_template = new Software_template();
        $Template_type=new Template_type();

        $cond=array();
        if($_GET['id']>0){
            $cond['id']=$_GET['id'];
        }

        $list = $software_template->getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"id desc");

        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $t_type=$Template_type->getAll(array(),array('*'));

        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$software_template->status,
            'is_top'=>$software_template->is_top,
            't_type'=>$t_type,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 添加模板
     */
    public function c_add(){
        $software_template = new Software_template();
        if(Input::isPost()){

            $_POST=Input::post();
            $t_type = Input::request('t_type/a',"",'intval');
            if(!empty($t_type)){
                $t_type = implode(",",$t_type);
                $_POST['t_type']=$t_type;
            }
            $ret = $software_template->_add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }

        $Template_type=new Template_type();
        $t_type=$Template_type->getAll(array(),array('*'));

        $this->assign(array(
            '_GET'=>$_GET,
            't_type'=>$t_type,
            'status'=>$software_template->status,
            'is_top'=>$software_template->is_top,
            's_type'=>$software_template->s_type,
        ))->base();
    }

    /**
     * 修改模板
     */
    public function c_edit(){
        $Software_template = new Software_template();
        if(Input::isPost()){
            $_POST=Input::post();
            $t_type = Input::request('t_type/a',"",'intval');
            if(!empty($t_type)){
                $t_type = implode(",",$t_type);
                $_POST['t_type']=$t_type;
            }
            $_POST['update_time']=time();
            $ret = $Software_template->updateInfo(array('id'=>$_POST['id']),$_POST);
            if($ret!=false){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
        }
        $id = Input::request('id',0,'intval');
        $info = $Software_template->findone(['id'=>$id]);
        $info['t_type']=explode(',',$info['t_type']);
        $Template_type=new Template_type();
        $t_type=$Template_type->getAll(array(),array('*'));
        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            't_type'=>$t_type,
            'status'=>$Software_template->status,
            'is_top'=>$Software_template->is_top,
            's_type'=>$Software_template->s_type,
        ))->base('software_template/add');
    }

    //获取文字列表
    public function c_content_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');

        $content = new Content();

        $list = $content->getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"sort desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$content->status,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 添加文字模板
     */
    public function c_content_add(){
        $content = new Content();
        if(Input::isPost()){

            $_POST=Input::post();
            //define("DEBUG",1);
            $ret = $content->add($_POST);
            //exit;
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
            return ;
        }

        $Software_template = new Software_template();
        $User_template=new User_template();
        $flist=$Software_template->getAll();
        $ulist=$User_template->getAll();

        $this->assign(array(
            '_GET'=>$_GET,
            'flist'=>$flist,
            'ulist'=>$ulist,
            'type'=>$content->type,
            'status'=>$content->status,
        ))->base();
    }

    /**
     * 修改模板
     */
    public function c_content_edit(){
        $content = new Content();
        if(Input::isPost()){
            $_POST=Input::post();

            $ret = $content->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
            return ;
        }
        $id = Input::request('id',0,'intval');
        $info = $content->findOne(['id'=>$id]);

        $Software_template = new Software_template();
        $User_template=new User_template();
        $flist=$Software_template->getAll();
        $ulist=$User_template->getAll();

        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            'flist'=>$flist,
            'ulist'=>$ulist,
            'type'=>$content->type,
            'status'=>$content->status,
        ))->base('software_template/content_add');
    }

    //获取关键字列表
    public function c_keyword_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',10,'intval');

        $keyword = new Keyword();

        $list = $keyword->getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"id desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$keyword->status,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 添加关键词
     */
    public function c_keyword_add(){
        $keyword = new Keyword();
        if(Input::isPost()){

            $_POST=Input::post();
            $ret = $keyword->_add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }

        $this->assign(array(
            '_GET'=>$_GET,
            'type'=>$keyword->type,
            'status'=>$keyword->status,
        ))->base();
    }

    /**
     * 修改关键词
     */
    public function c_keyword_edit(){
        $keyword = new Keyword();
        if(Input::isPost()){
            $_POST=Input::post();

            $ret = $keyword->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
        }
        $id = Input::request('id',0,'intval');
        $info = $keyword->findone(['id'=>$id]);

        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            'type'=>$keyword->type,
            'status'=>$keyword->status,
        ))->base('software_template/keyword_add');
    }

    //获取类型列表
    public function c_type_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',10,'intval');

        $Template_type = new Template_type();

        $list = $Template_type->_getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"id desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$Template_type->status,
            'page'=>$Page->show()
        ))->base();
    }


    /**
     * 添加模板类型
     */
    public function c_type_add(){
        $Template_type = new Template_type();
        if(Input::isPost()){

            $_POST=Input::post();
            $_POST['create_time']=time();
            $ret = $Template_type->_add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }

        $this->assign(array(
            '_GET'=>$_GET,
            'status'=>$Template_type->status,
        ))->base();
    }

    /**
     * 修改模板类型
     */
    public function c_type_edit(){
        $Template_type = new Template_type();
        if(Input::isPost()){
            $_POST=Input::post();
            $_POST['update_time']=time();
            $ret = $Template_type->_updateInfo(array('id'=>$_POST['id']),$_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
        }
        $id = Input::request('id',0,'intval');
        $info = $Template_type->_findOne(['id'=>$id]);

        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            'status'=>$Template_type->status,
        ))->base('software_template/type_add');
    }

    //获取留言列表
    public function c_feedback_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',10,'intval');

        $Feedback = new Feedback();

        $list = $Feedback->getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"id desc");

        foreach ($list->items as $k=>&$v){
            $Account=new Account();
            $name=$Account->findone(array('id'=>$v['uid']),array('name'));
            $v['uid']=$name['name']??'--';
        }
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'page'=>$Page->show()
        ))->base();
    }

    //获取举报列表
    public function c_report_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',10,'intval');

        $Report = new Report();

        $list = $Report->getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"id desc");

        foreach ($list->items as $k=>&$v){
            $Account=new Account();
            $name=$Account->findone(array('id'=>$v['uid']),array('name'));
            $v['uid']=$name['name']??'非登录举报';
            $software_template = new Software_template();
            $img=$software_template->findone(array('id'=>$v['mid']),array('pic'));
            $v['mid']=$img['pic'];
        }
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'page'=>$Page->show()
        ))->base();
    }

    //用户字幕生成记录
    public function c_user_template_log(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',10,'intval');
        $_GET['uid']    = Input::get('uid',"");
        $_GET['name_id']    = Input::get('name_id',"");
        $_GET['status']    = Input::get('status',"-1",'intval');

        $cond=array();
        if(!empty($_GET['uid'])){
            $cond['uid']=$_GET['uid'];
        }

        if($_GET['status']>-1){
            $cond['status']=$_GET['status'];
        }

        if(!empty($_GET['name_id'])){
            $keyword=$_GET['name_id'];
            $cond[]="name_id LIKE '%$keyword%'";
        }

        $User_template = new User_template();

        $list = $User_template->_getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"id desc");

        foreach ($list->items as $k=>&$v){
            $Account=new Account();
            $name=$Account->_findone(array('id'=>$v['uid']),array('name'));
            $v['name']=$name['name'];
            $software_template = new Software_template();
            $img=$software_template->_findone(array('id'=>$v['mid']),array('m_pic'));
            $v['mid']=$img['m_pic'];
            $v['content']=json_decode($v['content'],true);
        }
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$User_template->status,
            'page'=>$Page->show()
        ))->base();
    }

    //审核状态更新
    public function c_update_status(){
        $User_template=new User_template();
        if(Input::isAJAX()){
            $id=Input::post('id','0','intval');
            $status=Input::post('status','0','intval');
            $r=$User_template->updateInfo(array('id'=>$id),array('status'=>$status,'update_time'=>time()));

            if($r>0){
                Response::apiJsonResult(
                    array(
                        'id'=>$id,
                        'status'=>$status,
                        'title'=>$User_template->status[$status]['title'],
                    ),1,'审核成功'
                );
            }
            Response::apiJsonResult(array(),0,'审核失败');
        }
    }
}