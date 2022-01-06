<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Video_Product_Catgroy extends ReturnVal {

    public $status = array(2=>'禁用',1=>"正常");
    public $is_shangjia = array(1=>"审核通过前",0=>'审核通过后',);
    public $ccat = array(1=>"视频分类",0=>'作假商品分类',);
    public $top = array(0=>"不推荐",1=>'推荐',);


    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getList($cond=array(),$feild=['*'],$orderby="sort desc,id desc"){

        $ret = $this->select($cond,$feild,"",$orderby);

        if(!empty($ret->items)){
            return $ret->items;
        }
        return [];
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
}