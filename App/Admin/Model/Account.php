<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;
use SsdPHP\SsdPHP;

class Account extends Model {

    public $sex = array(
        0=>"女",
        1=>"男",
    );

    public $vip_rank = array(
        0=>"普通用户",
        1=>"VIP用户"
    );

    public $os = array(
        1=>"安卓",
        2=>"苹果"
    );

    public $did = array(
        1=>"安卓",
        2=>"苹果",
    );

    public $login_type = array(
        1=>"微信",
        2=>"苹果"
    );

    //账号状态
    public $status = array(
        1 => array('title' => '正常', 'color' => 'green',),
        2 => array('title' => '禁用(禁止登录)', 'color' => 'red',),
    );

    public function cat_table_struct(){
        $db = \SsdPHP\Core\Config::getField("mysql",'main');
        $sql = "select column_name, column_comment from information_schema.columns where table_schema ='{$db[0]['database']}' and table_name = '{$db[0]['prefix']}account';";
        return $this->exec($sql);
    }
}