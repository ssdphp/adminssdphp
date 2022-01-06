<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Account_task_photo_log extends Model {

    public $is_used = array(
        0=>"未使用",
        1=>"使用中",
    );

    public $type = array(
        1=>"快手",
        2=>"抖音",
    );

    public function add($data){
        $time = time();
        $data['create_time']=$time;
        $s = $this->insert($data);
        return $s;
    }

    public function _edit($data){
        $time = time();
        $data['update_time']=$time;
        $s = $this->update(array("uid"=>$data['uid'],'pay_type'=>$data['pay_type']),$data);
        return $s;
    }


    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getList($cond=array(),$page=1,$pagesize=10,$field=array("*")){

        $a = $this->setPage($page,$pagesize)
            ->select($cond,$field,"","id desc");

        return $a;
    }

    /**
     * 获取一条信息
     * @param $cond
     * @param array $feild
     * @return array|mixed
     */
    public function findone($cond,$feild=array("*")){

        $ret = $this->selectOne($cond,$feild);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }
}