<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/22
 * Time: 16:32
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Account_photo_bind extends Model {

    public $type = array(
        1=>"快手",
        2=>"抖音",
    );

    public $status = array(
        0=>"禁用",
        1=>"正常",
        2=>"待审核"
    );

    public $is_used = array(
        0=>"未使用",
        1=>"使用中",
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

    public function writeLog($data){
        $time = time();
        $data['index_ymd']=date('Ymd',$time);
        $data['index_ym']=date('Ym',$time);
        $data['index_y']=date('Y',$time);
        $data['create_time']=$time;
        $s = $this->insert($data);
        return $s;
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

    public function cat_table_struct(){
        $db = \SsdPHP\Core\Config::getField("mysql",'main');
        $sql = "select column_name, column_comment from information_schema.columns where table_schema ='{$db[0]['database']}' and table_name = '{$db[0]['prefix']}account';";
        return $this->exec($sql);
    }

}