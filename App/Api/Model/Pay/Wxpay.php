<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/18
 * Time: 17:50
 */
namespace App\Api\Model\Pay;;

use SsdPHP\Core\Config;
class Wxpay {


    /**
     * 微信支付得时候。生产签名
     * @param array $data
     * @param string $key
     * @return string
     */
    public static function sign(array $data, $key=""){
        if(empty($data)){
            return "";
        }
        if(empty($key)){
            return "";
        }
        if(isset($data['sign'])){
            unset($data['sign']);
        }
        ksort($data);
        $str = urldecode(http_build_query($data))."&key={$key}";
        return md5($str);
    }

    /**
     * H5支付统一下单微信统一下单接口
     * @param float $fee 金额，以人类正常理解的面值操作。
     * @param string $order_no 订单号，商户自定义生成的订单号码
     * @param string $body 商品说明描述
     * @param string $clientIp 客户端ip
     * @return int|string
     */
    public static function H5_unifiedorder($fee,$order_no,$body,$clientIp,$pay_config){
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

        if(empty($pay_config)){
            $pay_config                     = Config::getField('Weixin','H5Pay');
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
            return -3;
        }
        $sign = strtoupper(self::sign($wxdata,$pay_config['key']));
        if($wxdata['sign']!==$sign){
            return -4;
        }
        return !empty($wxdata['mweb_url'])?$wxdata['mweb_url']:-5;
    }


    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $encoding 数据编码
     * @param string $root 根节点名
     * @return string
     */
    public static function xml_encode($data, $encoding='utf-8', $root='xml',$isheader=false) {
        $xml    = $isheader?'<?xml version="1.0" encoding="' . $encoding . '"?>':'';
        $xml   .= '<' . $root . '>';
        $xml   .= self::data_to_xml($data);
        $xml   .= '</' . $root . '>';
        return $xml;
    }

    /**
     * 数据toXML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml    .=  "<$key>";
            $xml    .=  ( is_array($val) || is_object($val)) ? self::data_to_xml($val) : $val;
            list($key, ) = explode(' ', $key);
            $xml    .=  "</$key>";
        }
        return $xml;
    }

    protected static function postXmlCurl($xml, $url,$header=array(), $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //设置header
        if($header){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }else{
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
        }
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            //curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            //curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }
}