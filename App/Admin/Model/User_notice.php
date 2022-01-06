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

class User_notice extends Model {

    //公告状态
    public $status=array(
        1=>'正常',
        2=>'禁用',
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

        if(empty($data) || empty($data['id'])){
            return -1;
        }
        $data['update_time']=time();
        $id = $this->update(array("id"=>intval($data['id'])),$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            return $id;
        }
        return -2;
    }

    public function cat_table_struct(){
        $db = \SsdPHP\Core\Config::getField("mysql",'main');
        $sql = "select column_name, column_comment from information_schema.columns where table_schema ='{$db[0]['database']}' and table_name = '{$db[0]['prefix']}task_list';";
        return $this->exec($sql);
    }
}