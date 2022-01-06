<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use App\Api\Plugin\Curl;
use SsdPHP\Core\Model;
use SsdPHP\SsdPHP;

class Card_No extends ReturnVal {

    private $status=array(
        1=>'正常',
        2=>'禁用',
    );

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getList($cond=array(),$page=1,$pagesize=10,$field=array("*"),$order="project_id asc,id desc"){

        $a = $this->setPage($page,$pagesize)
            ->select($cond,$field,"",$order);

        return $a;
    }

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function findall($cond=array(),$field=array("*"),$order="id asc,num desc"){

        $a = $this->setCount(true)->select($cond,$field,"",$order);

        return $a;
    }
    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function findOne($cond=array(),$field=array("*"),$order="id desc"){

        $a = $this->selectOne($cond,$field,"",$order);

        return $a;
    }


    /**
     * 添加
     * @return array
     */
    public function add($data){
        $retval = $this->getReturnVal();
        if(empty($data)){
            $retval['ret']=0;
            $retval['code']=0;
            return $retval;
        }
        $data['create_time']=time();
        $id = $this->insert($data);

        if(!empty($id)){
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1004;
        return $retval;
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

    public function _update($cond,$data){

        $id = $this->update($cond,$data);
        //echo $this->getlastsql();
        if($id!==false){
            return true;
        }
        return false;
    }
}