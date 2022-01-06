<?php
namespace App\Admin\Controller;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Menu;
use App\Admin\Model\Adv;
use App\Admin\Model\Card_No;
use App\Admin\Model\Jifen_Rule;
use App\Admin\Model\Product;
use App\Admin\Model\Project;
use App\Admin\Model\Software_Version;
use App\Admin\model\SoftwareVersion;
use App\Admin\Model\Video_Product_Catgroy;
use App\Api\Model\Sys_Notice;
use App\Common\Tool\Functions;
use Qiniu\Auth;
use SsdPHP\Core\Config;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Page\Factory as Page;
use SsdPHP\SsdPHP;
use SsdPHP\Pulgins\PushBaiduNew\PushSDK;
class Cconfig extends Common {

    /**
     * 卡密列表
     */
    public function c_list(){

        $list = Config::all();
        $this->assign(array('list'=>$list))->base();
    }

    public function c_shortcut_grop(){
        $status=array(
            '0'=>'不跳转什么',
            '1'=>'url',
            '2'=>'指定一个app跳转界面',
        );

        $Config = new \App\Admin\Model\Config();
        $info = $Config->findone(array('key'=>'Config_shortcut'));
        $config_data=array('shortcut'=>array());
        if(!empty($info['value']) ){
            $config_data = json_decode($info['value'],true);
        }
        if(Input::isPost()){
            $d = json_encode($_POST);
            $key = $Config->edit(array(
                "key"=>"Config_shortcut",
                "value"=>$d
            ));

            Response::apiJsonResult(array(),1,"设置成功");
        }

        $this->assign(array(
            "shortcut"=>$config_data['shortcut'],
            "type"=>$status
        ))->base();
    }

    public function c_top_config(){
        $status=array(
            '0'=>'不跳转什么',
            '1'=>'url',
            '2'=>'指定一个app跳转界面',
        );

        $Config = new \App\Admin\Model\Config();
        $info = $Config->findone(array('key'=>'Config_indextop'));
        $config_data=array('shortcut'=>array());
        if(!empty($info['value']) ){
            $config_data = json_decode($info['value'],true);
        }
        if(Input::isPost()){
            $d = json_encode($_POST);
            $key = $Config->edit(array(
                "key"=>"Config_indextop",
                "value"=>$d
            ));

            Response::apiJsonResult(array(),1,"设置成功");
        }

        $this->assign(array(
            "shortcut"=>$config_data['shortcut'],
            "type"=>$status
        ))->base();
    }
    public function c_other_config(){
        $status=array(
            '0'=>'不跳转什么',
            '1'=>'url',
            '2'=>'指定一个app跳转界面',
        );

        $Config = new \App\Admin\Model\Config();
        $info = $Config->findone(array('key'=>'Config_other'));
        $config_data=array('shortcut'=>array());
        if(!empty($info['value']) ){
            $config_data = json_decode($info['value'],true);
        }
        if(Input::isPost()){
            $d = json_encode($_POST);
            $key = $Config->edit(array(
                "key"=>"Config_other",
                "value"=>$d
            ));

            Response::apiJsonResult(array(),1,"设置成功");
        }

        $this->assign(array(
            "shortcut"=>$config_data['shortcut'],
            "type"=>$status
        ))->base();
    }


    public function c_paytype(){
        $status=array(
            '0'=>'禁用',
            '1'=>'启用',
        );

        $Config = new \App\Admin\Model\Config();
        $info = $Config->findone(array('key'=>'Config_paytype'));
        $config_data=array('shortcut'=>array());
        if(!empty($info['value']) ){
            $config_data['shortcut'] = json_decode($info['value'],true);
        }
        if(Input::isPost()){
            $d = json_encode($_POST['shortcut']);
            $key = $Config->edit(array(
                "key"=>"Config_paytype",
                "value"=>$d
            ));

            Response::apiJsonResult(array(),1,"设置成功");
        }

        $this->assign(array(
            "shortcut"=>$config_data['shortcut'],
            "type"=>$status,
            "open_type"=>array(
                0=>'余额处理',
                1=>'内部打开',
                2=>'外部打开',
            )
        ))->base();
    }
}