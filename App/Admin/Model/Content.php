<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/15
 * Time: 11:26
 */

namespace App\Admin\Model;

use SsdPHP\Core\Config;
use SsdPHP\Core\Model;

class Content extends Model

{
    //文字状态
    public $status = array(
        0 => array('title' => '正常', 'color' => 'green',),
        1 => array('title' => '禁用', 'color' => 'red',),
    );

    //文字状态
    public $type = array(
        0 => array('title' => '无', 'color' => '',),
        1 => array('title' => '用户', 'color' => 'green',),
        2 => array('title' => '系统', 'color' => 'red',),
    );


    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getList($cond=array(),$page=1,$pagesize=100,$feild=['*'],$orderby="sort desc"){

        $ret = $this->setPage($page,$pagesize)->select($cond,$feild,"",$orderby);

        return $ret;
    }

    /**
     * 添加
     * @return mixed
     */
    public function add($data){

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

        if(empty($data) || (empty($data['id']) && empty($data['did']))){
            return -1;
        }

        if(!empty($data['did'])){
            $cond = array("did"=>$data['did']);
        }else{
            $cond = array("id"=>intval($data['id']));
        }
        $id = $this->update($cond,$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            return $id;
        }
        return -2;
    }



    /**
     *获取单条
     */
    public function findOne($cond=array(),$field=array("*"),$order="id desc"){

        $a = $this->selectOne($cond,$field,"",$order);

        return $a;
    }
}