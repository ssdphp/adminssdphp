<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;

use App\Api\Model\Pay\Alipay;
use App\Api\Model\Pay\Wxpay;
use SsdPHP\Core\Config as Aconfig;

class Pay extends Model {


    /**
     * 订单通知
     * @param int $page
     * @param int $pagesize
     */
    public function getNotice($cond=array('pay_status'=>1),$page=1,$pagesize=10,$field=array(
        'uid','p_title','pay_amt','p_price',
    )){

        $a = $this->setPage($page,$pagesize)
            ->select($cond,$field,"","id desc");

        return $a;
    }

    /**
     * 通过订单id更新支付完成状态
     * 1.更新订单,更新用户状态
     * @param $oid
     * @param $data
     */
    public function update_pay_status($oid,$data){
        $Vip_Order = new Vip_Order();
        $orderInfo = $Vip_Order->findone(array('order_id'=>$oid));
	    if(empty($orderInfo['order_id'])){
            return false;
        }
        if(!empty($orderInfo['pay_status']) && $orderInfo['pay_status']==1){
            return true;
        }
        $od=array(
            'pay_status'=>1,
            'pay_ok_time'=>time(),
        );
        if(!empty($data['ios_unique_identifier'])){
            $od['ios_unique_identifier']=$data['ios_unique_identifier'];
        }
        $s = $Vip_Order->edit(array('id'=>$orderInfo['id'])+$od);

        if($s>0){
            $Account=new Account();
            $s=$Account->updateInfo(array('id'=>$orderInfo['uid']),array(
                'vip_rank'=>1,
                'vip_time'=>time(),
            ));
        }
        if($s>0){
            return true;
        }
        return false;
    }


    //支付宝处理自己平台
    public function Pay_zfb_me_h5($data){
        $APay = new \SsdPHP\Pulgins\Alipay\Pay();
        return $url = $APay::H5_unifiedorder($data['price'],$data['oid'],$data['body']);
    }

    //{{{支付

    //支付宝处理自己平台
    public function zhifubao($data){
        $PAYCONFIG = Aconfig::getField('pay',"alipay")['h5pay'];
        return $url = Alipay::Ali_H5_unifiedorder($data['price'],$data['oid'],$data['body'],$PAYCONFIG);

    }

    //微信自己平台
    public function weixin($data){
        $body       = $data['body'];
        $order_no   = $data['oid'];
        $fee        = $data['price'];
        $clientIp   = !empty($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER["REMOTE_ADDR"];
        $PAYCONFIG = Aconfig::getField('pay',"weixin")['h5pay'];
        $mweb_url = Wxpay::H5_unifiedorder($fee,$order_no,$body,$clientIp,$PAYCONFIG);
        if(is_numeric($mweb_url)){
            return false;
        }
        $pay_url = $mweb_url;

        return $PAYCONFIG['payWebUrl']."?pay_url=".base64_encode($pay_url);
    }

    //支付宝处理自己平台
    public function ios($data){
        $PAYCONFIG = Aconfig::getField('pay',"alipay")['h5pay'];
        return $url = Alipay::Ali_H5_unifiedorder($data['price'],$data['oid'],$data['body'],$PAYCONFIG);

    }
    //}}}end 支付


    /**

     * 验证AppStore内付

     * @param  string $receipt_data 付款后凭证

     * @return array                验证是否成功

     */
    public function validate_apple_pay($apple_receipt)
    {
        // 验证参数
        if (strlen($apple_receipt)<20){

            $result=array(

                'status'=>-1,

                'message'=>'非法参数'

            );
            return $result;

        }

        // 请求验证
        $html = Functions::acurl($apple_receipt);

        $data = json_decode($html,true);

        // 如果是沙盒数据 则验证沙盒模式

        if($data['status']=='21007'){

            // 请求验证

            $html = Functions::acurl($apple_receipt, 1);

            $data = json_decode($html,true);

            $data['sandbox'] = '1';

        }

        if (isset($_GET['debug'])) {

            exit(json_encode($data));

        }
        // 判断是否购买成功
        if(intval($data['status'])===0){

            $result=array(

                'status'=>1,

                'message'=>'购买成功'

            );

        }else{

            $result=array(

                'status'=>-1,

                'message'=>'购买失败 status:'.$data['status']

            );

        }
        return $result;
    }
}