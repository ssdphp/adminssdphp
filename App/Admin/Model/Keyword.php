<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/15
 * Time: 10:15
 */

namespace App\Admin\Model;

use SsdPHP\Core\Config;
use SsdPHP\Core\Model;


class Keyword extends Model
{
//文字状态
    public $status = array(
        0 => array('title' => '正常', 'color' => 'green',),
        1 => array('title' => '禁用', 'color' => 'red',),
    );

    //文字状态
    public $type = array(
        0 => array('title' => '无', 'color' => '',),
        1 => array('title' => '用户', 'color' => 'green',),
        2 => array('title' => '系统', 'color' => 'red',),
    );


    /**
     * 添加
     * @return mixed
     */
    public function _add($data){

        if(empty($data)){
            return -1;
        }
        $data['create_time']=time();
        $id = $this->insert($data);
        if($id>0){
            return $id;
        }

        return -2;
    }

    /**
     * 修改
     * @param $data
     * @return mixed
     */
    public function edit($data)
    {

        if(empty($data) || (empty($data['id']) && empty($data['did']))){
            return -1;
        }


        $cond = array("id"=>intval($data['id']));
        $data['update_time']=time();
        $id = $this->update($cond,$data);
        if(!empty($id)){
            return $id;
        }
        return -2;
    }

}