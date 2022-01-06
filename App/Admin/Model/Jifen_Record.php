<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Jifen_Record extends ReturnVal {


    public function writeLog($data){
        $time = time();
        $data['index_ymd']=date('Ymd',$time);
        $data['create_time']=$time;
        return $this->insert($data);
    }


	
	/**
     * @param $cond
     * @param int $page
     * @param int $pagesize
     */
    public function getAllList($cond=array(),$page=1,$pagesize=10,$field=["*"],$order="id desc"){

        $a = $this->setPage($page,$pagesize)
            ->select($cond,$field,"",$order);

        return $a;
    }

    public function _update($data){

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