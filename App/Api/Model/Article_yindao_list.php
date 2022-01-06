<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;

class Article_yindao_list extends Model
{
    //1-正常，2-禁用
    public $status = array(

        1 => array(
            'color' => 'green',
            'str' => "正常",
        ),
        2 => array(
            'color' => 'red',
            'str' => "禁用",
        ),
    );

    //文章推荐状态
    public $is_recommend = array(
        0 => array(
            'color' => 'red',
            'str' => "未推荐",
        ),
        1 => array(
            'color' => 'green',
            'str' => "推荐",
        ),
    );

    //文章置顶状态
    public $is_top = array(
        0 => array(
            'color' => 'red',
            'str' => "未置顶",
        ),
        1 => array(
            'color' => 'green',
            'str' => "置顶",
        ),
    );
}