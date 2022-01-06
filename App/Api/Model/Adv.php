<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;
use SsdPHP\SsdPHP;

class Adv extends Model {


    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getPageList($cond=array(),$page=1,$pagesize=50,$feild=['*'],$orderby="sort desc,id desc"){

        $ret = $this->setPage($page,$pagesize)->select($cond,$feild,"",$orderby);

        return $ret;
    }

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getAll($cond=array(),$field=array("*"),$order="id desc"){

        return $this->select($cond,$field,"",$order)->items;
    }



    /**
     * 修改
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $retval = $this->getReturnVal();
        if(empty($data) || empty($data['id'])){
            $retval['ret']=0;
            $retval['code']=1005;
            return $retval;
        }
        $id = $this->update(array("id"=>intval($data['id'])),$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1005;
        return $retval;
    }
}