<?php

namespace App\Common\Wxsdk\Adaptor;

use Qiniu\Processing\PersistentFop;
use SsdPHP\Cache\Cache;
use SsdPHP\Core\Config;
use SsdPHP\SsdPHP;

use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
use function Qiniu\base64_urlSafeDecode;
use function Qiniu\base64_urlSafeEncode;

class Weixin{



    /**
     * 微信网页开发，获取微信code
     * @param string $scope snsapi_userinfo|snsapi_base
     * @param string $redirect_uri 回调url有code的情况
     * @param array $wxconfig 微信公众号配置
     * @return bool|string
     */
    protected static function gzh_getcode_url($scope="snsapi_userinfo",$redirect_uri){
        $wxconfig = Config::getField('weixin','gzh');
        if(empty($redirect_uri)){
            return false;
        }
        if(empty($wxconfig)){
            return false;
        }
        if($scope!='snsapi_base' && $scope!='snsapi_userinfo'){
            return false;
        }
        if($scope == "snsapi_base"){
            $wx_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$wxconfig['appid']}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=123#wechat_redirect";

        }else{
            $wx_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$wxconfig['appid']}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=STATE#wechat_redirect";
        }

        return $wx_url;

    }
    /**
     * 请求微信access_token
     * @param $code
     * @param array $config ::Config::getField('weixin','gzh');
     * @return bool|mixed
     */
    protected static function access_token($code,$config=array()){


        if(empty($code) || empty($config) ){
            return false;
        }
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$config['appid']}&secret={$config['appSecret']}&code={$code}&grant_type=authorization_code";

        return self::getXmlCurl($url);
    }

    /**
     * 获取微信共账号的access_token
     * @param $config
     * @return bool|mixed
     */
    private static function gzh_access_ticket($token){
        if( empty($token) ){
            return "";
        }
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi";

        return self::getXmlCurl($url);
    }

    /**
     * 下载语音
     * @param $id
     * @param $mid
     * @param string $is_gq
     * @return bool|string
     * @throws \Exception
     */
    protected static function down_gzh_voic_media($id,$mid,$is_gq='yes'){
        $token = self::get_gzh_access_token();
        $zz =md5($mid);
        $z="";
        if($is_gq == 'yes'){
            $t = "speex";
            $url ="https://api.weixin.qq.com/cgi-bin/media/get/jssdk?access_token={$token}&media_id={$mid}";
        }else{
            $t = "amr";
            $z = "?attname=$zz.mp3";
            $url ="https://api.weixin.qq.com/cgi-bin/media/get?access_token={$token}&media_id={$mid}";
        }
        $data  = self::getXmlCurl($url,[],false,30,true);

        $upManager = new UploadManager();

        $QiniuConfig = Config::getField('upload','qiniu');
        $auth = new Auth($QiniuConfig['ak'], $QiniuConfig['sk']);
        $zzz = urlencode(base64_urlSafeEncode("{$QiniuConfig['bucket']}:$zz"));
        $fops = "avthumb/mp3/ab/192k|saveas/{$zzz}";
        if($is_gq=='yes'){
            $fops="avthumb/mp3/speex/1|saveas/{$zzz}";
        }
        $pipeline='yuyin2mp3';  //自己创建的私有队列 速度更快
        $notifyUrl = Config::getField('config','gzh_host_url').'/index/notify2mp3?id='.$id;
        $policy = array(   //使用私有队列  公有队列速度太慢
            'persistentOps' => $fops,
            'persistentPipeline' => $pipeline,
            'persistentNotifyUrl' => $notifyUrl,
            'forceSaveKey' => true,
            'saveKey' => $zz,
            'scope' => 'qiniu-ts-demo:sample.'.$t,

        );
        $token = $auth->uploadToken($QiniuConfig['bucket'],null, 3600, $policy);
        list($ret, $error) = $upManager->put($token, $zz, $data['data']);
        if($error!=null){
            return false;
        }

        if(!empty($ret['hash'])){
            return ($QiniuConfig['domain']."/".$ret['key']??$ret['hash']);
        }
        return false;
    }

    /**
     * 获取微信公众号access_token
     * @param null $config ['appid','secret']
     * @return mixed|string
     * @throws \Exception
     */
    protected static function get_gzh_access_token($config=null){

        if ($config==null) {

            $config = Config::getField('weixin','gzh');
        }
        $Cache = Cache::getInstance();
        $token_key = 'wxgzh:token:'.$config['appid'];
        $token = $Cache->get($token_key);
        if(!empty($token)){
            return $token;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$config['appid']}&secret={$config['secret']}";
        $token = self::getXmlCurl($url);
        if(empty($token)){
            return "";
        }

        $as = json_decode($token,true);
        if(empty($as['access_token'])){
            return "";
        }
        $Cache->SETEX($token_key,$as['expires_in']-30,$as['access_token']);
        return $as['access_token'];
    }


    protected static function gzh_set_menu($config,$menu){
        if(empty($menu)) return false;
        if(empty($config)) return false;
        $ACCESS_TOKEN = self::get_gzh_access_token($config);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$ACCESS_TOKEN}";
        return self::postXmlCurl($menu,$url,array("Content-Type"=>'application/json'));
    }

    /**
     * 通过openid获取唯一id
     */
    protected static function get_unionidByopenId($openid){

        $token = self::get_gzh_access_token();

        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$openid}&lang=zh_CN";

        return self::getXmlCurl($url);
    }




    //获取微信公众号access_token
    protected static function get_gzh_access_ticket(){

        $Cache = Cache::getInstance();
        $ticket = $Cache->get('XINMAIMCARD:GZH:TICKET');
        if(!empty($ticket)){
            return $ticket;
        }
        $token = self::get_gzh_access_token();
        $ticketData = self::gzh_access_ticket($token);



        if(empty($ticketData)){
            return "";
        }

        $ticketData = json_decode($ticketData,true);
        if(empty($ticketData['ticket'])){
            return "";
        }
        $Cache->SETEX('XINMAIMCARD:GZH:TICKET',$ticketData['expires_in']-30,$ticketData['ticket']);
        return $ticketData['ticket'];
    }

    /**
     * 发送模版消息
     * @param array $data
     * @return bool|mixed
     */
    public static function sendTplMsg($body){
        if(empty($body)) return false;
        $ACCESS_TOKEN = self::get_gzh_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$ACCESS_TOKEN}";
        return self::postXmlCurl($body,$url,array("Content-Type"=>'application/json'));
    }

    /**
     * 发送客服消息
     * @return bool|mixed
     */
    public static function sendKFmsg($body){
        if(empty($body)) return false;
        $ACCESS_TOKEN = self::get_gzh_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$ACCESS_TOKEN}";
        $a = self::postXmlCurl($body,$url,array("Content-Type"=>'application/json'));
        return $a;
    }

    /**
     * 签名ticket
     * @param array $data
     * @return string
     */
    public static function signTicket(&$data=array()){
        $data['noncestr'] = uniqid();
        $data['timestamp'] = time();

        $data['jsapi_ticket'] = self::get_gzh_access_ticket();
        $url = Config::getField('config','gzh_host_url');
        $data['url'] = $url.$_SERVER['REQUEST_URI'];
        ksort($data);
        $s = urldecode(http_build_query($data));
        $a = sha1($s);
        $data['appid']=Config::getField('weixin','gzh')['appid'];
        $data['signature'] = $a;
        return $a;
    }

    /**
     * 新版签名ticket
     * @return array
     */
    public static function newSignTicket(){
        $data=array();
        $data['noncestr'] = uniqid();
        $data['timestamp'] = time();

        $data['jsapi_ticket'] = self::get_gzh_access_ticket();
        $url = Config::getField('config','gzh_host_url');
        $data['url'] = $url.$_SERVER['REQUEST_URI'];
        ksort($data);
        $s = urldecode(http_build_query($data));
        $a = sha1($s);
        $data['appid']=Config::getField('weixin','gzh')['appid'];
        $data['signature'] = $a;
        return $data;
    }

    /**
     * 公众号支付签名数据
     * @param array $_data
     * @return array
     */
    public static function GzhPaySign($_data = array(),$key=""){
        $signType = $_data['signType'];
        $data = array();
        if(!empty($_data) && is_array($_data)){
            $data +=$_data;
        }
        ksort($data);
        $s = urldecode(http_build_query($data));
        $a = $signType($s."&key={$key}");
        $data['paySign']=strtoupper($a);
        return $data;
    }

    /**
     * 获取临时微信公众号二维码
     * @author xzh
     * @param $uid
     * @param int $expire_time 有效期7天
     * @return bool|mixed
     */
    public static function getTempQrcode($uid,$expire_time=604800){

        $redis = Cache::getInstance();
        $key = Config::getField('config','GZH_QRCODE_CACHE').$uid;
        if($code = $redis->get($key)){
            return $code;
        }
        $token = self::get_gzh_access_token();

        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$token;


        $json = json_encode(array(
            'expire_seconds'=>$expire_time,
            'action_name'=>'QR_STR_SCENE',
            'action_info'=>array(
                'scene'=>array(
                    'scene_str'=>$uid
                )
            ),
        ));
        //$data = self::postXmlCurl("{\"expire_seconds\": {$expire_time}, \"action_name\": \"QR_STR_SCENE\", \"action_info\": {\"scene\": {\"scene_str\": {$uid}}}",$url);
        $data = self::postXmlCurl($json,$url);

        if(empty($data)){
            return "";
        }
        $data = json_decode($data,true);
        if(empty($data)){
            return "";
        }
        if(!empty($data['url'])){
            $redis->set($key,$data['url']);
            $redis->expire($key,120);
        }
        return $data['url']??"";

    }

    /**
     * 获取微信用户信息
     * @param string $assess_token
     * @param string $openid
     * @return bool|mixed
     */
    protected static function getuserinfo($assess_token,$openid){


        if(empty($assess_token) || empty($openid) ){
            return false;
        }
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$assess_token}&openid={$openid}&lang=zh_CN";

        return self::getXmlCurl($url);
    }
    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $encoding 数据编码
     * @param string $root 根节点名
     * @return string
     */
    protected static function xml_encode($data, $encoding='utf-8', $root='xml',$isheader=false) {
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


    protected static function wxupdatecurl($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            foreach ($data as &$val) {
                if(is_file($val)){
                    $val = new \CURLFile($val);
                }
            }
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


    public static function postXmlCurl($xml, $url,$header=array(), $useCert = false, $second = 30)
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
            $dir = SsdPHP::getAppDir()."/resources/cert/";
            //使用证书：cert 与 key 分别属于两个.pem文件
            $s = curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            $s = curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');

            $s = curl_setopt($ch, CURLOPT_SSLCERT, $dir.'apiclient_cert.pem');
            $s = curl_setopt($ch, CURLOPT_SSLKEY, $dir.'apiclient_key.pem');
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

    /**
     * 请求微信执行CURL
     * @param $url
     * @param array $header
     * @param bool $useCert
     * @param int $second
     * @return bool|mixed
     */
    protected static function getXmlCurl($url,$header=array(), $useCert = false, $second = 30,$return_header=false)
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
        }
        if($return_header){
            curl_setopt($ch, CURLOPT_HEADER, true);
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
        //运行curl
        $data = curl_exec($ch);
        if($return_header){
            $headerSize = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
            $heder_info = substr($data, 0, $headerSize);
            $data = substr($data, $headerSize, -1);
        }

        //返回结果
        if ($data) {

            curl_close($ch);
            if($return_header){
                return array(
                    'data'=>$data,
                    'header'=>$heder_info
                );
            }
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }


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
}