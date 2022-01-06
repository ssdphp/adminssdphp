<?php
/**
 * Created by PhpStorm.
 * User: xzh_tx@163.com
 * Date: 2017/3/14
 * Time: 16:04
 */
namespace App\Admin\Model;

use App\Utils\Utils;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Pulgins\Cache\Factory as Cache;
use SsdPHP\Pulgins\Session\Factory as Session;
use SsdPHP\Pulgins\DataBase\Factory as Db;

/**
 * 渠道管理
 * Class Member
 * @author xiaohuihui <xzh_tx@163.com>
 */
class Msg  extends Model
{
    //0-进行中，1-已完成，2-退单,3-已禁用
    public $t_status=array(
        0=>array(
            'color'=>'red',
            'str'=>'否',
        ),
        1=>array(
            'color'=>'green',
            'str'=>'是',
        )

    );
}