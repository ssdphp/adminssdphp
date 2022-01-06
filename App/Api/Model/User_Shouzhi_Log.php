<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;

class User_Shouzhi_Log extends Model {

    /**
     * 收支记录映射
     * 收支类型，
     * @var array
     */
    public $type = array(
        1=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp0fj7si21irn2461di08sm8.png',
            'str'=>'在线支付',
            'addon'=>'-',
            'str_color'=>'#D3D3D3',
        ),
        2=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp13si15u7786p9t1o0e4qad.png',
            'str'=>'合伙人分成收入',
            'addon'=>'+',
            'str_color'=>'#D3D3D3',
        ),
        3=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp1ii6fh67npmiv1fom13eji.png',
            'str'=>'提现扣取',
            'addon'=>'-',
            'str_color'=>'#D3D3D3',
        ),
        4=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp1uhkbnscmo1lkn1gkj12nmn.png',
            'str'=>'成为合伙人',
            'addon'=>'-',
            'str_color'=>'#D3D3D3',
        ),
        5=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp2a2j1qcs1ivq18tb13mtohcs.png',
            'str'=>'签到收入',
            'addon'=>'+',
            'str_color'=>'#D3D3D3',
        ),
        6=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp2lvnfd01id41htd18ligmd11.png',
            'str'=>'未知',
            'addon'=>'-',
            'str_color'=>'#D3D3D3',
        ),
        7=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp39cv1s14sn4qqg117jpb016.png',
            'addon'=>'-',
            'str'=>'余额支付',
            'str_color'=>'#D3D3D3',
        ),
        8=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp3kot1c4d10ca1bm61pok141r1b.png',
            'addon'=>'+',
            'str'=>'系统补发',
            'str_color'=>'#D3D3D3',
        ),
        9=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1d9ccpucfadc1lrs8s31dfktk7i.png',
            'addon'=>'+',
            'str'=>'邀请奖励',
            'str_color'=>'#D3D3D3',
        ),
        10=>array(
            'icon'=>'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1d9ccp2bvlcb163stdnq1te5md.png',
            'addon'=>'+',
            'str'=>'成为合伙人',
            'str_color'=>'#D3D3D3',
        )
    );



    public function writeLog($data){
        $time = time();
        $data['index_ymd']=date('Ymd',$time);
        $data['index_ym']=date('Ym',$time);
        $data['index_y']=date('Y',$time);
        $data['create_time']=$time;
        $s = $this->insert($data);
        return $s;
    }



    /**
     * @param array $cond
     * @param array $field
     * @param string $order
     * @return mixed
     */
    public function findone($cond=array(),$field=array("*"),$order="id desc"){

        $a = $this->selectOne($cond,$field,"",$order);

        return $a;
    }

    /**
     * 记录排行榜
     */
    public function write_ranking_list($data=array()){
        if(empty($type))
            return false;
        if($type == 'day'){

        }

    }
}