<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use App\Api\Plugin\Curl;
use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\SsdPHP;

class Account_Ysconfig extends Model {


    public function getconfig ($uid){
        $uid=$uid??UID;
        $cond = array("uid"=>$uid);
        $Account_Card = new Account_Card();
        $cardInfo = $Account_Card->findone(array('uid'=>$uid,'is_used'=>1),array('*'));
        $cinfo = $this->findone($cond);
        $ysData = array();

        $yscfg = json_decode($cinfo['yscfg']??"",true);
        $yscfg = $yscfg?$yscfg:array();
        /**
         * 用户隐私设置
         * on:开
         * off:关
         */
        $ysData['yscfg']=array(

            //被别人搜索
            'search' => !empty($yscfg['search'])?$yscfg['search']:'off',

            //电话公开
            'phone'  => !empty($yscfg['phone'])?$yscfg['phone']:'off',

            //邮箱公开
            'email'  => !empty($yscfg['email'])?$yscfg['email']:'off',
            //微信开关
            'weixin'  => !empty($yscfg['weixin'])?$yscfg['weixin']:'off',

            //地址默认开。不控制
            //'addr'  => !empty($yscfg['addr'])?$yscfg['addr']:'off',
            'addr'  => "on",

        );
        $cardsharecfg = json_decode($cinfo['cardsharecfg']??"",true);
        $cardsharecfg = $cardsharecfg?$cardsharecfg:array();
        /**
         * 名片分享抬头设置
         */
        $ysData['cardsharecfg']=array(
            'icon'=>!empty($cardsharecfg['icon'])?$cardsharecfg['icon']:$cardInfo['info_headimg'],
            'title'=>!empty($cardsharecfg['title'])?$cardsharecfg['title']:"我是".$cardInfo['info_name'],
            'content'=>!empty($cardsharecfg['content'])?$cardsharecfg['content']:"您好。这是我的咨脉AI电子名片，请您查看，多联系哦",
        );

        /**
         * 用户留言开关
         * on:开-默认
         * off:关
         */
        //$ysData['is_feedback']=!empty($cinfo['is_feedback'])?$cinfo['is_feedback']:'on';
        $ysData['is_feedback']='off';

        /**
         * 统计开关
         * on:开-默认
         * off:关
         */
        $ysData['is_total']=!empty($cinfo['is_total'])?$cinfo['is_total']:'on';

        return $ysData;
    }
}