<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 15:33
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;
use SsdPHP\SsdPHP;

class Account_ext extends Model {

    //奖励领取状态
    public $lingjiang_status = array(
        0 => array('title' => '未领取', 'color' => 'red',),
        1 => array('title' => '已领取', 'color' => 'green',),
    );

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getList($cond=array(),$page=1,$pagesize=10,$field=array("*"),$order="id desc"){

        $a = $this->setPage($page,$pagesize)
            ->select($cond,$field,"",$order);

        return $a;
    }


    /**
     * @param array $cond
     * @param array $field
     * @param string $order
     * @return mixed
     */
    public function findone($cond=array(),$field=array("*"),$order="id desc"){

        $a = $this->selectOne($cond,$field,"",$order);

        return $a;
    }

    /**
     * 更新用户信息
     */
    public function updateInfo($condition,$item){

        return $this->update($condition,$item);
    }

}