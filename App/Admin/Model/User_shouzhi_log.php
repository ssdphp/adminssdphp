<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/28
 * Time: 10:30
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;


class User_shouzhi_log extends Model {

    public $type = array(
        1=>"在线支付",
        2=>"做任务收入",
        3=>"佣金收入",
        4=>"注册奖励",
        5=>"签到收入",
        6=>"提现扣取",
        7=>"余额支付",
        8=>"系统补发",
        9=>"邀请奖励",
	10=>"开团奖励",
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