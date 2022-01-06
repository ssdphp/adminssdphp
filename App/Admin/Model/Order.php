<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use App\Api\Plugin\Curl;
use SsdPHP\Core\Model;
use SsdPHP\SsdPHP;

class Order extends Model {
    //支付状态
    public $pay_status = array(
        -1 => array('title' => '所有', 'color' => 'dark',),
        0 => array('title' => '未成功', 'color' => 'red',),
        1 => array('title' => '已成功', 'color' => 'green',),
    );
    //支付类型
    public $pay_type = array(
        0 => array('title' => '所有','icon'=>''),
        1 => array('title' => '支付宝','icon'=>'fa fa-wechat green'),
        2 => array('title' => '微信','icon'=>'fa fa-alipay blue'),
    );
    //支付的客户端
    public $pay_client = array(
        0 => array('title' => '所有','icon'=>''),
        1 => array('title' => '安卓','icon'=>'fa fa-android green'),
        2 => array('title' => '苹果','icon'=>'fa fa-apple grey'),
    );
    
}