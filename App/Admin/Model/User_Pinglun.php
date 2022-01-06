<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use function Psy\debug;
use SsdPHP\Core\Model;

class User_Pinglun extends Model {

    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getList($cond=array(),$page=1,$pagesize=100,$feild=['*'],$orderby="id asc"){

        $ret = $this->setPage($page,$pagesize)->select($cond,$feild,"",$orderby);

        return $ret;
    }

    /**
     * 通过条件获取一条记录
     * @param array $cond
     * @param array $feild
     * @return array|mixed
     */
    public function findOne($cond=array(),$feild=['*']){
        $ret = $this->selectOne($cond,$feild);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }

    /**
     * 随机获取一条记录
     * @param array $cond
     * @param array $feild
     * @return array|mixed
     */
    public function randomOne($cond=array(),$feild=['*'],$orderby="rand()"){
        $ret = $this->selectOne($cond,$feild,"",$orderby);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }

    /**
     * 添加
     * @return mixed
     */
    public function add($data){
        if(empty($data)){
            return -1;
        }
        if(isset($data['id'])){
            unset($data['id']);
        }
        $id = $this->insert($data);

        if(!empty($id)){
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

        if(empty($data) || empty($data['id'])){
            return -1;
        }
        $data['update_time']=time();
        $id = $this->update(array("id"=>intval($data['id'])),$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            return $id;
        }
        return -2;
    }

    public function cat_table_struct(){
        $db = \SsdPHP\Core\Config::getField("mysql",'main');
        $sql = "select column_name, column_comment from information_schema.columns where table_schema ='{$db[0]['database']}' and table_name = '{$db[0]['prefix']}task_list';";
        return $this->exec($sql);
    }
}