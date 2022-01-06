<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use App\Api\Plugin\Curl;
use function Psy\debug;
use SsdPHP\Core\Model;
use SsdPHP\SsdPHP;

class Adv extends Model {

    //类型处理，0-没有任何处理，1-url跳转，2-跳转抽奖
    private $type=array(
        0=>'没有任何处理',
        1=>'url跳转',
        2=>'跳转抽奖',
    );


    /**
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getList($cond=array(),$page=1,$pagesize=10,$field=array("*"),$order="sort desc,id desc"){

        $a = $this->setPage($page,$pagesize)
            ->select($cond,$field,"",$order);

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
     * @return number
     */
    public function add($data){
        if(empty($data)){
            return -1;
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