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

class Account_Total_Viewlog_Day extends Model {

    //统计
    public function _add($d=array()){
        $data = $d;
        $data = array(
            'uid'=>$data['uid'],
            'vuid'=>$data['vuid'],
            'ext_id'=>$data['ext_id'],
            'total_view_count'=>$data['total_view_count'],
            'total_jindu'=>$data['total_jindu'],
            'total_view_time'=>$data['total_view_time'],
            'total_type'=>$data['total_type'],
            'index_y'=>$data['index_y'],
            'index_ym'=>$data['index_ym'],
            'index_ymd'=>$data['index_ymd'],
            'update_time'=>$data['create_time'],
            'create_time'=>$data['create_time'],
        );
        if(empty($data)){
            return 0;
        }
        $ret = $this->findone(array(
            'uid'=>$data['uid'],
            'vuid'=>$data['vuid'],
            'ext_id'=>$data['ext_id'],
            'index_ymd'=>$data['index_ymd'],
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