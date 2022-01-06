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

class Account_Total_Today extends Model {
    //统计
    public function _add($d=array()){
        $data = array(
            'uid'=>$d['uid'],
            'ext_id'=>$d['ext_id'],
            'total_view_count'=>$d['total_view_count'],
            'total_type'=>$d['total_type'],
            'index_y'=>$d['index_y'],
            'index_ym'=>$d['index_ym'],
            'index_ymd'=>$d['index_ymd'],
            'create_time'=>$d['create_time'],
            'update_time'=>$d['create_time'],
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
        if($ret['update_time'] > strtotime(date("Y-m-d 00:00:00",$data['update_time']))){
            //今天的开始累加
            $updata_data = array();
            if(isset($data['total_view_count'])){
                $updata_data['total_view_count'] = $ret['total_view_count']+1;
            }
            $updata_data['update_time']=$data['create_time'];
            return $this->updateInfo(array('id'=>$ret['id']),$updata_data);
        }
        //今天的开始累加
        $updata_data = array();
        if(isset($data['total_view_count'])){
            $updata_data['total_view_count'] = $data['total_view_count'];
        }
        $updata_data['update_time']=$data['create_time'];
        return $this->updateInfo(array('id'=>$ret['id']),$updata_data);
    }
}