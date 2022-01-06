<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/23
 * Time: 16:41
 */

namespace App\Api\Model;

use SsdPHP\Core\Config;
use SsdPHP\Session\Session;
use SsdPHP\Core\Model;
use SsdPHP\SsdPHP;

class Article_list extends Model
{

    //文章状态
    public $status = array(
        0 => array(
            'color' => 'green',
            'str' => '正常',
        ),
        1 => array(
            'color' => 'red',
            'str' => '禁用',
        ),
    );

    //文章类型分类
    public $typeid = array(
        1 => '免费共享',
        2 => 'VIP专属文章',
        3 => '收费文章'
    );

    //文章推荐状态
    public $is_recommend = array(
        0 => '不推荐',
        1 => '推荐',
    );

    //文章置顶状态
    public $is_top = array(
        0 => '不置顶',
        1 => '置顶',
    );


    /**
     * @param $uid
     * @param int $page
     * @param int $pagesize
     */
    public function getAll($cond = array(), $field = array("*"), $order = "id desc")
    {

        return $this->select($cond, $field, "", $order)->items;
    }


    /**
     * 修改
     * @param array $data
     * @return number
     */
    public function edit($data)
    {
        if (empty($data) || empty($data['id'])) {
            return -1;
        }
        $id = $this->update(array("id" => intval($data['id'])), $data);

        if (!empty($id)) {
            return $id;
        }

        return -2;
    }

    /**
     * 时间转换
     * @param array $data
     * @return array
     */
    public function wordTime($data)
    {
        if (empty($data)) {
            return false;
        }
        $gzh_host_url = Config::getField('config', 'gzh_host_url');
        foreach ($data as &$v) {
            $time = $v['create_time'];
            $int = time() - $time;
            if ($int <= 2) {
                $v['create_time'] = sprintf('刚刚', $int);
            } elseif ($int < 60) {
                $v['create_time'] = sprintf('%d秒前', $int);
            } elseif ($int < 3600) {
                $v['create_time'] = sprintf('%d分钟前', floor($int / 60));
            } elseif ($int < 86400) {
                $v['create_time'] = sprintf('%d小时前', floor($int / 3600));
            } elseif ($int < 604800) {
                $v['create_time'] = sprintf('%d天前', floor($int / 86400));
            } elseif ($int < 2592000) {
                $v['create_time'] = sprintf('%d周前', floor($int / 604800));
            } elseif ($int < 31104000) {
                $v['create_time'] = sprintf('%d月前', floor($int / 2592000));
            } else {
                $v['create_time'] = "1年前";
            }
            $v['url'] = $gzh_host_url . '/article/art_view/id/' . $v['wid'];
        }
        return $data;
    }
}