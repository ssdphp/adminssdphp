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

class Account_Total_Viewlog_Horus extends Model {

    //统计
    public function _add($data=array()){

        if(empty($data)){
            return 0;
        }
        $ret = $this->findone(array(
            'uid'=>$data['uid'],
            'vuid'=>$data['vuid'],
            'ext_id'=>$data['ext_id'],
            'index_ymdh'=>$data['index_ymdh'],
        ));
        if(empty($ret['id'])){
            return $this->add($data);
        }
        $updata_data = array();
        if(isset($data['total_view_count'])){
            $updata_data['total_view_count'] = $ret['total_view_count']+1;
        }
        if(isset($data['total_jindu'])){

            if($data['total_jindu'] > $ret['total_jindu']){
                $updata_data['total_jindu'] = $data['total_jindu'];
            }
        }
        if(isset($data['total_view_time'])){

            if($data['total_view_time'] > $ret['total_view_time']){
                $updata_data['total_view_time'] = $data['total_view_time'];
            }
        }
        $updata_data['update_time']=$data['create_time'];
        return $this->updateInfo(array('id'=>$ret['id']),$updata_data);

    }
}