<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Shua_Record extends ReturnVal {

    //刷单进行状态，1-完成，2-进行中
    public $order_status = array(
        1=>array(
            'color'=>'#D3D3D3',
            'str'=>'完成',
        ),
        2=>array(
            'color'=>'#FA4841',
            'str'=>'进行中',
        )
    );
    //提交到三方刷单进行状态，1-完成，2-进行中
    //异步下单成功状态，0-未下单，1-成功下单，2-下单失败等待下次队列
    public $task_status = array(
        0=>array(
            'color'=>'#D3D3D3',
            'str'=>'未提交',
        ),
        1=>array(
            'color'=>'#FA4841',
            'str'=>'已下单',
        ),
        2=>array(
            'color'=>'#FA4841',
            'str'=>'提交失败',
        )
    );


    public function writeLog($data){
        $time = time();
        $data['index_ymd']=date('Ymd',$time);
        $data['index_ym']=date('Ym',$time);
        $data['create_time']=$time;

        $s = $this->insert($data);
        return $s;
    }

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getListCond($cond=array(),$page=1,$pagesize=10,$field=array("*")){
        $a = $this->setPage($page,$pagesize)->select($cond,$field,"","id desc");
        return $a;
    }

    public function _update($data){

        $retval = $this->getReturnVal();
        if(empty($data) || empty($data['id'])){
            $retval['ret']=0;
            $retval['code']=1005;
            return $retval;
        }

        if(is_array($data['id'])){
            $ids = "'".implode("','",$data['id'])."'";
            $cdtion=array(
                "id in ($ids)"
            );
            unset($data['id']);
        }else{
            $cdtion=array("id"=>intval($data['id']));
        }
        $id = $this->update($cdtion,$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1005;
        return $retval;
    }

    public function _update_task($cdtion,$data=array()){
        $id = $this->update($cdtion,$data);
        return $id;
    }

}