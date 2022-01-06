<?php
namespace App\Admin\Controller;
use App\Admin\Model\Account;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Menu;
use App\Admin\Model\Adv;
use App\Admin\Model\Card_No;
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
use function Psy\debug;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Session\Factory as Session;
use SsdPHP\Page\Factory as Page;
class Cuseryewu extends Common {


    /**
     * 业务list
     */
    public function c_index_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');
        $_GET['is_shangjia']    = Input::get('is_shangjia','1');


        $Yewu = new Yewu();
        $Ditch = new Softwaretemplate();
        $Software = new Software();
        $slist = $Software->getAll();
        $_GET['rj']    = Input::get('rj',$slist[0]['appid']);

        $list = $Yewu->getList(array(
            'is_shangjia'=>$_GET['is_shangjia']
        ),$_GET['page'],$_GET['pagesize'],array("*"),"sort desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$Yewu->status,
            'slist'=>$slist,
            'is_shangjia'=>$Ditch->is_shangjia,
            'open_type'=>$Yewu->open_type,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 添加业务
     */
    public function c_index_add(){
        $Yewu = new Yewu();
        $Ditch = new Softwaretemplate();
        if(Input::isPost()){

            $_POST=Input::post();
            $ret = $Yewu->add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
            return ;
        }

        $this->assign(array(
            '_GET'=>$_GET,
            'open_type'=>$Yewu->open_type,
            'status'=>$Yewu->status,
            'is_shangjia'=>$Ditch->is_shangjia,
        ))->base('yewu/index_edit');
    }
    /**
     * 添加业务
     */
    public function c_index_edit(){
        $Yewu = new Yewu();
        $Ditch = new Softwaretemplate();
        if(Input::isPost()){

            $_POST=Input::post();

            //define("DEBUG",1);
            $ret = $Yewu->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
            return ;
        }
        $id = Input::request('id',0,'intval');
        $info = $Yewu->findOne(['id'=>$id]);
        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            'open_type'=>$Yewu->open_type,
            'status'=>$Yewu->status,
            'is_shangjia'=>$Ditch->is_shangjia,
        ))->base();
    }

    /**
     * 业务list
     */
    public function c_product_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');
        $_GET['is_shangjia']    = Input::get('is_shangjia','1');


        $Yewu_Product = new Yewu_Product();
        $Yewu = new Yewu();
        $Ditch = new Softwaretemplate();
        $Software = new Software();
        $slist = $Software->getAll();
        $_GET['rj']    = Input::get('rj',$slist[0]['appid']);

        $yewu_list = $Yewu->getList(array(
            'is_shangjia'=>$_GET['is_shangjia']
        ),$_GET['page'],$_GET['pagesize'],array("*"),"sort desc")->items;

        /*print_r($yewu_list);
        die;*/



        foreach ($yewu_list as &$v){
            $v['child'] = $Yewu_Product->getList(array(
                'is_shangjia'=>$_GET['is_shangjia'],
                'yid'=>$v['id'],
            ),$_GET['page'],$_GET['pagesize'],array("*"),"sort desc")->items;
        }
        $this->assign(array(
            'yewu_list'=>$yewu_list,
            '_GET'=>$_GET,
            'status'=>$Yewu_Product->status,
            'slist'=>$slist,
            'is_shangjia'=>$Ditch->is_shangjia,
            'open_type'=>$Yewu_Product->open_type,
            'is_auto_num'=>$Yewu_Product->is_auto_num,
            'is_top'=>$Yewu_Product->is_top,
        ))->base();
    }

    /**
     * 添加业务
     */
    public function c_product_add(){
        $Yewu_Product = new Yewu_Product();
        $Yewu = new Yewu();
        $Ditch = new Softwaretemplate();
        if(Input::isPost()){

            $_POST=Input::post();
            //define("DEBUG",1);
            $ret = $Yewu_Product->add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
            return ;
        }

        $yewu_list = $Yewu->getList();
        $this->assign(array(
            '_GET'=>$_GET,
            'open_type'=>$Yewu_Product->open_type,
            'status'=>$Yewu_Product->status,
            'is_shangjia'=>$Ditch->is_shangjia,
            'is_auto_num'=>$Yewu_Product->is_auto_num,
            'is_top'=>$Yewu_Product->is_top,
            'yewu_list'=>$yewu_list->items,
        ))->base('yewu/product_edit');
    }
    /**
     * 添加业务
     */
    public function c_product_edit(){
        $Yewu = new Yewu();
        $Yewu_Product = new Yewu_Product();
        $Ditch = new Softwaretemplate();
        if(Input::isPost()){

            $_POST=Input::post();

            //define("DEBUG",1);
            $ret = $Yewu_Product->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
            return ;
        }
        $id = Input::request('id',0,'intval');
        $info = $Yewu_Product->findOne(['id'=>$id]);
        $yewu_list = $Yewu->getList();
        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            'open_type'=>$Yewu_Product->open_type,
            'status'=>$Yewu_Product->status,
            'is_shangjia'=>$Ditch->is_shangjia,
            'is_auto_num'=>$Yewu_Product->is_auto_num,
            'is_top'=>$Yewu_Product->is_top,
            'yewu_list'=>$yewu_list->items,
        ))->base();
    }

    /**
     * 业务list
     */
    public function c_price_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');
        $_GET['is_shangjia']    = Input::get('is_shangjia','1');
        $_GET['pid']    = Input::get('pid','0','intval');


        $Yewu_Price = new Yewu_Price();
        $Yewu_Product = new Yewu_Product();
        $Ditch = new Softwaretemplate();
        $Software = new Software();
        $slist = $Software->getAll();
        $_GET['rj']    = Input::get('rj',$slist[0]['appid']);
        $pinfo = $Yewu_Product->findOne(['id'=>$_GET['pid']]);
        $list = $Yewu_Price->getList(array(
            "pid"=>$_GET['pid']
        ),$_GET['page'],$_GET['pagesize'],array("*"),"sort desc");
        $this->assign(array(
            'list'=>$list,
            'pinfo'=>$pinfo,
            '_GET'=>$_GET,
            'status'=>$Yewu_Price->status,
            'slist'=>$slist,
            'is_shangjia'=>$Ditch->is_shangjia,
        ))->base();
    }

    /**
     * 添加业务
     */
    public function c_price_add(){
        $Yewu_Price = new Yewu_Price();
        $Yewu_Product = new Yewu_Product();
        if(Input::isPost()){

            $_POST=Input::post();
            //define("DEBUG",1);
            $ret = $Yewu_Price->add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
            return ;
        }
        $_GET['pid']=Input::request('pid',0,'intval');
        $pinfo = $Yewu_Product->findOne(['id'=>$_GET['pid']]);
        $this->assign(array(
            '_GET'=>$_GET,
            'pinfo'=>$pinfo,
            'status'=>$Yewu_Price->status,
        ))->base('yewu/price_edit');
    }
    /**
     * 添加业务
     */
    public function c_price_edit(){
        $Yewu_Price = new Yewu_Price();
        if(Input::isPost()){

            $_POST=Input::post();

            //define("DEBUG",1);
            $ret = $Yewu_Price->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
            return ;
        }
        $id = Input::request('id',0,'intval');
        $info = $Yewu_Price->findOne(['id'=>$id]);
        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            'status'=>$Yewu_Price->status,
        ))->base();
    }

    /**
     * 金币管理
     */
    public function c_jinbi_list(){

        $Software = new Software();
        $Product = new Product();
        $_GET['status']=Input::get('status',1);
        $slist = $Software->getAll();
        $list = $Product->findall(array(
            'status'=>$_GET['status']
        ),array("*"));
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$Product->status,
        ))->base();
    }

    /**
     * 添加业务
     */
    public function c_jinbi_add(){
        $Product = new Product();
        if(Input::isPost()){

            $_POST=Input::post();
            //define("DEBUG",1);
            $ret = $Product->add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
            return ;
        }
        $this->assign(array(
            '_GET'=>$_GET,
            'status'=>$Product->status,
        ))->base('yewu/jinbi_edit');
    }
    /**
     * 添加业务
     */
    public function c_jinbi_edit(){
        $Product = new Product();
        if(Input::isPost()){

            $_POST=Input::post();

            //define("DEBUG",1);
            $_POST['update_time']=time();
            $ret = $Product->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
            return ;
        }
        $id = Input::request('id',0,'intval');
        $info = $Product->findOne(['id'=>$id]);
        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            'status'=>$Product->status,
        ))->base();
    }

    //任务步骤设置
    public function c_task_step(){

        $Task_Step = new Yewu_Task_Step();
        $_GET['pid']=Input::get('pid',1);
        $list = $Task_Step->getList(array(
            "pid"=>$_GET['pid']
        ),1,100,array("*"));
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$Task_Step->status,
        ))->base();
    }


    /**
     * 添加步骤配置
     */
    public function c_task_step_add(){
        $Yewu_Task_Step = new Yewu_Task_Step();
        $Yewu_Product = new Yewu_Product();
        if(Input::isPost()){

            $_POST=Input::post();
            $ret = $Yewu_Task_Step->add($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
            return ;
        }
        $_GET['pid']=Input::request('pid',0,'intval');
        $pinfo = $Yewu_Product->findOne(['id'=>$_GET['pid']]);
        $this->assign(array(
            '_GET'=>$_GET,
            'pinfo'=>$pinfo,
            'status'=>$Yewu_Task_Step->status,
        ))->base('yewu/task_step_edit');
    }
    /**
     * 添加业务
     */
    public function c_task_step_edit(){
        $Yewu_Price = new Yewu_Task_Step();
        $Yewu_Product = new Yewu_Product();
        if(Input::isPost()){

            $_POST=Input::post();

            //define("DEBUG",1);
            $ret = $Yewu_Price->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'修改成功');
            }
            Response::apiJsonResult(array(),0,'修改失败');
            return ;
        }

        $id = Input::request('id',0,'intval');
        $info = $Yewu_Price->findOne(['id'=>$id]);
        $pinfo = $Yewu_Product->findOne(['id'=>$info['pid']]);
        $this->assign(array(
            'info'=>$info,
            '_GET'=>$_GET,
            'pinfo'=>$pinfo,
            'status'=>$Yewu_Price->status,
        ))->base();
    }
}