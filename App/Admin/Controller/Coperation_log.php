<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 15:48
 */

namespace App\Admin\Controller;

use App\Admin\Model\Operation_log;
use SsdPHP\Page\Factory as Page;
use SsdPHP\Http\Input;
use App\Admin\Model\Admin;
use SsdPHP\Http\Response;

class Coperation_log extends Common {

    public function c_record_list(){

        $_GET = Input::get();
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['uid']    = Input::get('uid',"");
        $_GET['create_time'] = Input::get('create_time',"");
        $cond=array();
        if(!empty($_GET['uid'])){
            $cond['uid']=$_GET['uid'];
        }
        if(!empty($_GET['create_time'])){
            $tiem = strtotime($_GET['create_time']);
            $cond[]="create_time>$tiem and create_time<$tiem+86400";
        }
        $admin=new Admin();
        $adminInfo = $admin->getList(array());

        $model = new Operation_log();
        $list = $model->getList($cond,$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            '_GET'=>$_GET,
            'list'=>$list,
            'page'=>$Page->show(),
            'adminInfo'=>$adminInfo
        ))->base();
    }

    //查看操作参数
    public function c_operation_log_data(){
        $model = new Operation_log();
        $id = Input::request('id');
        $ret = $model->findone(array("id"=>$id));
        Response::apiJsonResult($ret,1);
    }
}