<?php
/**
 * Created by PhpStorm.
 * User: 许
 * Date: 2020/2/19
 * Time: 11:36
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;

class Article_image extends Model
{
    //素材状态
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

    //VIP使用素材状态
    public $vip_status = array(
        0 => array(
            'color' => 'green',
            'str' => '免费使用',
        ),
        1 => array(
            'color' => 'red',
            'str' => 'VIP使用',
        ),
    );

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