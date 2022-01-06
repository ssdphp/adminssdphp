<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/12
 * Time: 14:39
 */

namespace App\Api\Model;

use App\Common\Tool\Functions;
use SsdPHP\Core\Model;

class Vip_order extends Model
{
    public function addorder($data){
        $ret = $this->insert($data);
        if($ret!==false && $ret > 0){
            return $ret;
        }
        return false;
    }

    /**
     * 这个方法废弃了 @author xhh
     * 通过订单id更新支付完成状态
     * 1.更新订单。充值积分
     * @param $oid
     * @param $data
     * @return bool
     */
    public function update_pay_status_old($oid, $data)
    {
        $orderInfo = $this->findone(array('order_id' => $oid));
        if (empty($orderInfo['order_id'])) {
            return false;
        }
        if (!empty($orderInfo['pay_status']) && $orderInfo['pay_status'] == 1) {
            return true;
        }
        $od = array(
            'pay_status' => 1,
            'pay_ok_time' => time(),
            'pay_seq' => $data['pay_seq'] ?? "",
        );

        $s = $this->update(array('order_id' => $oid), $od);
        //更新成功
        //写入积分
        if ($s > 0) {
            $Account = new Account();
            $uinfo = $Account->findone(array("id" => $orderInfo['uid']));
            $utime = time();
            if ($uinfo['vip_expire_time'] > $utime) {
                $utime = $uinfo['vip_expire_time'];
            }
            $s1 = $Account->updateInfo(array('id' => $orderInfo['uid']), array(
                //"vip_rank"=>"1",
                "update_time" => time(),
                "vip_expire_time" => strtotime($orderInfo['p_value'], $utime),
                '`total_pay`=`total_pay`+' . $orderInfo['p_price'],
            ));
            if ($s1) {
                /*$User_Shouzhi_Log = new User_Shouzhi_Log();
                //添加收入日志
                $s = $User_Shouzhi_Log->writeLog(array(
                    'uid'=>$orderInfo['uid'],
                    'type'=>1,
                    'money'=>$orderInfo['p_price'],
                    'ext_info'=>json_encode(array(
                        'orderinfo'=>$orderInfo
                    )),
                    'descript'=>$User_Shouzhi_Log->type[1]['str']."(开通会员)"
                ));
                $uinfo = $Account->findone(array('id'=>$orderInfo['uid']));*/
                if (false /*!empty($uinfo['agent_uid']) */) {
                    //为师父加钱
                    //移除师傅的邀请数量-1
                    $kaituan_money = Config::getField('config', 'kaituan_money');
                    $s = $Account->updateInfo(array('id' => $uinfo['agent_uid']), array("tixian_rmb=tixian_rmb+{$kaituan_money}", "total_member_num=total_member_num-1", 'update_time' => time()));

                    //师父佣金日志
                    $User_Yongjin_Log = new User_Yongjin_Log();
                    $s = $User_Yongjin_Log->writeLog($a = array(
                        'uid' => $uinfo['agent_uid'],
                        'type' => 3,
                        'money' => $kaituan_money,
                        'descript' => $User_Yongjin_Log->type[3]['str'],
                        'ext_info' => json_encode(array(
                            "tixian" => $data
                        ))
                    ));
/*
                    //添加收入日志
                    $s = $User_Shouzhi_Log->writeLog(array(
                        'uid' => $uinfo['agent_uid'],
                        'type' => 10,
                        'money' => Config::getField('config', 'kaituan_money'),
                        'ext_info' => json_encode(array(
                            'orderinfo' => $orderInfo
                        )),
                        'descript' => $User_Shouzhi_Log->type[10]['str'] . "(成员成为团长)"
                    ));*/

                }

                return true;
            }

        }
        return false;
    }

    /**
     * @param $oid
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function update_pay_status($oid)
    {
        $orderInfo = $this->findone(array('order_id'=>$oid));
        if(empty($orderInfo['order_id'])){
            return false;
        }
        if(!empty($orderInfo['pay_status']) && $orderInfo['pay_status']==1){
            return true;
        }
        $od=array(
            'pay_status'=>1,
            'pay_ok_time'=>time(),
        );
        $s = $this->update(array('order_id'=>$oid),$od);
        //更新成功
        //写入积分
        if($s>0){
            $Account = new Account();
            $uinfo = $Account->findone(array("id"=>$orderInfo['uid']));
            $utime = time();
            if($uinfo['vip_expire_time'] > $utime){
                $utime = $uinfo['vip_expire_time'];
            }
            //如果是代理商。不处理充会员了
            $viprank = Functions::getVipRank($uinfo['vip_expire_time'],$uinfo['vip_rank']);
            if($viprank == 2){
                return true;
            }
            $s1 = $Account->updateInfo(array('id'=>$orderInfo['uid']),array(
                "vip_rank"=>"1",
                "update_time"=>time(),
                "vip_expire_time"=>strtotime($orderInfo['p_value'],$utime),
                '`total_pay`=`total_pay`+'.$orderInfo['p_price'],
            ));
            if($s1){
                $User_Shouzhi_Log = new User_Shouzhi_Log();
                //添加收支记录日志
                $s = $User_Shouzhi_Log->writeLog(array(
                    'uid'=>$orderInfo['uid'],
                    'type'=>1,
                    'money'=>$orderInfo['p_price'],
                    'ext_info'=>json_encode(array(
                        'orderinfo'=>$orderInfo
                    )),
                    'descript'=>$User_Shouzhi_Log->type[1]['str']."(开通会员)"
                ));


                if(!empty($uinfo['agent_uid'])){
                    //检查上级是不是代理商
                    $agent_info = $Account->findone(array("id"=>$uinfo['agent_uid']));
                    $agent_viprank = Functions::getVipRank($agent_info['vip_expire_time'],$agent_info['vip_rank']);
                    //如果不是代理商，没有提成
                    if($agent_viprank != 2){
                        return true;
                    }
                    //为代理商分成

                    $agent_precent = Config::getField('config','agent_precent',10/100);

                    $txrmb = $orderInfo['p_price']*$agent_precent;
                    $s = $Account->updateInfo(array('id'=>$uinfo['agent_uid']),array(
                            "tixian_rmb=tixian_rmb+{$txrmb}",
                            'update_time'=>time())
                    );

                    //为代理商添加分成日志
                    $User_Yongjin_Log = new User_Shouzhi_Log();
                    $s = $User_Yongjin_Log->writeLog($a = array(
                        'uid'=>$uinfo['agent_uid'],
                        'type'=>1,
                        'money'=>$txrmb,
                        'descript'=>$User_Yongjin_Log->type[3]['str'],
                        'ext_info'=>json_encode(array(
                            "tixian"=>$orderInfo
                        ))
                    ));
                    //为代理商添加收入日志
                    $s = $User_Shouzhi_Log->writeLog(array(
                        'uid'=>$uinfo['agent_uid'],
                        'type'=>2,
                        'money'=>$txrmb,
                        'ext_info'=>json_encode(array(
                            'orderinfo'=>$orderInfo
                        )),
                        'descript'=>$User_Shouzhi_Log->type[2]['str']."(成员充值VIP)"
                    ));

                }

                return true;
            }

        }
        return false;
    }
}