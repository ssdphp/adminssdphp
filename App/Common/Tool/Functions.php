<?php
namespace App\Common\Tool ;

use App\Admin\Model\Software;
use App\Admin\Model\Software_Version;
use App\Api\Model\Ditch;
use App\Api\Model\Douyin;
use SsdPHP\Core\Config;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\PushBaiduNew\PushSDK;
use SsdPHP\SsdPHP;

class Functions {
    /**
     * 获取图片地址
     * @param string $path
     * @return string
     */
    public static function getImgUrl($path=""){

        $HTTP_HOST=$_SERVER['HTTP_ORIGIN']??$_SERVER['HTTP_HOST']??"";
        $HTTP_HOST="vipvideo.api.tunnel.65141.com";
        if(empty($path)){
            return self::http_type().$HTTP_HOST."/static/img/404.jpg";
        }
        return self::http_type().$HTTP_HOST.$path;
    }

    public static function http_type(){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $http_type;
    }

    /**
     * 事件格式化，用于我看过谁
     * 秒前。
     * 分钟前
     * 1天前
     * 2天前
     * @param $time
     * @return array
     */
    public static function TimeToFormat($time,$ft="Y-m-d H:i")
    {

        $int = time() - $time;
        if ($int <= 2){
            $t = sprintf('刚刚', $int);
        }elseif ($int < 60){
            $t = sprintf('%d秒前', $int);
        }elseif ($int < 3600){
            $t = sprintf('%d分钟前', floor($int / 60));
        }elseif ($int < 86400){
            $t = sprintf('%d小时前', floor($int / 3600));
        }elseif ($int < 86400*2){
            $t = sprintf('%d天前', floor($int / 86400));
        }elseif ($int < 86400*3){
            $t = sprintf('%d天前', floor($int / 86400));
        }else{
            $t = date($ft,$time);
        }
        return $t;
    }

    //判断是不是今天，昨天
    public static function  TimeToday($time,$ft="Y-m-d H:i"){
        $start_time = strtotime(date('Y-m-d 00:00:00'));
        $end_time = strtotime(date('Y-m-d 23:59:59'));

        if($time >$start_time &&  $time<=$end_time){
            return "<i>今天</i>";
        }

        if($time < $start_time &&  $time>=$start_time-86399){
            return "<i>昨天</i>";
        }

        return date($ft,$time);


    }

    /**
     * ios内购检测
     * author xubiao
     * @param string $sandbox 是否沙盒
     * @param string $receipt_data 票据凭证
     * @return mixed | array
     */
    public static function acurl($receipt_data, $sandbox=0){

        //小票信息

        //$secret = "";    // APP固定密钥，在itunes中获取

        $POSTFIELDS = array("receipt-data" => $receipt_data);

        $POSTFIELDS = json_encode($POSTFIELDS);

        //正式购买地址 沙盒购买地址

        $url_buy     = "https://buy.itunes.apple.com/verifyReceipt";

        $url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";

        $url = $sandbox ? $url_sandbox : $url_buy;

        //简单的curl

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;

    }

    /**
     * 互粉宝定时器请求
     * @param $timerData
     * @return mixed
     */
    public static function hfb_set_timer($timerData){
        curl_setopt_array($ch = curl_init(), array(
            CURLOPT_URL => Config::getField('task','timer_url'),
            CURLOPT_POSTFIELDS => array("data"=>$timerData),
            CURLOPT_HTTPHEADER => array(
                "Content-Type=application/x-www-form-urlencoded"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 10,
        ));
        $httpres = curl_exec($ch);
        curl_close($ch);
        return $httpres;
    }

    /**
     * debug
     * @param $data
     * @param bool $isfile
     */
    public static function debug($data,$isfile = false){
        $date =date("Y-m-d H:i:s");
        if(is_array($data)){
            $data = print_r($data,true);
        }
        file_put_contents(SsdPHP::getRootPath()."/www/debug.txt","========={$date}=========\r\n".$data."\r\n\r\n",FILE_APPEND);
    }

    /**
     * 返回IP地址
     * @return array|false|string
     */
    public static function getIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }


    /**
     * 生成订单id
     * @return string
     */
    public static function OrderId($prefix="order"){
        return "$prefix".date("YmdHis").mt_rand(100000,999999);
    }

    /**
     * 给互粉宝签名
     * rj=273d7a9&os=1&did=3fb440e&device=MANUFACTURER%3A+Google%0AMODEL%3A+Pixel+3%0AVERSION.RELEASE%3A+9%0AVERSION.SDK_INT%3A+28&v=5&v_str=1.6&in_v=dd14319e9f73
     * @param array $data
     * @param string $checkmd5
     * @param string $v
     * @return bool
     */
    public static function CheckSign(array $data,$checkmd5="abc123......"){


        if(empty($data) || empty($data['sign'])){
            return false;
        }

        if(isset($data['sign'])){
            $sign =strtolower( $data['sign']);
            unset($data['sign']);
        }else{
            return false;
        }

        ksort($data);

        $str = md5($srr = http_build_query($data).($checkmd5));
        //echo print_r($data,true).print_r($_REQUEST,true).$str;
        return $sign===$str;
    }

    /**
     * 接口通用签名处理
     * @param $data
     * @return bool
     */
    public static function api_sign($data){


        //{{{版本作假升级
        $rj = Input::request('rj',"273d7a9");
        $did = Input::request('did',"3fb440e");
        $up_num = Input::request('in_v',"");
        $v = Input::request('v',"0");
        //系统
        $os = Input::request('os',0);
        

        if($os !=1 && $os!=2){

            return -1;
        }
	
	//安卓才加密
        if($os != 1){
            return 1;
        }

        //渠道id
        $Ditch = new Ditch();
        $info = $Ditch->findOne(array('did'=>$did,'rj'=>$rj,'os'=>$os));
        if(empty($info)){
            return -2;
        }
        
       
        if($v <=5){
            return -4;
        }
	 //安卓版本小于6的都要更新才可以用才加密
        if($up_num != $info['in_v'] && $info['checkup_in_v']==1){
            return -3;
        }

        return self::CheckSign($data,'fac9fa0a65c9b884d6a3b23850ed6733')?1:-500;

    }

    //签名加密
    public static function login_sign($str,$key="123456789..."){
        if($key == ""){
            $key = md5('123456789...');
        }

        return md5($str.$key);
    }


    //签名加密
    public static function check_login_sign($s1,$s2){

        return true;
        return $s1==$s2;
    }


    public static function opensslEncrypt($sStr, $sKey="zhedimarket!@#...", $method = 'AES-128-ECB'){
        $str = openssl_encrypt($sStr,$method,$sKey);

        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));

    }
    public static function opensslDecrypt($sStr, $sKey="zhedimarket!@#...", $method = 'AES-128-ECB'){

        $find = array('-', '_');
        $replace = array('+', '/');
        $sStr =  base64_decode(str_replace($find, $replace, $sStr));
        $str = openssl_decrypt($sStr,$method,$sKey);
        return $str;
    }

    public static function opensslEncryptNoBase64($sStr, $sKey="xinmaicard123......", $method = 'AES-128-ECB'){
        
	    return $str = openssl_encrypt($sStr,$method,$sKey);
    }
    public static function opensslDecryptNoBase64($sStr, $sKey="xinmaicard123......", $method = 'AES-128-ECB'){
        
	    $str = openssl_decrypt($sStr,$method,$sKey);
        return $str;
    }
    /**
     * 生成随机唯一id
     * @param int $lenght 长度
     * @param bool $is_upper 是否大写默认否
     * @return string
     */
    public static function uniqidReal($lenght = 16,$is_upper=false) {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            return false;
        }
        $v = substr(bin2hex($bytes), 0, $lenght);
        return $is_upper?strtoupper($v):$v;
    }

    /**
     * 当前域名
     * @author hteen
     * static
     * @return string
     */
    public static function baseUrl(){

        $HTTP_HOST = $_SERVER['HTTP_HOST'];
        $config = \SsdPHP\Core\Config::getField('config','http_host');
        if ($config)
            $HTTP_HOST = $config;

        return self::http_type().$HTTP_HOST;
    }

    /**
     * author xiaohuihui <xzh_tx@163.com>
     * @param $url 请求的url
     * @param $option 发送的字段
     * @param int|array $header 发送的头信息
     * @param string $type 请求方法，默认post
     * @return mixed | array
     */
    public static function RequestCurlPay(string $url, $options=null, $header = [], $type = 'GET') {

        $curl = curl_init (); // 启动一个CURL会话
        curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
        if (! empty ( $options )) {

            curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
        }
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 15 ); // 设置超时限制防止死循环
        curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
        curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, strtoupper($type) );
        $result = curl_exec ( $curl ); // 执行操作
        $httpCode=curl_getinfo($curl);
        curl_close ( $curl ); // 关闭CURL会话

        return [
            'httpinfo'=>$httpCode,
            'ret'=>$result
        ];
    }

    //身份标识
    public static function VIP_RAND_STR($rank=1){

        $strary = array(
            '0'=>'免费会员',
            '1'=>'VIP会员',
            '2'=>'代理商',
        );
        return $strary[$rank] ?? "免费会员";
    }

    /**
     * 获取是不是代理商或者vip
     * @param int $vip_expire_time
     * @param int $vip_rank
     * @return int
     */
    public static function getVipRank($vip_expire_time,$vip_rank=0){

        $a = $vip_expire_time-time();
        //$puidinfo['vip_rank']=2;
        if($vip_rank == 2){
            //是代理商就是永久就是10年以上
            //如果代理商有日期限制，就是$a
            if( $a<1){
                $vip_rank = 0;
            }
        }else{

            if( $a<1){
                $vip_rank = 0;
            }else{
                $vip_rank = 1;
            }
        }

        return $vip_rank;
    }

    //短网址
    public static function code62($x){
        $show='';
        while($x>0){
            $s=$x % 62;
            if ($s>35){
                $s=chr($s+61);
            }elseif($s>9&&$s<=35){
                $s=chr($s+55);
            }
            $show.=$s;
            $x=floor($x/62);
        }
        return $show;
    }

    //生成短网址
    public static function shorturl($url){
        $url=crc32($url);
        $result=sprintf("%u",$url);
        return self::code62($result);
    }






    /**
     * author xiaohuihui <xzh_tx@163.com>
     * @param $url 请求的url
     * @param $option 发送的字段
     * @param int|array $header 发送的头信息
     * @param string $type 请求方法，默认post
     * @return mixed | array
     */
    public static function PHPCURL($url, $options=null, $header = [], $type = 'GET',$config=array()) {

        $curl = curl_init (); // 启动一个CURL会话
        curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
        if (! empty ( $options )) {

            curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
        }
        if(isset($config['CURLOPT_HEADER']) && $config['CURLOPT_HEADER'] == true){
            //返回响应头
            curl_setopt($curl, CURLOPT_HEADER, $config['CURLOPT_HEADER']);
        }
        if(isset($config['CURLOPT_NOBODY']) && $config['CURLOPT_NOBODY'] == true){
            // 是否不需要响应的正文,为了节省带宽及时间,在只需要响应头的情况下可以不要正文
            curl_setopt($curl, CURLOPT_NOBODY, true);
        }
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 15 ); // 设置超时限制防止死循环
        curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, TRUE ); // 自动301
        curl_setopt ( $curl, CURLOPT_MAXREDIRS, 3 ); // 自动301递归3次
        curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
        curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, strtoupper($type) );
        $result = curl_exec ( $curl ); // 执行操作
        $httpCode=curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close ( $curl ); // 关闭CURL会话

        return array(
            'httpcode'=>$httpCode,
            'body'=>$result,
            'response'=>$result,
        );
    }
    public static $init_gifshow_map = array(
        2=>array(//快手双击
            'app'=>'gifshow',
            'type'=>'detail',
            'init_feild'=>'like_count',
        ),
        4=>array(//快手播放
            'app'=>'gifshow',
            'type'=>'detail',
            'init_feild'=>'view_count',
        ),
        3=>array(//快手评论
            'app'=>'gifshow',
            'type'=>'detail',
            'init_feild'=>'comment_count',
        ),
        1=>array(//快手粉丝
            'app'=>'gifshow',
            'type'=>'userinfo',
            'init_feild'=>'fans',
        )
    );
    public static $init_douyin_map = array(

        5=>array(//抖音粉丝
            'app'=>'douyin',
            'type'=>'userinfo',
            'init_feild'=>'fans',
        ),
        6=>array(//抖音粉丝
            'app'=>'douyin',
            'type'=>'detail',
            'init_feild'=>'like_count',
        )
    );

    /**
     * 异常错误通知
     * @param $title
     * @param $content
     * @param string $key
     * @return int
     */
    public static function error_push($content,$unread=0,$sound="sound.wav",$debug="no"){
        if (empty($content)){
            return 0;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => "9002",
            CURLOPT_URL => "http://127.0.0.1:9002/push",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "body={$content}&debug={$debug}&unread={$unread}&sound={$sound}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        return 1;
    }

    /**
     * 获取快手抖音初始量和协议
     * @param $product_id
     * @param $user_id
     * @param $user_pid
     * @return  mixed
     */
    public static function GetInitNum($product_id,$user_id="",$user_pid=""){
        if(array_key_exists($product_id, self::$init_gifshow_map)){

            $cq = self::$init_gifshow_map[$product_id];
            $url = Config::getField('task','init_url');
            //$url = "http://127.0.0.1:7011/get";
            $httpres = Functions::PHPCURL($url, $q=array(
                "app"=>$cq['app']."",
                "userid"=>$user_id."",
                "photoid"=>$user_pid."",
                "type"=>$cq['type']."",
            ), [], 'post');
            if($httpres['httpcode']!=200){
               return -404;
            }
            $httpres = $httpres['body'];
            if(!$httpres){
                return -1;
            }
            $httpresData = json_decode($httpres,true);
            if(empty($httpresData)){
                return -2;
            }

            $uid = $httpresData['photos'][0]['user_id']??$user_id;
            $pid = $httpresData['photos'][0]['photo_id']??$user_pid;
            $photo_protocol= "kwai://work/{$pid}";
            switch ($cq['init_feild']){
                case "fans":
                    if(!is_numeric($user_id)){
                        $httpres = Functions::PHPCURL($url, array(
                            "app"=>$cq['app']."",
                            "userid"=>$user_id."",
                            "photoid"=>$user_pid."",
                            "type"=>"detail",
                        ), [], 'post');
                        if($httpres['httpcode']!=200){
                            return -404;
                        }
                        $httpres = $httpres['body'];
                        if(!$httpres){
                            return -3;
                        }
                        $httpresData = json_decode($httpres,true);
                        if(empty($httpresData)){
                            return -4;
                        }
                        if(!empty($httpresData['code']) && $httpresData['code']==0){
                            return -5;
                        }
                        $uid = $httpresData['photos'][0]['user_id']??false;
                        $pid = $httpresData['photos'][0]['photo_id']??false;
                        if(is_numeric($uid)){
                            return self::GetInitNum($product_id,$uid,$pid);
                        }
                    }
                    $photo_protocol= "kwai://profile/{$uid}";
                    $result = $httpresData['result'] == 1?$httpresData:-1;
                    //埋点异常监控
                    if( !isset($httpresData['userProfile']['ownerCount']['fan'])){
                        self::error_push("获取快手粉丝数据异常","获取快手粉丝数据异常，请检查，内网frp网络，和接口抓取数据结果{$user_id},{$user_pid}\r\n{$photo_protocol}\r\n{$httpres}");
                    }

                    $init_num = $httpresData['userProfile']['ownerCount']['fan']??-1;
                    $follow = $httpresData['userProfile']['ownerCount']['follow']??0;

                    break;
                case "like_count":
                    $init_num = $httpresData['photos'][0]['like_count']??-1;
                    $result = $httpresData['result'] == 1?$httpresData:-1;
                    //埋点异常监控
                    if( !isset($httpresData['photos'][0]['like_count'])){

                        self::error_push("获取快手双击数据异常","获取快手双击数据异常，请检查，内网frp网络，和接口抓取数据结果{$user_id},{$user_pid}\r\n{$httpres}");

                    }
                    break;
                case "view_count":
                    $init_num = $httpresData['photos'][0]['view_count']??-1;
                    $result = $httpresData['result'] == 1?$httpresData:-1;
                    //埋点异常监控
                    if( !isset($httpresData['photos'][0]['view_count'])){

                        self::error_push("获取快手播放数据异常","获取快手播放数据异常，请检查，内网frp网络，和接口抓取数据结果{$user_id},{$user_pid}\r\n{$httpres}");

                    }
                    break;
                case "comment_count":
                    $init_num = $httpresData['photos'][0]['comment_count']??-1;
                    $result = $httpresData['result'] == 1?$httpresData:-1;
                    //埋点异常监控
                    if( !isset($httpresData['photos'][0]['comment_count'])){

                        self::error_push("获取快手评论数据异常461","获取快手评论数据异常，请检查，内网frp网络，和接口抓取数据结果{$user_id},{$user_pid}\r\n{$httpres}");

                    }
                    break;
            }
            $i = $init_num??-1;
            return array(
                "init_num"=>$i,
                "photo_protocol"=>$photo_protocol,
                "follow"=>$follow??-1,
                "user_id"=>$uid,
                "user_pid"=>$pid,
                "result"=>$result===-1?-1:$result,
            );
        }

        if(array_key_exists($product_id, self::$init_douyin_map)){

            $cq = self::$init_douyin_map[$product_id];
            $url = Config::getField('task','init_url');
            //$url = "http://127.0.0.1:7011/get";
            $httpres = Functions::PHPCURL($url, array(
                "app"=>$cq['app']."",
                "userid"=>$user_id."",
                "photoid"=>$user_pid."",
                "type"=>$cq['type']."",
            ), [], 'post');
            if($httpres['httpcode']!=200){
                return -404;
            }
            $httpres = $httpres['body'];
            if(!$httpres){
                return -1;
            }
            $httpresData = json_decode($httpres,true);
            if(empty($httpresData)){
                return -2;
            }

            $uid = $user_id;
            $pid = $user_pid;
            $photo_protocol= "snssdk1128://aweme/detail/{$pid}?refer=web";
            switch ($cq['init_feild']){
                case "fans":
                    $result = !empty($httpresData['user'])?$httpresData:-1;
                    $init_num = $httpresData['user']['follower_count']??0;
                    $follow = $httpresData['user']['following_count']??0;
                    $photo_protocol= "snssdk1128://user/profile/{$uid}";
                    //埋点异常监控
                    if( !isset($httpresData['user'])){

                        //self::error_push("获取抖音粉丝数据异常","获取抖音粉丝数据异常，请检查，内网frp网络，和接口抓取数据结果\r\n{$httpres}");

                    }
                    break;
                case "like_count":
                    $init_num = $httpresData['aweme_detail']['statistics']['digg_count']??-1;
                    $result = !empty($httpresData['aweme_detail'])?$httpresData:-1;
                    //埋点异常监控
                    if( !isset($httpresData['aweme_detail']['statistics']['digg_count'])){

                        //self::error_push("获取抖音双击数据异常","获取抖音双击数据异常，请检查，内网frp网络，和接口抓取数据结果\r\n{$httpres}");

                    }
                    break;
                case "view_count":
                    $init_num = $httpresData['aweme_detail']['statistics']['play_count']??-1;
                    $result = !empty($httpresData['aweme_detail'])?$httpresData:-1;
                    break;
                case "comment_count":
                    $init_num = $httpresData['aweme_detail']['statistics']['comment_count']??-1;
                    $result = !empty($httpresData['aweme_detail'])?$httpresData:-1;
                    break;
            }
            $i = $init_num??-1;
            return array(
                "init_num"=>$i,
                "photo_protocol"=>$photo_protocol,
                "follow"=>$follow??-1,
                "user_id"=>$uid,
                "user_pid"=>$pid,
                "result"=>$result===-1?-1:$result,
            );
        }

        return array(
            "init_num"=>0,
            "follow"=>$follow??-1,
            "user_id"=>false,
            "user_pid"=>false,
            "result"=>-1
        );

    }


    /**
     * 验证输入的邮件地址是否合法
     * @access  public
     * @param   string      $email      需要验证的邮件地址
     * @return bool
     */
    public static function is_email($email)
    {
        $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (strpos($email, '@') !== false && strpos($email, '.') !== false)
        {
            if (preg_match($chars, $email))
            {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * 验证输入的手机号码是否合法
     * @access public
     * @param string $mobile_phone
     * 需要验证的手机号码
     * @return bool
     */
    public static function is_mobile_phone ($mobile_phone)
    {
        if(!preg_match('/^(?=\d{11}$)^1(?:3\d|4[57]|5[^4\D]|66|7[^01249\D]|8\d|9[891])\d{8}$/',$mobile_phone)){
            return false;
        }
        return true;
    }

    //打码手机号码
    public static function setPhone($phone,$start=3,$end=6){
        $phone .="";
        if($start<1){
            $start = 1;
        }
        if(!preg_match("/\w{20}/",$phone)){
            return $phone;
        }
        $count = strlen($phone);
        if($count>=6){
            $start-=1;

            for ($i=1;$i<=$end;$i++){
                if(isset($phone[$start+$i]))
                    $phone[$start+$i] = "*";
            }
        }

        return $phone;
    }
}
?>