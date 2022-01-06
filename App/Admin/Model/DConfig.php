<?php
/**
 * Created by PhpStorm.
 * User: xzh_tx@163.com
 * Date: 2017/3/14
 * Time: 16:04
 */
namespace App\Admin\Model;

use SsdPHP\Core\Model;

/**
 * 渠道管理
 * Class Member
 * @author xiaohuihui <xzh_tx@163.com>
 */
class DConfig extends Model
{



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

        if(empty($data) || (empty($data['key']))){
            return -1;
        }

        $cond = array("key"=>$data['key']);
        $data['update_time']=time();
        $id = $this->update($cond,$data);

        if(!empty($id)){
            return $id;
        }
        return -2;
    }

}