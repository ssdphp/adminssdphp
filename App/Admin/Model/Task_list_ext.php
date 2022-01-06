<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/13
 * Time: 14:24
 */

namespace App\Admin\Model;

use function Psy\debug;
use SsdPHP\Core\Model;

class Task_list_ext extends Model {
    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getList($cond=array(),$page=1,$pagesize=100,$feild=['*'],$orderby="id desc"){

        $ret = $this->setPage($page,$pagesize)->select($cond,$feild,"",$orderby);

        return $ret;
    }

    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getAll($cond=array(),$feild=['*'],$orderby="id desc"){

        $ret = $this->select($cond,$feild,"",$orderby)->items;

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
        $data['create_time']=time();
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

}