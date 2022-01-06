<?php
/**
 * Created by PhpStorm.
 * User: 许
 * Date: 2020/2/18
 * Time: 15:14
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;

class Article_image_cat extends Model
{
    //类型状态
    public $status = array(
        0 => array(
            'color' => 'green',
            'str' => '正常',
        ),
        1 => array(
            'color' => 'red',
            'str' => '禁用',
        ),
    );

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getall($cond = array(), $field = array("*"), $order = "sort desc")
    {

        return $this->select($cond, $field, "", $order)->items;
    }

    /**
     * 修改
     * @param $data
     * @return number
     */
    public function edit($data)
    {
        if (empty($data) || empty($data['id'])) {
            return -1;
        }
        $id = $this->update(array("id" => intval($data['id'])), $data);

        if (!empty($id)) {
            return $id;
        }

        return -2;
    }
}