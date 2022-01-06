<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/23
 * Time: 15:57
 */

namespace App\Api\Model;

use function Psy\debug;
use SsdPHP\Core\Model;
use SsdPHP\SsdPHP;

class Article_cat extends Model
{
    //类型状态
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
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getAll($cond=array(),$field=array("*"),$order="id desc"){

        return $this->select($cond,$field,"",$order);
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
     * 修改
     * @param $data
     * @return number
     */
    public function edit($data)
    {
        if(empty($data) || empty($data['id'])){
            return -1;
        }
        $id = $this->update(array("id"=>intval($data['id'])),$data);

        if(!empty($id)){
            return $id;
        }

        return -2;
    }
}