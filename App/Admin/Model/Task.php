<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use function Psy\debug;
use SsdPHP\Core\Model;

class Task extends Model {

    //0-进行中，1-已完成，2-退单,3-已禁用
    public $status=array(
            0=>array(
                'color'=>'red',
                'str'=>'进行中',
            ),
            1=>array(
                'color'=>'green',
                'str'=>'已完成',
            ),
            2=>array(
                'color'=>'red',
                'str'=>'退单',
            ),
            3=>array(
                'color'=>'red',
                'str'=>'已禁用',
            )

    );

    //支付方式 zhifubao-支付宝，weixin-微信，money-余额
    public $pay_type = array(
        "zhifubao"=>array(
            'color'=>'blue',
            'str'=>'支付宝',
        ),
        "weixin"=>array(
            'color'=>'#FA4841',
            'str'=>'微信',
        ),
        "money"=>array(
            'color'=>'#FA4841',
            'str'=>'余额',
        )
    );
    //0-未支付，1-已支付，2-已退款
    public $pay_status = array(
        "0"=>array(
            'color'=>'#D3D3D3',
            'str'=>'未支付',
        ),
        "1"=>array(
            'color'=>'green',
            'str'=>'已支付',
        ),
        "2"=>array(
            'color'=>'red',
            'str'=>'已退款',
        )
    );

    //1-内部业务处理，2-URL打开
    public $open_type=array(
        1=>'内部业务处理',
        2=>'URL打开',
    );

    /**
     * 添加
     * @return mixed
     */
    public function _add($data){
        if(empty($data)){
            return -1;
        }
        if(isset($data['id'])){
            unset($data['id']);
        }
        $data['create_time']=time();
        $id = $this->insert($data);

        if(!empty($id)){
            return $id;
        }
        return -2;
    }


    /**
     * 修改
     * @param $data
     * @return mixed
     */
    public function edit($data)
    {

        if(empty($data) || empty($data['tid'])){
            return -1;
        }
        $data['update_time']=time();
        $id = $this->update(array("tid"=>intval($data['tid'])),$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            return $id;
        }
        return -2;
    }

    public function cat_table_struct(){
        $db = \SsdPHP\Core\Config::getField("mysql",'main');
        $sql = "select column_name, column_comment from information_schema.columns where table_schema ='{$db[0]['database']}' and table_name = '{$db[0]['prefix']}task';";
        return $this->exec($sql);
    }
}