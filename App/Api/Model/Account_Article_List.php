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
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\SsdPHP;

class Account_Article_List extends Model
{
    //1-正常，0-禁用,2-审核中
    public $status = array(
        0 => array(
            'color' => '#ff0000',
            'str' => "未通过",
        ),
        1 => array(
            'color' => '#0cc55e',
            'str' => "已通过",
        ),
        2 => array(
            'color' => '#ffb82e',
            'str' => "审核中",
        )
    );

    //1-正常，0-禁用,2-审核中
    public $class = array(
        1 => array(
            'color' => '#FFFFFF',
            'bg_color' => '#FF9D3D',
            'str' => "长动态",
        ),
        2 => array(
            'color' => '#FFFFFF',
            'bg_color' => '#35E393',
            'str' => "转换文章",
        ),
        3 => array(
            'color' => '#FFFFFF',
            'bg_color' => '#23A34C',
            'str' => "短动态",
        )
    );
}