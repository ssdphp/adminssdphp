<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use App\Api\Model\Pay\Alipay;
use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;

class Txorder extends Model
{

    //1-完成，2-进行中
    public $txstate = array(
        1 => array(
            'color' => '#D3D3D3',
            'str' => '成功',
        ),
        2 => array(
            'color' => '#FA4841',
            'str' => '失败',
        )
    );


}