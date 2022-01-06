<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;

class Account_Friend_Log extends Model {

    /**
     * 申请交换名片
     * @param $uid 申请人
     * @param $fid 被申请人
     * @return int
     */
    public function change_card($uid,$fid){

        if(empty($uid) || empty($fid) ){
            return -1;
        }

        $Account_Friend = new Account_Friend();
        $friend = $Account_Friend->findone(array(
            'uid'=>$uid,
            'fid'=>$fid,
        ));

    }

}