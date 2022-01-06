<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Yewu_Product extends Model {


    //1-正常，2-维护中,3-禁用，4-未开通
    public $status=array(
        1=>'正常',
        2=>'维护中',
        3=>'禁用',
        4=>'未开通',
    );


    //1-内部业务处理，2-URL打开
    public $open_type=array(
        1=>'内部处理',
        2=>'URL打开',
    );


    //是否可以自定义订单数量0-不可以，1-可以
    public $is_auto_num=array(
        0=>'不可以',
        1=>'可以',
    );
    //是否推荐0-不是，1-是
    public $is_top=array(
        0=>'不推荐',
        1=>'推荐',
    );



    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getList($cond=array(),$page=1,$pagesize=50,$feild=['*'],$orderby="sort desc"){

        $ret = $this->setPage($page,$pagesize)->select($cond,$feild,"",$orderby);

        return $ret;
    }

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getAll($cond=array(),$field=array(
        "*",
    ),$order="id asc"){

        $a = $this->select($cond,$field)->items;

        return $a;
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
        if($id!==false){
            return $id;
        }
        return -2;
    }
}