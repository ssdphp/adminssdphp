<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Jifen_Get_Record extends Model {
    /**
     * @var array1-抽奖获得，2，充值，3-注册，4-邀请好友
     */
    public $type=array(
        1=>'抽奖获得',
        2=>'充值',
        3=>'注册',
        4=>'邀请好友',
    );

	/**
     * @var array
     * 1-抽奖获得，2，充值，3-注册，4-邀请好友,5-积分抽奖
     */
    public $type_str=array(
        1=>array(
            'str'=>'抽奖获得',
            'action'=>'+',
        ),
        2=>array(
            'str'=>'充值获得',
            'action'=>'+',
        ),
        3=>array(
            'str'=>'首次登录使用',
            'action'=>'+',
        ),
        4=>array(
            'str'=>'邀请好友',
            'action'=>'+',
        ),
        5=>array(
            'str'=>'分享APP获得',
            'action'=>'+',
        ),
        6=>array(
            'str'=>'每日签到',
            'action'=>'+',
        )
    );

	
    public function writeLog($data){
        $time = time();
        $data['index_ymd']=date('Ymd',$time);
        $data['create_time']=$time;
        return $this->insert($data);
    }


}