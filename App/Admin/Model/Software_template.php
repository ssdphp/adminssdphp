<?php
/**
 * Created by PhpStorm.
 * User: xzh_tx@163.com
 * Date: 2017/3/14
 * Time: 16:04
 */
namespace App\Admin\Model;

use App\Utils\Utils;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;

class Software_template extends Model
{

    //账号状态
    public $status = array(
        1 => array('title' => '审核通过', 'color' => 'green',),
        2 => array('title' => '审核中', 'color' => 'red',),
        3 => array('title' => '审核未通过', 'color' => 'red',),
    );

    //是否推荐
    public $is_top = array(
        0=>"不推荐",
        1=>"推荐"
    );

    //是否推荐
    public $t_type = array(
        0=>"暂无分类"
    );

    //使用类型
    public $s_type = array(
        0=>'免费',
        1=>'VIP',
        2=>'￥3'
    );

    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getAll($cond=array(),$field=["*"],$order="id desc"){

        $a = $this->select($cond,$field,"",$order)->items;

        return $a;
    }

    /**
     * 添加
     * @return mixed
     */
    public function _add($data){

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
}