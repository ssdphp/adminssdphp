<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 15:18
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;


class Operation_log extends ReturnVal {

    /**
     * 添加
     * @param $data
     * @return array
     */
    public function add($data)
    {
        $retval = $this->getReturnVal();
        if(empty($data) || empty($data['uid']) || empty($data['c_project'])){
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

}