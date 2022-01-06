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
use SsdPHP\Pulgins\Cache\Factory as Cache;
use SsdPHP\Pulgins\Session\Factory as Session;
use SsdPHP\Pulgins\DataBase\Factory as Db;

/**
 * 渠道管理
 * Class Member
 * @author xiaohuihui <xzh_tx@163.com>
 */
class Ditch  extends Model
{

    //市场审核状态，0-(市场审核前)未上架, 1-(市场审核成功后)已上架
    public $is_shangjia=array(1=>'审核通过后',0=>'审核通过前',);

    //状态，0-禁用。1-正常
    public $status=array(0=>'禁用',1=>'正常');

    //是否开启好评功能(1: 是, 0: 否)
    public $haoping_status = [0=>'关闭', 1=>'开启'];

    //是否市场违规(1: 是, 0: 否)
    public $haoping_failed = ['正常', '违规'];

    //APP是否开启内部版本检查更新(1: 是, 0: 否)
    public $checkup_in_v = ['关闭', '开启'];

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getAll($cond=array(),$field=array(
        "*",
    ),$order="id desc"){

        $a = $this->select($cond,$field)->items;

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
        //define("DEBUG",1);
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