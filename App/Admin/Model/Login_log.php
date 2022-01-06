<?php
/**
 * Created by PhpStorm.
 * User: 许
 * Date: 2020-9-14
 * Time: 11:50
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Login_log extends Model
{
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

}