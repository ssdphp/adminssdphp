<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/12
 * Time: 14:36
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;

class Product extends Model
{
    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getAll($cond=array(),$field=array("*"),$order="id asc"){

        $a = $this->select($cond,$field,$order);

        return $a;
    }

}