<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17
 * Time: 16:53
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Download_click_log extends Model{

    //任务状态
    public $type=array(
        1=>'赚钱',
        2=>'涨粉',
    );

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

        if(!empty($id)){
            return $id;
        }
        return -2;
    }
}