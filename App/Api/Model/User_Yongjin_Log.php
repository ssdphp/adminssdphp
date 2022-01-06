<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use SsdPHP\Core\Model;

class User_Yongjin_Log extends Model
{

    /**
     * 收支类型，1-徒弟订单收入，2-徒弟做任务提成，3-徒弟成为团长提成
     * @var array
     */
    public $type = array(
        1 => array(
            'icon' => 'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp0fj7si21irn2461di08sm8.png',
            'str' => '徒弟订单收入',
            'addon' => '+',
            'str_color' => '#D3D3D3',
        ),
        2 => array(
            'icon' => 'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp13si15u7786p9t1o0e4qad.png',
            'str' => '徒弟做任务提成',
            'addon' => '+',
            'str_color' => '#D3D3D3',
        ),
        3 => array(
            'icon' => 'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp1ii6fh67npmiv1fom13eji.png',
            'str' => '徒弟成为团长提成',
            'addon' => '+',
            'str_color' => '#D3D3D3',
        ),
        4 => array(
            'icon' => 'http://static.qiniu-static.65141.com/dfz/images/yewu/o_1cvpp1ii6fh67npmiv1fom13eji.png',
            'str' => '邀请成员',
            'addon' => '+',
            'str_color' => '#D3D3D3',
        ),
    );


    public function writeLog($data)
    {
        $time = time();
        $data['index_ymd'] = date('Ymd', $time);
        $data['index_ym'] = date('Ym', $time);
        $data['index_y'] = date('Y', $time);
        $data['create_time'] = $time;
        $s = $this->insert($data);
        return $s;
    }


    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getList($cond = array(), $page = 1, $pagesize = 10, $field = array("*"))
    {

        $a = $this->setPage($page, $pagesize)
            ->select($cond, $field, "", "id desc");

        return $a;
    }


    /**
     * 获取一条信息
     * @param $cond
     * @param array $feild
     * @return array|mixed
     */
    public function findone($cond, $feild = array("*"))
    {

        $ret = $this->selectOne($cond, $feild);
        if (!empty($ret)) {
            return $ret;
        }
        return [];
    }

}