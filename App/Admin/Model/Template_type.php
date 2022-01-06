<?php
/**
 * Created by PhpStorm.
 * User: 许
 * Date: 2020-7-29
 * Time: 10:26
 */

namespace App\Admin\Model;

use SsdPHP\Core\Config;
use SsdPHP\Core\Model;


class Template_type extends Model
{
    //类型状态
    public $status = array(
        0 => array('title' => '正常', 'color' => 'green',),
        1 => array('title' => '禁用', 'color' => 'red',),
    );

    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getAll($cond=array(),$field=["*"],$order="id desc"){

        $a = $this->select($cond,$field,"",$order)->items;

        return $a;
    }


}