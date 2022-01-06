<?php
namespace App\Admin\Controller;

use App\Admin\Model\Project;
use App\Common\Wxsdk\Wxsdk;
use SsdPHP\Cache\Cache;
use SsdPHP\Http\Response;
use SsdPHP\Page\Factory as Page;
use SsdPHP\Http\Input;

class Cproject extends Common {



    /**
     * 业务list
     */
    public function c_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');

        $model = new Project();
        $list = $model->_getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"sort desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $this->assign(array(
            'list'=>$list->items,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
        ))->base();
    }

    /**
     * 修改业务
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
     * 添加业务
     */
    public function c_add(){
        $Project = new Project();
        if(Input::isPost()){

            $data['code']=Input::request('code');
            $data['name']=Input::request('name');
            $data['state']=Input::request('state',0,'intval');
            $data['sort']=Input::request('sort',0,'intval');
            $data['wx_gzh_name']=Input::request('wx_gzh_name');
            $data['appid']=Input::request('appid');
            $data['secret']=Input::request('secret');
            $data['gzh_menu']=Input::request('gzh_menu');
            $data['create_time']=time();
            if (empty($data['code'])){
                Response::apiJsonResult(array(),0,'请填写项目编码');
            }
            if (empty($data['name'])){
                Response::apiJsonResult(array(),0,'请填写项目名称');
            }
            $ret = $Project->_add($data);
            if($ret>0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }

        $this->assign(array(
            '_GET'=>$_GET,
            'status'=>$Project->state,
        ))->base();
    }

    //通过项目菜单，设置公众号菜单
    public function c_gzh_menu_create(){
        $id = Input::request('appid');
        $Project = new Project();
        $projectInfo = $Project->_findone(['appid'=>$id],array("*"));

        if (empty($projectInfo)){
            Response::apiJsonResult(array(),0,'项目不存在');
        }

        $menu = json_decode($projectInfo['gzh_menu'],true);
        if (empty($menu['menu']) || empty($menu['menu']['button'])){
            Response::apiJsonResult(array(),0,'项目还没有创建菜单，请先修改项目');
        }
        $m = json_encode($menu['menu'],JSON_UNESCAPED_UNICODE);
        //查询公众号token for Cache
        $ret = Wxsdk::gzh_set_menu(array(
            'appid'=>$projectInfo['appid'],
            'secret'=>$projectInfo['secret'],
        ),$m);

        if ($ret){
            $d = json_decode($ret,true);
            if ($d==false){
                Response::apiJsonResult([],0,'设置失败');
            }
            if ($d['errcode'] == 0){
                Response::apiJsonResult(array(),1,"设置成功");
            }
            Response::apiJsonResult(array(),0,$d["errmsg"]);
        }
        Response::apiJsonResult([],0,'设置失败');


    }


}