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

/**
 * 我看过谁的记录
 * Class Account_Total_Lookmewho_Day
 * @package App\Home\Model
 */
class Account_Total_Lookmewho_Day extends Model {

    //统计
    public function _add($data=array()){

        if(empty($data)){
            return 0;
        }

        $ret = $this->findone(array(
            'uid'=>$data['uid'],
            'vuid'=>$data['vuid'],
            'ext_id'=>$data['ext_id']??0,
            'total_type'=>$data['total_type']??1,
            'index_ymd'=>$data['index_ymd'],
        ));
        if(empty($ret['id'])){
            $data['update_time']=$data['create_time'];
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