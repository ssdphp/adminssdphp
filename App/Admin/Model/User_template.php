<?php
/**
 * Created by PhpStorm.
 * User: 许
 * Date: 2020-8-21
 * Time: 15:15
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class user_template  extends Model
{
    //账号状态
    public $status = array(
        0 => array('title' => '审核中', 'color' => 'red',),
        1 => array('title' => '审核通过', 'color' => 'green',),
        2 => array('title' => '审核未通过', 'color' => 'red',),
    );

}