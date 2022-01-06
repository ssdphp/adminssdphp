<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Admin_Auth_Group extends Model {

    //0-正常 1-禁用
    private $status = array(
        0=>'正常',
        1=>'禁用',
    );

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
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

        if( $id >0 ){
            return 1;
        }
        return -1;
    }

}