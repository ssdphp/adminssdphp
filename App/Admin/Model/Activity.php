<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Activity extends Model{

    //状态 0-禁用 1-启用
    public $state = array(

        1=>'启用',
        0=>'禁用',
    );
    //活动类型 0-线下 1-线上
    public $online = array(

        1=>'线上',
        0=>'线下',
    );

    //状态 0-未开始 1-活动中 2-暂停中 3-已结束
    public $status = array(

        0=>'未开始',
        1=>'活动中',
        2=>'暂停中',
        3=>'已结束',
    );
    //是否置顶 0-否 1-是
    public $top = array(

        1=>'是',
        0=>'否',
    );
}