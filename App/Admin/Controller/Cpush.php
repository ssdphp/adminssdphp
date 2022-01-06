<?php
namespace App\Admin\Controller;
use App\Admin\Model\Config as AConfig;
use App\Admin\Model\Software;
use App\Api\Model\Sys_Notice;
use App\Common\PushXg\XingeApp;
use function Psy\debug;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
class Cpush extends Common {

    public function c_error_config(){
        $AConfig = new AConfig();
        $info['PUSH_ERROR_receiver'] = $AConfig->findone(['key'=>'PUSH_ERROR_receiver'])['value'];
        $info['PUSH_ERROR_source'] = $AConfig->findone(['key'=>'PUSH_ERROR_source'])['value'];

        $this->assign(array(
            '_GET'=>$_GET,
            'info'=>$info
        ))->base();
    }

    public function c_error_config_edit(){
        $Software = new Software();
        $sinfo = $Software->getAll();
        $this->assign(array(
            '_GET'=>$_GET,
            'sinfo'=>$sinfo,
        ))->base();
    }

    /**
     * 百度推送
     */
    public function c_baidu_push(){

        $Software = new Software();
        if(Input::isAJAX()){
            $msg = Input::request('msg');
            $p = Input::request('rj');
            $os = Input::request('os',1,'intval');


            if($os == 1){
                $sinfo = $Software->findone(array('appid'=>$p));
                $s = XingeApp::PushAllAndroid($sinfo['ard_push_key'], $sinfo['ard_push_secret'], "你有一条互粉宝系统消息", $msg);
            }else{
                $s = $Software->BaiduPush($msg,$p,$os,"all",array("msg_type"=>"sys"));

            }

            $Sys_Notice = new Sys_Notice();
            $id = $Sys_Notice->add(array(
                "rj"=>$p,
                "os"=>$os,
                "content"=>$msg
            ));
            if($id>0){
                Response::apiJsonResult(array(),1,"发布系统消息推送成功。");
            }
            Response::apiJsonResult(array(),0,"发布系统消息推送异常。");

        }

        $slist = $Software->getAll();
        $_GET['rj'] = Input::request('rj',$slist[0]['appid']);
        $this->assign(array(
            "slist"=>$slist,
            "_GET"=>$_GET,
            "os"=>$Software->os,
        ))->base();
    }
}