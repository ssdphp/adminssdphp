<?php
namespace App\Admin\Controller;
use App\Admin\Model\Account;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Menu;
use App\Admin\Model\Adv;
use App\Admin\Model\Card_No;
use App\Admin\Model\Jifen_Rule;
use App\Admin\Model\Order;
use App\Admin\Model\Product;
use App\Admin\Model\Product_Category_Cat;
use App\Admin\Model\Project;
use App\Admin\Model\Product_Category;
use App\Admin\Model\Zuojia_Product;
use App\Admin\Model\Zuojia_Product_Catgroy;
use App\Admin\Model\Video_Product;
use App\Admin\Model\Video_Product_Catgroy;
use SsdPHP\Core\Config;
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\Pulgins\Http\Response;
use SsdPHP\Pulgins\Session\Factory as Session;
use SsdPHP\Pulgins\Page\Factory as Page;
class Cvideo_shop extends Common {

    public function __get($name)
    {
        // TODO: Implement __get() method.
        $Project = new Video_Product();
        $name =str_replace("_","","get". $name);
        return $Project->$name();
    }

    public function __construct()
    {
        parent::__construct();
        $Project = new Video_Product();
        $this->assign(
            'icon_type',$Project->getIconType()
        );
    }
    /**
     * 业务list
     */
    public function c_product_categroy(){

        $_GET['page'] = Input::request('page',1,'intval');
        $_GET['k'] = Input::request('k','product/categroy','');
        $model = new Video_Product_Catgroy();

        $_GET['is_shangjia'] = Input::request('is_shangjia',1);
        $list = $model->getList(array('is_shangjia'=>$_GET['is_shangjia']),array("*"));
        //print_r($list);
        //$Page = new Page($list->totalSize,$_GET['pagesize']);
        $ccatlist = $model->getList();
        $index=array();
        foreach ($ccatlist as $v){
            $index[$v['id']]=$v;
        }
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'index'=>$index,
            'status'=>$model->status,
            'ccat'=>$model->ccat,
            'is_shangjia'=>$model->is_shangjia,
        ))->base();
    }



    /**
     * 业务list
     */
    public function c_product(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');
        $_GET['is_shangjia']    = Input::get('is_shangjia',1,'intval');
        $_GET['pid']    = Input::get('pid',0,'intval');
        $adv = new Project();

        $Product_Category = new Video_Product_Catgroy();
        $Zuojia_Product = new Video_Product();

        $cond = array();
        $cond['is_shangjia']=$_GET['is_shangjia'];
        if(!empty($_GET['pid'])){
            $cond['cid']=$_GET['pid'];
        }
        $list = $Zuojia_Product->getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"sort desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
            'status'=>$this->status,
            'teshu_product_type'=>$this->teshu_product_type,
            'shua_pingtai'=>$this->shua_pingtai,
            'page_type'=>$this->page_type,
            'need_aid'=>$this->need_aid,
            'project'=>$adv->getSoftList(),
            'is_shangjia'=>$Product_Category->is_shangjia,
        ))->base();
    }



    /**
     * 修改业务
     */
    public function c_product_edit(){
        $id = Input::get('id');
        $model = new Video_Product();
        $info = $model->findOne(array('id'=>$id));
        if(Input::isPost()){
            $Admin = new Video_Product();
            $_POST=Input::post();
            $_POST['update_time']=time();
            $ret = $Admin->edit($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1003);
            }
            Response::apiJsonResult(array(),$ret['code']);
            return ;
        }
        $PROJECT_JS_MODEL = Config::get('PROJECT_JS_MODEL',$this->project_cate);
        $Product_Category = new Video_Product_Catgroy();
        $plist = $Product_Category->getList(array());
        $this->assign(array(
            '_GET'=>$_GET,
            'info'=>$info,
            'status'=>$this->status,
            'teshu_product_type'=>$this->teshu_product_type,
            'plist'=>$plist,
            'top'=>$Product_Category->top,
            'shua_pingtai'=>$this->shua_pingtai,
            'page_type'=>$this->page_type,
            'project_cate'=>$PROJECT_JS_MODEL,
        ))->base();
    }

    /**
     * 添加业务
     */
    public function c_product_add(){
        $Admin = new Video_Product();
        if(Input::isPost()){

            $_POST=Input::post();
            $ret = $Admin->add($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1002);
            }
            Response::apiJsonResult(array(),$ret['code']);
            return ;
        }
        $Product_Category = new Video_Product_Catgroy();
        $plist = $Product_Category->getList();
        $PROJECT_JS_MODEL = Config::get('PROJECT_JS_MODEL',$this->project_cate);
        $this->assign(array(
            '_GET'=>$_GET,
            'status'=>$this->status,
            'shua_pingtai'=>$this->shua_pingtai,
            'teshu_product_type'=>$this->teshu_product_type,
            'top'=>$Product_Category->top,
            'plist'=>$plist,
            'page_type'=>$this->page_type,
        ))->base('video_shop/product_edit');
    }

    /**
     * 修改业务
     */
    public function c_product_categroy_edit(){
        $id = Input::get('id');
        $model = new Video_Product_Catgroy();

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
            'is_shangjia'=>$model->is_shangjia,
        ))->base();
    }
    /**
     * 添加业务
     */
    public function c_product_categroy_add(){
        $Admin = new Video_Product_Catgroy();
        if(Input::isPost()){
            $_POST=Input::post();
            $ret = $Admin->add($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),$ret['code']);
            return ;
        }
        $this->assign(array(
            '_GET'=>$_GET,
            'status'=>$Admin->status,
            'is_shangjia'=>$Admin->is_shangjia,
        ))->base('video_shop/product_categroy_edit');
    }


}