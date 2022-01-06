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

class Software_Version extends Model {

    //软件类型 1安卓，2苹果
    public $os=array(
        1=>'安卓',
        2=>'苹果',
        3=>'公众号',
        4=>'小程序',
    );

    //状态0-禁用，1-正常
    public $status=array(
        0=>'禁用',
        1=>'正常',
    );

    /**
     * 添加
     * @return number
     */
    public function _add($data){
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
        //echo $this->getlastsql();
        if(!empty($id)){
            return $id;
        }
        return -2;
    }
}