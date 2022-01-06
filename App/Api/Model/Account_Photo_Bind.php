<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use App\Api\Plugin\Curl;
use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\SsdPHP;

class Account_Photo_Bind extends Model {
	

    /**
     * @param array $cond
     * @param array $field
     * @param string $order
     * @return mixed
     */
    public function getList($cond=array(),$page=1,$pagesize=10,$field=array("*"),$order="id desc"){

        $a = $this->setPage($page,$pagesize)->select($cond,$field,"",$order);

        return $a;
    }

	/**
     * 检查作品绑定了没得
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $Unionid
     * @return array
     */
    public function CheckUserExistByPhoto($uid,$puid,$type){
        if(empty($uid)||empty($puid)||empty($type)){
            return [];
        }
		$cond =['uid'=>$uid,'user_id'=>$puid,'type'=>$type];
        $ret = $this->selectOne($cond,["*"]);
		if(!empty($ret)){
			return $ret;
		}
		return [];
    }
	
	/**
     * 通过帐号id获取帐号基础信息
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $regData
     * @return array
     */
	public function ByUidGetInfo($id,$feild=["*"]){
		if(empty($id)){
            return [];
        }
		$cond =['id'=>$id];
        $ret = $this->selectOne($cond,$feild);
		if(!empty($ret)){
			return $ret;
		}
		return [];
	}
    /**
     * 通过条件获取一条记录
     * @param array $cond
     * @param array $feild
     * @return array|mixed
     */
    public function findOne($cond=array(),$feild=['*']){
        $ret = $this->selectOne($cond,$feild);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }
    /**
     * 更新用户信息
     */
	public function updateInfo($condition,$item){
        $item['update_time']=time();
	    return $this->update($condition,$item);
    }




    /**
     * phone登录注册
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $regData
     * @return array
     */
    public function add($regData){

        $regData['create_time']= time();

        $id = $this->insert($regData);
        if($id  < 1){
            return -1;
        }
        return $id;
    }

    //绑定快手用户信息
    public function bindGifshowData($user_id,$photoid){

        if(empty($user_id) || empty($photoid)){
            return -1;
        }
        $dret = Functions::GetInitNum(1,$user_id,$photoid);

        if(empty($dret['result'])){
            return -2;
        }
        $ret = $dret['result'];
        $d['user_id']=$ret['userProfile']['profile']['user_id']??"";
        $d['photo_id']=$photoid;
        $d['user_sex']=$ret['userProfile']['profile']['user_sex']??"";
        $d['user_name']=$ret['userProfile']['profile']['user_name']??"";
        $d['header_img']=$ret['userProfile']['profile']['headurl']??"";
        $d['follow_num']=$ret['userProfile']['ownerCount']['follow']??-1;
        $d['photo_num']=$ret['userProfile']['ownerCount']['photo_public']??"";
        $d['open_protocol']=$dret['photo_protocol'];


        if(empty($d['user_id']))
            return -3;

        if($d['photo_num']<1)
            return -4;

        return $d;
    }
    //绑定快手用户信息
    public function bindDouyin($user_id,$photoid){

        if(empty($user_id) || empty($photoid)){
            return -1;
        }
        $dret = Functions::GetInitNum(5,$user_id,$photoid);

        if(empty($dret['result'])){
            return -2;
        }
        $ret = $dret['result'];
        $d['user_id']=$ret['user']['uid']??"";
        $d['photo_id']=$data['pid']??"";
        $d['user_sex']=$ret['user']['gender']??"0";
        $d['user_name']=$ret['user']['nickname']??"";
        $d['header_img']=$ret['user']['avatar_larger']['url_list'][0]??"";
        $d['follow_num']=$ret['user']['following_count']??"0";
        $d['photo_num']=$ret['user']['dongtai_count']??"0";
        $d['like_num']=$ret['user']['favoriting_count']??"0";
        $d['open_protocol']=$dret['photo_protocol'];


        if(empty($d['user_id']))
            return -3;


        return $d;
    }
}