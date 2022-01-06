<?php
namespace App\Admin\Controller;
use App\Admin\Model\Account;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Menu;
use App\Admin\Model\Adv;
use App\Admin\Model\Card_No;
use App\Admin\Model\Category;
use App\Admin\Model\Softwaretemplate;
use App\Admin\Model\Jifen_Rule;
use App\Admin\Model\Order;
use App\Admin\Model\Product;
use App\Admin\Model\Project;
use App\Admin\Model\Software;
use App\Admin\Model\Task_Step;
use App\Admin\Model\Yewu;
use App\Admin\Model\Yewu_Price;
use App\Admin\Model\Yewu_Product;
use App\Admin\Model\Yewu_Task_Step;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Session\Factory as Session;
use SsdPHP\Page\Factory as Page;
class Cweb extends Common {


    /**
     * 业务分类list
     */
    public function c_category(){

        $Category = new Category();

        $list = $Category->_getList(array('pid'=>0),1,100,array("*"),"sort desc");

        $this->assign(array(
            'list'=>$list,
        ))->base();
    }
    /**
     * 文章列表
     */
    public function c_article_list(){

        $Category = new Category();
        $_GET['pid'] = Input::request('pid',0,'intval');
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = 10;
        $_GET['top']    = Input::request('top',-1,'intval');
        $_GET['status']    = Input::request('status',-1,'intval');


        $Product  = new Product();
        $cond = array();
        if($_GET['top']>-1){
            $cond['top']=$_GET['top'];
        }
        if($_GET['status']>-1){
            $cond['display']=$_GET['status'];
        }
        if(!empty($_GET['pid']) && $_GET['pid']>-1){
            $cond['pid']=$_GET['pid'];
        }
        if(!empty($_GET['id'])){
            $cond['id']=$_GET['id'];
        }
        if(!empty($_GET['title'])){
            $cond[]='title like "%'.$_GET['title'].'%"';
        }
//        define("DEBUG",1);
        $list = $Product->_getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"top desc,level desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        foreach ($list->items as &$v){
            $v['explain'] = mb_substr(strip_tags(htmlspecialchars_decode($v['explain'])),0,30).'...';
            $v['category_name'] = $Category->_findone(array('id'=>$v['pid']),array("name"))['name']??'--';
        }
        $yewu_list = $Category->_getList(array('pid'=>0,'status'=>0),1,100,array("id",'name'),"sort desc")->items;
        $this->assign(array(
            '_GET'=>$_GET,
            'list'=>$list,
            'category'=>$yewu_list,
            'top'=>array(0=>'不推荐',1=>'推荐'),
            'status'=>array(0=>'正常',1=>'关闭'),
            'page'=>$Page->show(),
        ))->base();
    }


    /**
     * 添加业务
     */
    public function c_product_add(){
        $Product = new Product();
        $Category = new Category();
        if(Input::isPost()){

            $_POST=Input::post();
            $_POST['create_time']=time();
            $ret = $Product->_add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
            return ;
        }
        $_GET['id'] = Input::request('id',0,'intval');
        $_GET['pid'] = Input::request('pid',0,'intval');
        $info = array();
        if(!empty( $_GET['id'])){
            $info = $Product->_findone(array());
        }
        $yewu_list = $Category->_getList(array('pid'=>0,'status'=>0),1,100,array("id",'name'),"sort desc");
        $this->assign(array(
            '_GET'=>$_GET,
            'category'=>$yewu_list->items,
            'info'=>$info,
            'top'=>array(0=>'不推荐',1=>'推荐'),
            'status'=>array(0=>'正常',1=>'关闭'),
        ))->base('web/product_edit');
    }
    /**
     * 添加业务
     */
    public function c_product_edit(){
        $Product = new Product();
        $Category = new Category();

        if(Input::isPost()){

            $_POST=Input::post();
            if(empty($_POST['id'])){
                Response::apiJsonResult(array(),0,'id不能为空');
            }

//            if(!empty($_POST['explain'])){
////                //$_POST['explain'] = htmlspecialchars($_POST['explain']);
////            }
            $_POST['update_time']=time();
            $ret = $Product->_updateInfo(array('id'=>$_POST['id']),$_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
            return ;
        }
        $_GET['id'] = Input::request('id',0,'intval');
        $_GET['pid'] = Input::request('pid',0,'intval');
        $info = array();
        if(!empty( $_GET['id'])){
            $info = $Product->_findone(array('id'=>$_GET['id']));
            if(!empty($info['explain'])){
                $info['explain'] = htmlspecialchars_decode($info['explain']);
            }
        }
        $yewu_list = $Category->_getList(array('pid'=>0,'status'=>0),1,100,array("id",'name'),"sort desc");
        $this->assign(array(
            '_GET'=>$_GET,
            'category'=>$yewu_list->items,
            'info'=>$info,
            'top'=>array(0=>'不推荐',1=>'推荐'),
            'status'=>array(0=>'正常',1=>'关闭'),
        ))->base('web/product_edit');
    }


}