<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;

class Account_Friend extends Model {

    /**
     * 检查对方是不是我的好友
     * @param $uid
     * @param $fid
     * @return bool
     */
    public function is_friend($uid,$fid){
        $friend = $this->findone(array(
            'uid'=>$uid,
            'fid'=>$fid,
        ));
        if(empty($friend['relation'])){
            return false;
        }
        if($friend['relation'] == 2){
            return true;
        }
        return false;

    }

    /**
     * 检查双方是否都是好友
     * @param $uid
     * @param $fid
     * @return bool
     */
    public function is_all_friend($uid,$fid){
        $friend = $this->findone(array(
            'uid'=>$uid,
            'fid'=>$fid,
        ));
        if(empty($friend['relation'])){
            return false;
        }
        $friend2 = $this->findone(array(
            'uid'=>$fid,
            'fid'=>$uid,
        ));
        if(empty($friend2['relation'])){
            return false;
        }
        if($friend['relation'] == 2 && $friend2['relation'] == 2){
            return true;
        }
        return false;
    }

    /**
     * 申请交换名片
     * @param $uid 申请人
     * @param $fid 被申请人
     * @param int $from  来源
     * @return int
     */
    public function change_card($uid,$fid,$from=1){

        if(empty($uid) || empty($fid) ){
            return -1;
        }

        $friend = $this->findone(array(
            'uid'=>$uid,
            'fid'=>$fid,
        ));
        if(!empty($friend)){
            //update
            if($friend['relation']!=2){
                $s = $this->updateInfo(array('id'=>$friend['id']),array(
                    'relation'=>2,
                    'py'=>"",
                    'from'=>$from,
                    'update_time'=>time(),
                ));
            }
        }else{
            //add
            $s = $this->add(array(
                'uid'=>$uid,
                'fid'=>$fid,
                'relation'=>2,
                'py'=>"",
                'from'=>$from,
                'update_time'=>time(),
            ));
        }



        $friend = $this->findone(array(
            'uid'=>$fid,
            'fid'=>$uid,
        ));

        if(!empty($friend)){
            //update
            if($friend['relation']!=2){
                $s = $this->updateInfo(array('id'=>$friend['id']),array(
                    'relation'=>2,
                    'py'=>"",
                    'from'=>$from,
                    'update_time'=>time(),
                ));
            }
        }else{
            //add
            $s = $this->add(array(
                'uid'=>$fid,
                'fid'=>$uid,
                'relation'=>2,
                'py'=>"",
                'from'=>$from,
                'update_time'=>time(),
            ));
        }

        return true;

    }

}