<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\SsdPHP;

class Account_Total_All extends Model {
//统计
    public function _add($d=array()){
        $data = $d;
        $data = array(
            'uid'=>$data['uid'],
            'ext_id'=>$data['ext_id'],
            'total_view_count'=>$data['total_view_count'],
            'total_type'=>$data['total_type'],
            'create_time'=>$data['create_time'],
        );
        if(empty($data)){
            return 0;
        }
        $ret = $this->findone(array(
            'uid'=>$data['uid'],
            'ext_id'=>$data['ext_id'],
            'total_type'=>$data['total_type'],
        ));

        if(empty($ret['id'])){
            return $this->add($data);
        }

        $updata_data = array();
        if(isset($data['total_view_count'])){
            $updata_data['total_view_count'] = $ret['total_view_count']+1;
        }

        $updata_data['update_time']=$data['create_time'];
        return $this->updateInfo(array('id'=>$ret['id']),$updata_data);


    }
}