<?php
/**
 * Created by PhpStorm.
 * User: 许
 * Date: 2021-5-26
 * Time: 10:36
 */

namespace App\Admin\Controller;

use App\Admin\Model\Business;
use App\Admin\Model\Industry;
use App\Admin\Model\Project;
use SsdPHP\Http\Response;
use SsdPHP\Page\Factory as Page;
use SsdPHP\Http\Input;

class Cbusiness extends Common{

    /**
     * 商家列表
     */
    public function c_list(){

        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');

        $business=new Business();

        $list=$business->_getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),'id desc');
        $Page = new Page($list->totalSize,$_GET['pagesize']);

        foreach ($list->items as $k=>&$v){
            $Project = new Project();

            $xm_title=$Project->_findone(array('id'=>$v['department_id']),array('name'));
            $v['department_id']=empty($xm_title['name'])?'未设置':$xm_title['name'];
        }

        $this->assign(array(
            'list'=>$list->items,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
        ))->base();
    }

    /**
     * 添加商家
     */
    public function c_add(){
        $business=new Business();
        if(Input::isPost()){

            $data['name']=Input::request('name','');
            $data['department_id']=Input::request('department_id',0,'intval');
            $industry_id=Input::request('industry_id',0,'intval');
            $data['contacts']=Input::request('contacts','');
            $data['state']=Input::request('state',0,'intval');
            $data['phone']=Input::request('phone',0,'intval');
            $data['version']=Input::request('version','');
            $data['created_at']=time();
            if (empty($data['name'])){
                Response::apiJsonResult(array(),0,'请填写商家名称');
            }
            if (empty($data['department_id'])){
                Response::apiJsonResult(array(),0,'请选择项目');
            }
            if(!empty($industry_id)){
                $data['industry_id'] = implode(",",$industry_id);
            }else{
                if (empty($industry_id)){
                    Response::apiJsonResult(array(),0,'请选择业态');
                }
            }
            if (empty($data['contacts'])){
                Response::apiJsonResult(array(),0,'请填写联系人');
            }
            if (empty($data['phone'])){
                Response::apiJsonResult(array(),0,'请填写联系电话');
            }
            $ret = $business->_add($data);

            if($ret>0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
        }
        $Project = new Project();
        $Project_list = $Project->_getList(["state"=>1],1,100);
        $industry=new Industry();
        $industry_list = $industry->_getList(["state"=>1],1,100);

        $this->assign(array(
            '_GET'=>$_GET,
            'project'=>$Project_list->items,
            'industry'=>$industry_list->items,
            'state'=>$business->state,
        ))->base();
    }

    /**
     * 修改商家
     */
    public function c_edit(){
        $business=new Business();
        if(Input::isPost()){
            $data['id']=Input::request("id",0,'intval');
            $data['name']=Input::request('name','');
            $data['department_id']=Input::request('department_id',0,'intval');
            $industry_id=Input::request('industry_id',0,'intval');
            $data['contacts']=Input::request('contacts','');
            $data['state']=Input::request('state',0,'intval');
            $data['phone']=Input::request('phone',0,'intval');
            $data['version']=Input::request('version','');
            $data['updated_at']=time();
            if (empty($data['name'])){
                Response::apiJsonResult(array(),0,'请填写商家名称');
            }
            if (empty($data['department_id'])){
                Response::apiJsonResult(array(),0,'请选择项目');
            }
            //if(!empty($industry_id)){
            //    $data['industry_id'] = implode(",",$industry_id);
            //}else{
            //    if (empty($industry_id)){
            //        Response::apiJsonResult(array(),0,'请选择业态');
            //    }
            //}
            if (empty($data['contacts'])){
                Response::apiJsonResult(array(),0,'请填写联系人');
            }
            if (empty($data['phone'])){
                Response::apiJsonResult(array(),0,'请填写联系电话');
            }
            $ret = $business->_updateInfo(['id'=>$data['id']],$data);

            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
        }
        $Project = new Project();
        $Project_list = $Project->_getList(["state"=>1],1,100);
        $industry=new Industry();
        $industry_list = $industry->_getList(["state"=>1],1,100);

        $id = Input::request("id",0,'intval');

        $info=$business->_findone(['id'=>$id]);

        $this->assign(array(
            '_GET'=>$_GET,
            'project'=>$Project_list->items,
            'industry'=>$industry_list->items,
            'state'=>$business->state,
            'info'=>$info
        ))->base();
    }

    /**
     * 修改营业状态
     */
    public function c_state_edit(){
        $business=new Business();
        if(Input::isPost()){
            $data['id']=Input::request("id",0,'intval');
            $data['state']=Input::request('state',0,'intval');
            $data['updated_at']=time();

            $ret = $business->_updateInfo(['id'=>$data['id']],$data);

            if($ret>0){
                Response::apiJsonResult(array(
                    'id'=>$data['id'],
                    'state'=>$data['state'],
                    'state_str'=>$data['state']==1?'在营':'闭店'
                ),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
        }
    }
}