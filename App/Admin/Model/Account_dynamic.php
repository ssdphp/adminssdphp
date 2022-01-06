<?php
/**
 * Created by PhpStorm.
 * User: 许
 * Date: 2020-11-18
 * Time: 10:29
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Account_dynamic extends Model{

    //动态状态
    public $status = array(
        0 => array('title' => '审核中', 'color' => 'red',),
        1 => array('title' => '通过', 'color' => 'green',),
        2 => array('title' => '未通过', 'color' => 'red',),
    );

}