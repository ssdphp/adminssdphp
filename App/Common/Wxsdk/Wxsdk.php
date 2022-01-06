<?php
/**
 * Created by PhpStorm.
 * User: xzh
 * Date: 2019/6/27
 * Time: 11:42 AM
 */

namespace App\Common\Wxsdk ;

use SsdPHP\Core\Config;
use App\Common\Wxsdk\Adaptor\Weixin;

class Wxsdk extends Weixin{


    /**
     * 获取微信公众号的token
     * @param array $config['appid','secret']
     * @return mixed|string
     * @throws \Exception
     */
    public static function get_gzh_access_token($config = array())
    {
        return parent::get_gzh_access_token($config);
    }

    /**
     * 设置公众号的菜单
     * @param $config['appid','secret']
     * @param $menu string
     * @return mixed|string|void
     * @throws \Exception
     */
    public static function gzh_set_menu($config,$menu){
        return parent::gzh_set_menu($config,$menu);
    }


    /**
     * 获取公众号web code
     * @param $url
     * @param string $scope
     * @return bool|string
     */
    public static function gzh_getcode($url,$scope='snsapi_userinfo'){
        if(empty($url)){
            exit("操作有误");
        }
        $url = urlencode($url);
        return self::gzh_getcode_url($scope,$url);
    }

    /**
     * @param array $data [touser,template_id,page,data] //模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
     */
    public static function SubscribeNotice ($data=array()){
        $token = self::get_gzh_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/bizsend?access_token={$token}";

        $s = parent::postXmlCurl(json_encode($data),$url);

    }

    /**
     * 获取语音数据
     * @param $mid
     * @param $is_gq
     * @return bool|string
     */
    public static function gzh_media_down($id,$mid,$is_gq){

        return parent::down_gzh_voic_media($id,$mid,$is_gq);
    }



    /**
     * 通过code获取微信用户基本信息
     * @inheritdoc https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public static function gzh_getuserinfo($code){
        if(empty($code)){
            return -1;
        }
        $config = Config::getField('weixin','gzh');
        $wxaccessData = self::access_token($code,$config);

        if(empty($wxaccessData)){
            return -2;
        }
        $d = json_decode($wxaccessData,true);
        if(empty($d)){
            return -4;
        }
        if(!empty($d['errcode'])){
            //{"errcode":40163,"errmsg":"code been used, hints: [ req_id: NgnCj24ce-2Rl5ka ]"}
            if($d['errcode'] == '40163'){
                return -5;
            }
            return -6;
        }

        $userinfo = self::getuserinfo($d['access_token'],$d['unionid']);
        if(empty($userinfo)){
            return -7;
        }

        $userdata = json_decode($userinfo,true);
        if(empty($userdata)){
            return -8;
        }

        if(!empty($userdata['errcode'])){
            return -9;
        }

        return $userdata;
    }

    /**
     * H5支付统一下单微信统一下单接口
     * @param float $fee 金额，以人类正常理解的面值操作。
     * @param string $order_no 订单号，商户自定义生成的订单号码
     * @param string $body 商品说明描述
     * @param string $clientIp 客户端ip
     * @return int|string
     */
    public static function H5_unifiedorder($fee,$order_no,$body,$clientIp){
        $pay_data=array();
        $pay_data['body']               = !empty($body) ? $body :"";
        $pay_data['spbill_create_ip']   = !empty($clientIp)?$clientIp:$_SERVER["REMOTE_ADDR"];
        $pay_data['total_fee']          = !empty($fee)  ? $fee*100:0;
        if($pay_data['total_fee'] < 1){
            return -1;
        }
        $pay_data['out_trade_no']       = !empty($order_no)?$order_no:"";
        if(empty($pay_data['out_trade_no'])){
            return -2;
        }

        $pay_config                     = Config::getField('Weixin','H5Pay');
        $pay_data['appid']              = $pay_config['appid'];
        $pay_data['mch_id']             = $pay_config['mch_id'];
        $pay_data['nonce_str']          = substr(md5(mt_rand()),8,16);
        $pay_data['notify_url']         = $pay_config['notify_url'];
        $pay_data['trade_type']         = $pay_config['trade_type'];
        $pay_data['sign']               = self::sign($pay_data,$pay_config['key']);
        $data = self::xml_encode($pay_data);

        libxml_disable_entity_loader(true);
        $xml = self::postXmlCurl($data,$pay_config['order_url']);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $wxdata = json_decode(json_encode($xmlstring),true);

        if(empty($wxdata['sign'])){
            return -3;
        }
        $sign = strtoupper(Pay::sign($wxdata,$pay_config['key']));
        if($wxdata['sign']!==$sign){
            return -4;
        }
        return !empty($wxdata['mweb_url'])?$wxdata['mweb_url']:-5;
    }

    /**
     * 微信扫码支付订单生成
     * @param $fee
     * @param $order_no
     * @param $body
     * @param $clientIp
     * @return int
     */
    public static function qr_unifiedorder($fee,$order_no,$body,$clientIp){
        $pay_data=array();
        $pay_data['body']               = !empty($body) ? $body :"";
        $pay_data['spbill_create_ip']   = !empty($clientIp)?$clientIp:$_SERVER["REMOTE_ADDR"];
        $pay_data['total_fee']          = !empty($fee)  ? $fee*100:0;
        if($pay_data['total_fee'] < 1){
            return -1;
        }
        $pay_data['out_trade_no']       = !empty($order_no)?$order_no:"";
        if(empty($pay_data['out_trade_no'])){
            return -2;
        }

        $pay_config                     = Config::getField('weixin','PCPay');
        $pay_data['appid']              = $pay_config['appid'];
        $pay_data['mch_id']             = $pay_config['mch_id'];
        $pay_data['nonce_str']          = substr(md5(mt_rand()),8,16);
        $pay_data['notify_url']         = $pay_config['notify_url'];
        $pay_data['trade_type']         = $pay_config['trade_type'];
        $pay_data['sign']               = self::sign($pay_data,$pay_config['key']);
        $data = self::xml_encode($pay_data);

        libxml_disable_entity_loader(true);
        $xml = self::postXmlCurl($data,$pay_config['order_url']);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $wxdata = json_decode(json_encode($xmlstring),true);
        if(empty($wxdata['sign'])){
            return -3;
        }
        $sign = strtoupper(self::sign($wxdata,$pay_config['key']));
        if($wxdata['sign']!==$sign){
            return -4;
        }
        return !empty($wxdata['mweb_url'])?$wxdata['mweb_url']:-5;
    }

    /**
     * 获取uniondid
     * @param $openid
     * @return bool|mixed
     */
    public static function getUnionidByopenid($openid){
        if(empty($openid)){
            return false;
        }
        return parent::get_unionidByopenId($openid);
    }

    /**
     * 微信公众号号支付订单生成
     * @param $fee
     * @param $order_no
     * @param $body
     * @param $clientIp
     * @return array
     */
    public static function gzh_unifiedorder($fee,$order_no,$body,$clientIp,$openid){
        $pay_data=array();
        $pay_data['body']               = !empty($body) ? $body :"";
        $pay_data['openid']               = !empty($openid) ? $openid :"";
        $pay_data['spbill_create_ip']   = !empty($clientIp)?$clientIp:$_SERVER["REMOTE_ADDR"];
        $pay_data['total_fee']          = !empty($fee)  ? $fee*100:0;
        if($pay_data['total_fee'] < 1){
            return array();
        }
        $pay_data['out_trade_no']       = !empty($order_no)?$order_no:"";
        if(empty($pay_data['out_trade_no'])){
            return array();
        }

        $pay_config                     = Config::getField('weixin','gzh');
        $pay_data['appid']              = $pay_config['appid'];
        $pay_data['mch_id']             = $pay_config['mch_id'];
        $pay_data['nonce_str']          = substr(md5(mt_rand()),8,16);
        $pay_data['notify_url']         = $pay_config['notify_url'];
        $pay_data['trade_type']         = $pay_config['trade_type'];
        $pay_data['sign']               = self::sign($pay_data,$pay_config['key']);
        $data = self::xml_encode($pay_data);

        libxml_disable_entity_loader(true);
        $xml = self::postXmlCurl($data,$pay_config['order_url']);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $wxdata = json_decode(json_encode($xmlstring),true);
        if(empty($wxdata['sign'])){
            return $wxdata;
        }
        $sign = strtoupper(self::sign($wxdata,$pay_config['key']));
        if($wxdata['sign']!==$sign){
            return array();
        }

        return !empty($wxdata)?$wxdata:array();
    }

    /**
     * 微信APP支付订单生成
     * @param $fee
     * @param $order_no
     * @param $body
     * @param $clientIp
     * @param string $rj 软件
     * @return array|mixed|\stdClass
     * @throws \Exception
     */
    public static function app_unifiedorder($fee,$order_no,$body,$clientIp,$rj=""){
        $pay_data=array();
        $pay_data['body']               = !empty($body) ? $body :"";
        $pay_data['spbill_create_ip']   = !empty($clientIp)?$clientIp:$_SERVER["REMOTE_ADDR"];
        $pay_data['total_fee']          = !empty($fee)  ? $fee*100:0;
        if($pay_data['total_fee'] < 1){
            return array();
        }
        $pay_data['out_trade_no']       = !empty($order_no)?$order_no:"";
        if(empty($pay_data['out_trade_no'])){
            return array();
        }
        if($rj=="9438723"){
            $pay_config                     = Config::getField('weixin','app_pyq');
        }else{
            $pay_config                     = Config::getField('weixin','app');
        }

        $pay_data['appid']              = $pay_config['appid'];
        $pay_data['mch_id']             = $pay_config['mch_id'];
        $pay_data['nonce_str']          = substr(md5(mt_rand()),8,16);
        $pay_data['notify_url']         = $pay_config['notify_url'];
        $pay_data['trade_type']         = $pay_config['trade_type'];
        $pay_data['sign']               = self::sign($pay_data,$pay_config['key']);
        $data = self::xml_encode($pay_data);

        libxml_disable_entity_loader(true);
        $xml = self::postXmlCurl($data,$pay_config['order_url']);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $wxdata = json_decode(json_encode($xmlstring),true);
        if(empty($wxdata['sign'])){
            return $wxdata;
        }
        $sign = strtoupper(self::sign($wxdata,$pay_config['key']));
        if($wxdata['sign']!==$sign){
            return array();
        }

        return !empty($wxdata)?$wxdata:array();
    }

    //发送模版消息
    public static function sendtmsg($data){
        return parent::sendTplMsg($data);
    }

    /**
     * 提现到微信零钱
     * @param $fee
     * @param $order_no
     * @param $openid
     * @param $body
     * @param $clientIp
     * @return array|mixed|\stdClass
     * @throws \Exception
     */
    public static function TIXIAN($fee,$order_no,$openid,$body,$clientIp){
        $pay_data=array();
        $pay_data['desc']               = !empty($body) ? $body :"奖励";
        $pay_data['spbill_create_ip']   = !empty($clientIp)?$clientIp:$_SERVER["REMOTE_ADDR"];
        $pay_data['amount']          = !empty($fee)  ? $fee*100:0;
        $pay_data['openid']          = !empty($openid)  ? $openid:"";
        if(empty($pay_data['openid'])){
            return array();
        }
        if($pay_data['amount'] < 1){
            return array();
        }
        $pay_data['partner_trade_no']       = !empty($order_no)?$order_no:"";
        if(empty($pay_data['partner_trade_no'])){
            return array();
        }

        $pay_config                     = Config::getField('weixin','gzh');
        $pay_data['mch_appid']          = $pay_config['appid'];
        $pay_data['mchid']              = $pay_config['mch_id'];
        $pay_data['nonce_str']          = substr(md5(mt_rand()),8,16);
        $pay_data['check_name']         = "NO_CHECK";
        $pay_data['sign']               = self::sign($pay_data,$pay_config['key']);
        $data = self::xml_encode($pay_data);

        libxml_disable_entity_loader(true);
        $xml = self::postXmlCurl($data,"https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers",array(),true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $wxdata = json_decode(json_encode($xmlstring),true);
        if(empty($wxdata['sign'])){
            return $wxdata;
        }
        $sign = strtoupper(self::sign($wxdata,$pay_config['key']));
        if($wxdata['sign']!==$sign){
            return array();
        }

        return !empty($wxdata)?$wxdata:array();
    }


}