<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/20
 * Time: 15:58
 */

namespace App\Admin\Model;

use function Psy\debug;
use SsdPHP\Core\Model;

class User_pinglun_type extends Model{

    //任务状态
    public $status=array(
        0=>array(
            'color'=>'green',
            'str'=>'正常',
        ),
        1=>array(
            'color'=>'red',
            'str'=>'禁用',
        ),
    );

    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getAll($cond=array(),$feild=['*'],$orderby="id asc"){

        $ret = $this->select($cond,$feild,"",$orderby)->items;

        return $ret;
    }

    /**
     * 添加
     * @return mixed
     */
    public function _add($data){
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