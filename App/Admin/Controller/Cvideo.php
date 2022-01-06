<?php
namespace App\Admin\Controller;
use App\Admin\Model\Account;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Menu;
use App\Admin\Model\Card_No;
use App\Admin\Model\Jifen_Rule;
use App\Admin\Model\Order;
use App\Admin\Model\Product;
use App\Admin\Model\Project;
use App\Admin\Model\Video_Parse_Map;
use App\Admin\Model\Zuojia_Content;
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\Pulgins\Http\Response;
use SsdPHP\Pulgins\Session\Factory as Session;
use SsdPHP\Pulgins\Page\Factory as Page;
class Cvideo extends Common {



    /**
     * 业务list
     */
    public function c_parse_url(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');

        $model = new Video_Parse_Map();
        $list = $model->getList(array(),$_GET['page'],$_GET['pagesize'],array("*"));

        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'is_three'=>$model->status,
            'vtype'=>$model->vtype,
            'page'=>$Page->show(),
        ))->base();
    }

    /**
     * 修改业务
     */
    public function c_parse_url_edit(){
        $id = Input::get('id');
        $model = new Video_Parse_Map();
        $info = $model->findOne(array('id'=>$id));
        if(Input::isPost()){
            $_POST=Input::post();
            $_POST['update_time']=time();
            $ret = $model->edit($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1003);
            }
            Response::apiJsonResult(array(),$ret['code']);
            return ;
        }
        $this->assign(array(
            '_GET'=>$_GET,
            'info'=>$info,
            'status'=>$model->status,
            'vtype'=>$model->vtype,
        ))->base('video/parse_url_edit');
    }
    /**
     * 添加业务
     */
    public function c_parse_url_add(){
        $Admin = new Video_Parse_Map();
        if(Input::isPost()){

            $_POST=Input::post();
            $ret = $Admin->add($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1002);
            }
            Response::apiJsonResult(array(),$ret['code']);
            return ;
        }
        $this->assign(array(
            '_GET'=>$_GET,
            'status'=>$Admin->status,
            'vtype'=>$Admin->vtype,
        ))->base('video/parse_url_edit');
    }

}