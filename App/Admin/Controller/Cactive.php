<?php
namespace App\Admin\Controller;

use App\Admin\Model\Activity;
use App\Admin\Model\Project;
use App\Common\Wxsdk\Wxsdk;
use Qiniu\Auth;
use SsdPHP\Cache\Cache;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Http\Response;
use SsdPHP\Page\Factory as Page;
use SsdPHP\Http\Input;

class Cactive extends Common {



    /**
     * 业务list
     */
    public function c_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',10,'intval');

        $Activity = new Activity();
        $Project = new Project();
        $list = $Activity->_getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"top desc,start_time desc");

        if (!empty($list->items)){
            foreach($list->items as &$v){
                if(!empty($v['project_id'])){
                    $info = $Project->_findone(array('id'=>$v['project_id']),['id','name']);
                    $v['project_info'] = $info;
                }
            }
        }
        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $this->assign(array(
            'list'=>$list->items,
            '_GET'=>$_GET,
            'status'=>$Activity->status,
            'state'=>$Activity->state,
            'top'=>$Activity->top,
            'online'=>$Activity->online,
            'page'=>$Page->show(),
        ))->base();
    }

    /**
     * 修改活动
     */
    public function c_edit(){
        $id = Input::get('id');
        $Project = new Project();
        $info = $Project->_findOne(array('id'=>$id));
        if(Input::isPost()){
            $data=array();

            $data['id']=Input::request('id',0,"intval");
            $data['code']=Input::request('code');
            $data['name']=Input::request('name');
            $data['state']=Input::request('state',0,'intval');
            $data['sort']=Input::request('sort',0,'intval');
            $data['wx_gzh_name']=Input::request('wx_gzh_name');
            $data['appid']=Input::request('appid');
            $data['secret']=Input::request('secret');
            $data['gzh_menu']=Input::request('gzh_menu');
            $data['update_time']=time();
            if (empty($data['code'])){
                Response::apiJsonResult(array(),0,'请填写项目编码');
            }
            if (empty($data['name'])){
                Response::apiJsonResult(array(),0,'请填写项目名称');
            }
            $ret = $Project->_updateInfo(['id'=>$data['id']],$data);
            if($ret){
                Response::apiJsonResult(array(),1,'项目保存成功');
            }
            Response::apiJsonResult(array(),0,'项目保存失败');
        }
        $this->assign(array(
            '_GET'=>$_GET,
            'info'=>$info,
            'status'=>$Project->state,
        ))->base();
    }
    /**
     * 添加活动
     */
    public function c_add(){
        $Activity = new Activity();
        if(Input::isPost()){

            $data['project_id']=Input::request('project_id',0,'intval');
            $data['name']=Input::request('name');
            $data['banner']=Input::request('banner');
            $data['online']=Input::request('online',0,'intval');
            $data['need_integral']=Input::request('need_integral',0,'intval');
            $data['state']=Input::request('state',0,'intval');
            $data['style_id']=Input::request('style_id',0,'intval');
            $data['top']=Input::request('top',0,'intval');
            $content=Input::request('content');

            $show_time=Input::request('show_time');
            $run_time=Input::request('run_time');

            $showTimeAry = explode(" - ",$show_time);
            if (empty($showTimeAry[0])){
                Response::apiJsonResult(array(),0,'请选择活动展示时间');
            }else{
                $show_time_val = strtotime($showTimeAry[0]);
                if ($show_time_val>0){
                    $data['show_start_time'] = $show_time_val;
                }
                $show_time_val = strtotime($showTimeAry[1]);
                if ($show_time_val>0){
                    $data['show_end_time'] = $show_time_val;
                }
            }

            $runTimeAry = explode(" - ",$run_time);
            if (empty($runTimeAry[0])){
                Response::apiJsonResult(array(),0,'请选择活动进行时间');
            }else{
                $show_time_val = strtotime($runTimeAry[0]);
                if ($show_time_val>0){
                    $data['start_time'] = $show_time_val;
                }
                $show_time_val = strtotime($runTimeAry[1]);
                if ($show_time_val>0){
                    $data['end_time'] = $show_time_val;
                }
            }

            if(empty($content)){
                if ($data['online']==0){
                    Response::apiJsonResult(array(),0,'请填写活动的领奖须知');
                }else{
                    Response::apiJsonResult(array(),0,'请填写活动介绍');
                }
            }
            if ($data['online']==0){
                $data['tips'] = $content;
            }else{
                $data['introduce'] = $content;
            }

            $data['create_time']=time();
            $data['update_time']=time();

            if ($data['project_id'] < 1){
                Response::apiJsonResult(array(),0,'请选择项目');
            }
            if ($data['need_integral'] < 1){
                if ($data['online']==0){
                    Response::apiJsonResult(array(),0,'请填写参加该活动所需积分数量');
                }else{
                    Response::apiJsonResult(array(),0,'请填写参加该活动所需幸运值数量');
                }

            }
            if (empty($data['name'])){
                Response::apiJsonResult(array(),0,'请填写活动名称');
            }
            if (empty($data['banner'])){
                Response::apiJsonResult(array(),0,'请上传活动封面图');
            }
            //define("DEBUG",1);
            $id = $Activity->_add($data);
            if($id>0){
                Response::apiJsonResult(array('id'=>$id),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }

        $Project = new Project();
        $p = $Project->_getList(["state"=>1],1,100);


        $accessKey = Config::getField("upload","qiniu")['ak'];
        $secretKey = Config::getField("upload","qiniu")['sk'];
        $auth = new Auth($accessKey, $secretKey);
        // 空间名  http://developer.qiniu.com/docs/v6/api/overview/concepts.html#bucket
        $bucket = Config::getField("upload","qiniu")['bucket'];
        // 生成上传Token
        $token = $auth->uploadToken($bucket,null,3600*3,null);
        $qiniucfg = array(
            "uptoken"=>$token,
            "domain"=>Config::getField("upload","qiniu")['domain'],
        );

        $this->assign(array(
            '_GET'=>$_GET,
            'project'=>$p->items,
            'status'=>$Activity->status,
            'state'=>$Activity->state,
            'top'=>$Activity->top,
            'qiniucfg'=>$qiniucfg,
            'online'=>$Activity->online,
        ))->base();
    }

    //添加奖品
    public function c_prize_add(){
        $id = Input::request('id',0,'intval');
        $Activity = new Activity();
        $ainfo = $Activity->_findone(['id'=>$id],["*"]);
        $this->assign(array(
            'status'=>array(
                0=>'不限',
                1=>'中奖后不可中奖'
            ),
            'prize_rule'=>array(
                0=>'概率中奖',
                1=>'间隔人数中奖'
            ),
            'ainfo'=>$ainfo
        ))->base();
    }

    //活动皮肤管理
    public function c_style(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',10,'intval');

        $Activity = new Activity();
        $Project = new Project();
        $list = $Activity->_getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"top desc,start_time desc");




        if (!empty($list->items)){
            foreach($list->items as &$v){
                if(!empty($v['project_id'])){
                    $info = $Project->_findone(array('id'=>$v['project_id']),['id','name']);
                    $v['project_info'] = $info;
                }
            }
        }
        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $this->assign(array(
            'list'=>$list->items
        ))->base();
    }
}