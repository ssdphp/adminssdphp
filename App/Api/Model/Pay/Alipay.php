<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/18
 * Time: 17:50
 */
namespace App\Api\Model\Pay;

use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
class   Alipay {


    /**
     * RSA单独签名方法，未做字符串处理,字符串处理见getSignContent()
     * @param $data 待签名字符串
     * @param $privatekey 商户私钥，根据keyfromfile来判断是读取字符串还是读取文件，false:填写私钥字符串去回车和空格 true:填写私钥文件路径
     * @param $signType 签名方式，RSA:SHA1     RSA2:SHA256
     * @param $keyfromfile 私钥获取方式，读取字符串还是读文件
     * @return string
     * @author mengyu.wh
     */
    public static function alonersaSign(string $data,$privatekey,$signType = "RSA2",$keyfromfile=false) {
        if(!$keyfromfile){
            $priKey=$privatekey;
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($priKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        }
        else{
            $priKey = file_get_contents($privatekey);
            $res = openssl_get_privatekey($priKey);
        }

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        if($keyfromfile){
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }
    public static function verify($data, $sign,$publicKey, $signType = 'RSA') {

        $pubKey= $publicKey;
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值

        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }

        return $result;
    }

    /**
     * H5支付统一下单微信统一下单接口
     * @param float $fee 金额，以人类正常理解的面值操作。
     * @param string $order_no 订单号，商户自定义生成的订单号码
     * @param string $body 商品说明描述
     * @param string $clientIp 客户端ip
     * @return int|string
     */
    public static function Ali_H5_unifiedorder($fee,$order_no,$body,$pay_config){
        $pay_data=array();
        if(empty($pay_config))
            return false;
        $pay_data['app_id']             = $pay_config['Appid'];
        $pay_data['method']             = $pay_config['method'];
        $pay_data['return_url']         = $pay_config['return_url'];
        $pay_data['notify_url']         = $pay_config['notify_url'];
        $pay_data['charset']            = $pay_config['charset'];
        $pay_data['sign_type']          = $pay_config['sign_type'];
        $pay_data['timestamp']          = date("Y-m-d H:i:s");
        $pay_data['version']            = $pay_config['version'];
        $pay_data['biz_content']        = json_encode(array(
            "subject"=>"$body",
            "out_trade_no"=>"$order_no",
            "total_amount"=>"$fee",
            "product_code"=>"QUICK_WAP_WAY",
        ));
        ksort($pay_data);
        $data=urldecode(http_build_query($pay_data));

        $sign               = self::alonersaSign($data,$pay_config['privateKey']);
        $pay_data['sign']=$sign;
        foreach ($pay_data as &$v){
            $v=urlencode($v);
        }
        $data = urldecode(http_build_query($pay_data));

        $ss = $pay_config['Geteway']."?".$data;
        return  $ss;
    }
    /**
     * H5支付统一下单微信统一下单接口
     * @param float $fee 金额，以人类正常理解的面值操作。
     * @param string $order_no 订单号，商户自定义生成的订单号码
     * @param string $body 商品说明描述
     * @param string $clientIp 客户端ip
     * @return int|string
     */
    public static function Ali_App_unifiedorder($fee,$order_no,$body,$pay_config){
        $pay_data=array();
        if(empty($pay_config))
            return false;
        $pay_data['app_id']             = $pay_config['Appid'];
        $pay_data['method']             = $pay_config['method'];
        //$pay_data['return_url']         = $pay_config['return_url'];
        $pay_data['notify_url']         = $pay_config['notify_url'];
        $pay_data['charset']            = $pay_config['charset'];
        $pay_data['sign_type']          = $pay_config['sign_type'];
        $pay_data['timestamp']          = date("Y-m-d H:i:s");
        $pay_data['version']            = $pay_config['version'];
        $pay_data['biz_content']        = json_encode(array(
            "subject"=>"$body",
            "out_trade_no"=>"$order_no",
            "total_amount"=>"$fee",
            "product_code"=>"QUICK_MSECURITY_PAY",
        ));
        ksort($pay_data);
        $data=urldecode(http_build_query($pay_data));
        //echo $data;

        $sign               = self::alonersaSign($data,$pay_config['privateKey']);
        $pay_data['sign']=$sign;
        foreach ($pay_data as &$v){
            $v=urlencode($v);
        }
        $data = urldecode(http_build_query($pay_data));

        $ss = $data;
        return  $ss;
    }
    /**
     * H5支付统一下单微信统一下单接口
     * @param float $fee 金额，以人类正常理解的面值操作。
     * @param string $order_no 订单号，商户自定义生成的订单号码
     * @param string $body 商品说明描述
     * @param string $clientIp 客户端ip
     * @return int|string
     */
    public static function Ali_PC_unifiedorder($fee,$order_no,$body,$pay_config){
        $pay_data=array();
        if(empty($pay_config))
            return false;
        $pay_data['app_id']             = $pay_config['Appid'];
        $pay_data['method']             = "alipay.trade.page.pay";
        $pay_data['return_url']         = $pay_config['return_url'];
        $pay_data['notify_url']         = $pay_config['notify_url'];
        $pay_data['charset']            = $pay_config['charset'];
        $pay_data['sign_type']          = $pay_config['sign_type'];
        $pay_data['timestamp']          = date("Y-m-d H:i:s");
        $pay_data['version']            = $pay_config['version'];
        $pay_data['biz_content']        = json_encode(array(
            "subject"=>"充值订单号：{$order_no}",
            "out_trade_no"=>$order_no,
            "total_amount"=>$fee,
            "product_code"=>"FAST_INSTANT_TRADE_PAY",
        ));
        ksort($pay_data);
        $data=urldecode(http_build_query($pay_data));

        $sign               = self::alonersaSign($data,$pay_config['privateKey']);
        $pay_data['sign']=$sign;
        foreach ($pay_data as &$v){
            $v=urlencode($v);
        }
        $data = urldecode(http_build_query($pay_data));

        $ss = $pay_config['Geteway']."?".$data;
        return  $ss;
    }
    /**
     * H5支付统一下单微信统一下单接口
     * @param float $fee 金额，以人类正常理解的面值操作。
     * @param string $order_no 订单号，商户自定义生成的订单号码
     * @param string $body 商品说明描述
     * @param string $clientIp 客户端ip
     * @return int|string
     */
    public static function Ali_Tixian_unifiedorder($data,$tixian,$pay_config){
        $pay_data=array();
        if(empty($pay_config))
            return false;
        $pay_data['app_id']             = $pay_config['Appid'];
        $pay_data['method']             = "alipay.fund.trans.toaccount.transfer";
        $pay_data['format']             = 'JSON';
        $pay_data['charset']            = $pay_config['charset'];
        $pay_data['sign_type']          = $pay_config['sign_type'];
        $pay_data['timestamp']          = date("Y-m-d H:i:s");
        $pay_data['version']            = $pay_config['version'];
        $pay_data['biz_content']        = json_encode(array(
            "out_biz_no"=>$data['order_id'],
            "payee_type"=>"ALIPAY_LOGONID",
            "payee_account"=>$tixian['payee_account'],
            "amount"=>$data['pay_amt'],
            "payer_show_name"=>$pay_config['tx_payer'],
            "payee_real_name"=>$tixian['real_name'],
            "remark"=>$pay_config['tx_payer'],
        ));
        ksort($pay_data);
        $data=urldecode(http_build_query($pay_data));

        $sign               = self::alonersaSign($data,$pay_config['privateKey']);
        $pay_data['sign']=$sign;
        foreach ($pay_data as &$v){
            $v=urlencode($v);
        }
        $data = urldecode(http_build_query($pay_data));

        $ss = $pay_config['Geteway']."?".$data;

        $http = Functions::RequestCurlPay($ss);
        if($http['httpinfo']['http_code']!=200){
            return false;
        }
        return  $http['ret']??false;
    }


}