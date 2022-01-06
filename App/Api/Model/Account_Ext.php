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

class Account_Ext extends Model {


    /**
     * 通过条件获取帐号基础信息
     * @param array $cond
     * @param array $feild
     * @return array|mixed
     */
	public function findone($cond=array(),$feild=["*"]){

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

	    return $this->update($condition,$item);
    }
    /**
     * 添加用户信息
     */
	public function add($item){

        $id = $this->insert($item);

        return $id;
    }


}