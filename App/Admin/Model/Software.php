<?php
/**
 * Created by PhpStorm.
 * User: xzh_tx@163.com
 * Date: 2017/3/14
 * Time: 16:04
 */
namespace App\Admin\Model;

use App\Common\PushXg\XingeApp;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Pulgins\Cache\Factory as Cache;
use SsdPHP\Pulgins\Session\Factory as Session;
use SsdPHP\Pulgins\DataBase\Factory as Db;
use SsdPHP\PushBaiduNew\PushSDK;

/**
 * 渠道管理
 * Class Member
 * @author xiaohuihui <xzh_tx@163.com>
 */
class Software  extends Model
{
    //状态，0-禁用。1-正常
    public $status=array(0=>'禁用',1=>'正常');

    //苹果推送开发状态，1（开发状态）和 2（生产状态）
    public $push_status=array(1=>'开发状态',2=>'生产状态');

    public $os=array(1=>'安卓',2=>'苹果',3=>'小程序');

    public $reg_type=array(
        "phone"=>"电话短信注册",
        "qq"=>"QQ注册",
        "weixin"=>"微信注册",
        "imei"=>"唯一识别码注册",
    );


    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getAll($cond=array(),$field=array(
        "*",
    ),$order="id asc"){

        $a = $this->select($cond,$field)->items;

        return $a;
    }
    /**
     * 添加
     * @return mixed
     */
    public function _add($data){

        if(empty($data)){
            return -1;
        }
        $data['create_time']=time();
        unset($data['id']);
        $id = $this->insert($data);

        if($id>0){
           return $id;
        }

        return -2;
    }

    /**
     * 修改
     * @param $data
     * @return mixed
     */
    public function edit($data)
    {

        if(empty($data) || (empty($data['id']) && empty($data['did']))){
            return -1;
        }

        $cond = array("id"=>intval($data['id']));
        $id = $this->update($cond,$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            return $id;
        }
        return -2;
    }



    /**
     * @param string $msg 消息内容
     * @param int $p 项目分类，0-粉丝，1-双击
     * @param int $os 1-安卓，2-苹果
     * @param $channel 推送对象，all-所有，否则是指定channel_id
     * @return
     */
    public function BaiduPush($msg="",$p="",$os,$channel="all",$ext=""){
        if(empty($p) || empty($channel)){
            return false;
        }
        $sinfo = $this->findOne(array('appid'=>$p));
        $opts = array(
            'msg_type' => 1,
            //deploy_status int 可取值 1（开发状态）和 2（生产状态）仅iOS推送使用。
            'deploy_status' => intval($sinfo['ios_push_status'])
        );
        $msg = str_replace("<point>","·",$msg);
        $message = array(
            // 消息的标题.
            //'title' => "",
            // 消息内容
            'description' => $msg,
            'ext' => $ext,
        );
        $message['aps'] =  array(
            "badge" => 1,
            "alert" => $msg,
            'ext' => $ext,//msg_type|sys-msg
            //"content-available"=>"1",
            // 提示音，需要在Xcode工程中添加同名的音频资源 //"sound" => "default" // 提示音，需要在Xcode工程中添加同名的音频资源
            "sound" => "default" ,
        );

        $PushSDK = new PushSDK();

        //{{{
        $debug = array();
        if($os==1){ //安卓推送

            $sinfo = $this->findOne(array('appid'=>$p));

            //$PushSDK->setApiKey($sinfo['ard_push_key']);
            //$PushSDK->setSecretKey($sinfo['ard_push_secret']);
            if($channel=="all"){
                $s = XingeApp::PushAllAndroid($sinfo['ard_push_key'], $sinfo['ard_push_secret'], "你有一条互粉宝系统消息", $message);
                //$s = $PushSDK->pushMsgToAll($message, $opts);
            }else{
                XingeApp::PushAccountAndroid($sinfo['ard_push_key'],$sinfo['ard_push_secret'],"你有一条互粉宝系统消息",$message,$channel);
                //$s = $PushSDK->pushMsgToSingleDevice($channel,$message, $opts);
            }
            $debug['a']=1;
        }
        //}}}
        //{{{
        if($os==2){//苹果推送
            $PushSDK->setApiKey($sinfo['ios_push_key']);
            $PushSDK->setSecretKey($sinfo['ios_push_secret']);
            if($channel=="all"){
                $s = $PushSDK->pushMsgToAll($message, $opts);
            }else{
                $s = $PushSDK->pushMsgToSingleDevice($channel,$message, $opts);
            }
        }
        //}}}
        return true;
    }
}