<?php
/**
 * Created by PhpStorm.
 * User: xzh_tx@163.com
 * Date: 2017/3/14
 * Time: 16:04
 */

namespace App\Api\Model;

use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Pulgins\Cache\Factory as Cache;
use SsdPHP\Pulgins\Session\Factory as Session;
use SsdPHP\Pulgins\DataBase\Factory as Db;

/**
 * 渠道管理
 * Class Member
 * @author xiaohuihui <xzh_tx@163.com>
 */
class Ditch extends Model
{

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    //public function getList($cond=array(),$page=1,$pagesize=10,$field=array("*"),$order="id desc"){
//
    //    $a = $this->setPage($page,$pagesize)
    //        ->select($cond,$field,"",$order);
//
    //    return $a;
    //}
    ///**
    // * @param $uid
    // * @param int $page
    // * @param int $pagesize
    // */
    //public function findOne($cond=array(),$field=array("*"),$order="id desc"){
//
    //    $a = $this->selectOne($cond,$field,"",$order);
    //
    //    return $a;
    //}
}