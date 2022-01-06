<?php
/**
 * Created by PhpStorm.
 * User: xzh_tx@163.com
 * Date: 2017/3/14
 * Time: 16:04
 */
namespace App\Api\Model;

use SsdPHP\Core\Model;

/**
 * 渠道管理
 * Class Member
 * @author xiaohuihui <xzh_tx@163.com>
 */
class Config extends Model
{


    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getList($cond=array(),$page=1,$pagesize=10,$field=array("*"),$order="id desc"){
        //define("DEBUG",1);
        $a = $this->setPage($page,$pagesize)->select($cond,$field,"",$order);

        return $a;
    }

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getAll($cond=array(),$field=array(
        "*",
    ),$order="id asc"){

        $a = $this->select($cond,$field)->items;

        return $a;
    }


    /**
     * 修改
     * @param $data
     * @return mixed
     */
    public function edit($data)
    {

        if(empty($data) || (empty($data['key']))){
            return -1;
        }

        $cond = array("key"=>$data['key']);
        $id = $this->update($cond,$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            return $id;
        }
        return -2;
    }



    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function findOne($cond=array(),$field=array("*")){

        $a = $this->selectOne($cond,$field,"");

        return $a;
    }
}