<?php
/**
 * Created by PhpStorm.
 * User: xzh_tx@163.com
 * Date: 2017/3/14
 * Time: 16:04
 */
namespace App\Admin\Model;

use App\Utils\Utils;
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
class Channel_ID  extends Model
{


    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getall($cond=array(),$field=array("*"),$order="id desc"){

        return $this->select($cond,$field,"",$order)->items;
    }

    /**
     * 修改
     * @param $data
     * @return array
     */
    public function edit($data)
    {

        $id = $this->update(array("id"=>intval($data['id'])),$data);
        return $id;
    }
}