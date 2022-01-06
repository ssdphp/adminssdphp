<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/23
 * Time: 11:06
 */

namespace App\Api\Controller;

use App\Api\Controller\Common\CommonController;
use SsdPHP\Http\Response;
use App\Api\Model\Adv;
use App\Api\Model\Config;
use SsdPHP\Core\Config as newConfig;
use App\Api\Model\Article_cat;
use App\Api\Model\Article_list;
use App\Api\Model\Account_Total_All;
use App\Api\Model\Account_Total_Today;
use App\Api\Model\Account_Total_Viewlog;
use App\Api\Model\Account_Total_Viewlog_Day;
use App\Api\Model\Account_Article_List;
use App\Api\Model\Account_Card;
use SsdPHP\Http\Input;
use App\Common\Wxsdk\Wxsdk;
use App\Common\Tool\Functions;
use SsdPHP\SsdPHP;
use App\Api\Model\Account;
use SsdPHP\Session\Session;
use App\Api\Model\Account_vip;

class Cindex extends CommonController
{
    //干货
    public function c_index()
    {

        $adv = new Adv();
        $config = new Config();
        $article_cat = new Article_cat();

        $lunbo = $adv->getAll(array('status' => 1, 'class' => 0), array('pic', 'herf', 'type'));
        $shortcut = $config->getAll(array('key' => 'Config_shortcut'), array('value'));
        $video = $config->getAll(array('key' => 'Config_video'), array('value'));
        $classify = $article_cat->getAll(array('status' => 0), array('name', 'cid' => 'id'))->items;

        $c = array(
            'name' => '关注',
            'cid' => "-1"
        );
        array_unshift($classify, $c);
        $fast = json_decode($shortcut[0]['value'], true);
        $video_course = json_decode($video[0]['value'], true);

        $data = array();
        $data['lunbotu'] = $lunbo;
        $data['fast'] = $fast['shortcut'];
        $data['classify'] = $classify;
        $data['video'] = $video_course['video'];

        $course_list = array(
            'fabu' => array(
                'content' => '文章发布',
                'desc' => '指导你如何操作文章发布模块',
                'url' => 'http://static.tripbe.com/videofiles/20121214/9533522808.f4v.mp4'
            ),
            'tuwen' => array(
                'content' => '文章管理',
                'desc' => '指导你如何操作文章管理',
                'url' => 'http://static.tripbe.com/videofiles/20121214/9533522808.f4v.mp4'
            ),
            'tongji' => array(
                'content' => 'AI统计讲解',
                'desc' => '指导快速理解本软件AI统计功能',
                'url' => 'http://static.tripbe.com/videofiles/20121214/9533522808.f4v.mp4'
            ),
        );

        $data['course_list'] = $course_list;

        Response::apiJsonResult($data, 1);
    }

    //首页文章展示
    public function c_article()
    {

        $cid = Input::request('cid', 0, 'intval');
        $page = Input::request('page', 1, 'intval');
        $pagesize = Input::request('pagesize', 10, 'intval');

        if (empty($cid)) {
            Response::apiJsonResult([], 0);
        }

        $article_list = new Article_list();

        if ($cid == -1) {
            $article_cat = new Article_cat();
            //$c=$article_cat->getAll(array('status' => 0), array('id'), 'id desc');
            $cat = $article_cat->getList(array('status' => 0), $page, $pagesize, array('id'), 'id desc')->items;

            if (empty($cat)) {
                Response::apiJsonResult(array(), 1, '暂无数据');
            }

            foreach ($cat as $k => $v) {
                $article[$k] = $article_cat->findOne(array('id' => $v['id']), array('name'));
                $article[$k]['article'] = $article_list->getList(array('cid' => $v['id'], 'status' => 1), 0, 4, array('title', 'img', 'wid' => 'id', 'cid', 'read_num', 'create_time'));
                if (empty($article[$k]['article']->items)) {
                    $article[$k]['article']->items = [];
                } else {
                    //时间转换
                    $article[$k]['article']->items = $article_list->wordTime($article[$k]['article']->items);
                }

            }
            $d['list'] = $article;


            if (empty($d)) {
                Response::apiJsonResult([], 0);
            }
        } else {
            $article = $article_list->getList(array('status' => 1, 'cid' => $cid), $page, $pagesize, array('title', 'img', 'wid' => 'id', 'cid', 'read_num', 'create_time'), 'id desc');
            if (empty($article->items)) {
                $article->items = [];
            } else {
                //时间转换
                $article->items = $article_list->wordTime($article->items);
            }

            $d['list'] = $article;

            if (empty($article->items)) {
                Response::apiJsonResult([], 1);
            }
        }

        Response::apiJsonResult($d, 1);
    }

    //视频教程
    public function c_course_list()
    {

        $data = array(
            0 => array(
                'content' => '名片编辑',
                'desc' => '指导你如何操作名片模块',
                'url' => 'http://static.tripbe.com/videofiles/20121214/9533522808.f4v.mp4'
            ),
            1 => array(
                'content' => '名片夹操作',
                'desc' => '指导你如何操作名片夹模块',
                'url' => 'http://static.tripbe.com/videofiles/20121214/9533522808.f4v.mp4'
            ),
            2 => array(
                'content' => '文章发布管理',
                'desc' => '指导你如何操作文章发布管理模块',
                'url' => 'http://static.tripbe.com/videofiles/20121214/9533522808.f4v.mp4'
            ),
            3 => array(
                'content' => 'AI统计讲解',
                'desc' => '指导快速理解本软件AI统计功能',
                'url' => 'http://static.tripbe.com/videofiles/20121214/9533522808.f4v.mp4'
            ),
        );

        if (!empty($data)) {
            Response::apiJsonResult($data, 1);
        }

        Response::apiJsonResult([], 0);

    }

    //客户追踪
    public function c_auth_index_total()
    {
        $page = Input::request('page', 1, 'intval');
        $os = Input::request('os', 0, 'intval');
        $pagesize = 10;
        $mark = Input::request('mark', 0, 'intval');
        $keyword = Input::request('keyword', '');

        $Account_Total_All = new Account_Total_All();
        $Account_Total_Today = new Account_Total_Today();
        $Account_Card = new Account_Card();
        $Account_Article_List = new Account_Article_List();

        $cond = array(
            'uid' => UID,
            'is_del' => 0
        );

        if (!empty($keyword)) {
            $cond[] = "title like '%$keyword%'";
        }

        $alist = $Account_Article_List->getList($cond, $page, $pagesize, array('eid' => 'id', 'uid', 'cid', 'status', 'title', 'share_pic', 'desc', 'class'), "id desc");

        foreach ($alist->items as &$v) {
            $v['total_view_count'] = $Account_Total_All->findone(array('uid' => UID, 'ext_id' => $v['eid']), array('total_view_count'))['total_view_count'] ?? "0";
            $v['today'] = $Account_Total_Today->findone(array('uid' => UID, 'ext_id' => $v['eid'], 'update_time >= ' . strtotime(date("Ymd 00:00:00"))), array('sum(total_view_count) as num'))['num'] ?? "0";
            $v['total_type'] = 2;
            $v['ai_class'] = 'wz';
            $v['class'] = empty($v['class']) ? 1 : $v['class'];
        }

        if ($mark == 1) {
            Response::apiJsonResult(array(
                'alist' => $alist
            ), 1);
        }

        //总访问次数
        $all = $Account_Total_All->findone(array('uid' => UID), array('sum(total_view_count) as num'))['num'] ?? "0";

        //今日访问次数
        $today_all = $Account_Total_Today->findone(array('uid' => UID, 'update_time' . ">" . strtotime(date('Ymd 00:00:00'))), array('sum(total_view_count) as num'))['num'] ?? "0";

        $cardinfo = $Account_Card->findone(array('uid' => UID, 'is_used' => 1), array('mid' => 'id', 'uid', 'info_headimg', 'info_name'));

        //名片总访问次数
        $cardinfo['total_view_count'] = $Account_Total_All->findone(array('uid' => UID, 'ext_id' => $cardinfo['mid']), array('total_view_count'))['total_view_count'] ?? "0";
        $cardinfo['total_type'] = 1;
        $cardinfo['ai_class'] = 'mp';

        //名片今日访问量
        $cardinfo['today'] = $Account_Total_Today->findone(array('uid' => UID, 'ext_id' => $cardinfo['mid'], 'update_time >= ' . strtotime(date("Ymd 00:00:00"))), array('sum(total_view_count) as num'))['num'] ?? "0";;

        //公告
        $adv = new Adv();

        $advlist = $adv->getAll(array('status' => 1, 'type' => 1), array('title'));

        foreach ($advlist as $k => $va) {
            $notice[] = $va['title'];

        }

        $url = newConfig::getField('config', 'gzh_host_url');

        if ($os == 2) {
            $cat = array(
                [
                    'title' => '发布文章',
                    'img' => $url . '/home/img/conn_img/fbwz.png',
                    'type' => 'fabuwenzhang',
                ], [
                    'title' => '文章转换',
                    'img' => $url . '/home/img/conn_img/wzzh.png',
                    'type' => 'wenzhangzhuanhuan',
                ], [
                    'title' => '谁看过我',
                    'img' => $url . '/home/img/conn_img/seewho.png',
                    'type' => 'sheikanguowo',
                ], [
                    'title' => '我看过谁',
                    'img' => $url . '/home/img/conn_img/whosee.png',
                    'type' => 'wokanguoshei',
                ]
            );
        } else {
            $cat = array(
                [
                    'title' => '发布文章',
                    'img' => $url . '/home/img/conn_img/fbwz.png',
                    'type' => 'fabuwenzhang',
                ], [
                    'title' => '文章转换',
                    'img' => $url . '/home/img/conn_img/wzzh.png',
                    'type' => 'wenzhangzhuanhuan',
                ], [
                    'title' => '谁看过我',
                    'img' => $url . '/home/img/conn_img/seewho.png',
                    'type' => 'sheikanguowo',
                ], [
                    'title' => '我看过谁',
                    'img' => $url . '/home/img/conn_img/whosee.png',
                    'type' => 'wokanguoshei',
                ]
            );
        }


        Response::apiJsonResult(array(
            'all' => $all,
            'today_all' => $today_all,
            'cat' => $cat,
            'cardinfo' => $cardinfo,
            'alist' => $alist,
            'notice' => $notice
        ), 1);

    }

    //客户追踪
    public function c_auth_index_total_two()
    {
        $page = Input::request('page', 1, 'intval');
        $os = Input::request('os', 0, 'intval');
        $pagesize = 10;
        $mark = Input::request('mark', 0, 'intval');
        $keyword = Input::request('keyword', '');

        $Account_Total_All = new Account_Total_All();
        $Account_Total_Today = new Account_Total_Today();
        $Account_Card = new Account_Card();
        $Account_Article_List = new Account_Article_List();

        $cond = array(
            'uid' => UID,
            'is_del' => 0
        );

        if (!empty($keyword)) {
            $cond[] = "title like '%$keyword%'";
        }

        $alist = $Account_Article_List->getList($cond, $page, $pagesize, array('eid' => 'id', 'uid', 'cid', 'status', 'title', 'share_pic', 'desc', 'class'), "id desc");

        foreach ($alist->items as &$v) {
            $v['total_view_count'] = $Account_Total_All->findone(array('uid' => UID, 'ext_id' => $v['eid']), array('total_view_count'))['total_view_count'] ?? "0";
            $v['today'] = $Account_Total_Today->findone(array('uid' => UID, 'ext_id' => $v['eid'], 'update_time >= ' . strtotime(date("Ymd 00:00:00"))), array('sum(total_view_count) as num'))['num'] ?? "0";
            $v['total_type'] = 2;
            $v['ai_class'] = 'wz';
            $v['class'] = empty($v['class']) ? 1 : $v['class'];
            $v['label_str'] = $Account_Article_List->class[$v['class']]['str'];
            $v['label_color'] = $Account_Article_List->class[$v['class']]['color'];
            $v['label_bg_color'] = $Account_Article_List->class[$v['class']]['bg_color'];
            $Account_Total_Viewlog_Day = new Account_Total_Viewlog_Day();
            $_ulist = $Account_Total_Viewlog_Day->getList(array("uid" => UID, 'total_type' => 2, 'ext_id' => $v['eid']), 1, 5, array('vuid'), "create_time desc")->items;
            $ulist = array();
            if (!empty($_ulist)) {
                //$aeskey= newConfig::getField('config','UID_AES_KEY');
                foreach ($_ulist as $_vudata) {
                    if (!empty($_vudata['vuid'])) {
                        $Account = new Account();
                        $tmp = $Account->findone(array("id" => $_vudata['vuid']), array('info_headimg' => 'wx_headimg'));
                        if (!empty($tmp)) {
                            //$tmp['uid']=Functions::opensslEncrypt($tmp['uid'],$aeskey);
                            if (!empty($tmp)) {
                                $ulist[] = $tmp['info_headimg'];
                            }
                        }

                    }

                }
            }
            $v['ulist'] = $ulist;
        }

        if ($mark == 1) {
            Response::apiJsonResult(array(
                'alist' => $alist
            ), 1);
        }

        //总访问次数
        $all = $Account_Total_All->findone(array('uid' => UID), array('sum(total_view_count) as num'))['num'] ?? "0";

        //今日访问次数
        $today_all = $Account_Total_Today->findone(array('uid' => UID, 'update_time' . ">" . strtotime(date('Ymd 00:00:00'))), array('sum(total_view_count) as num'))['num'] ?? "0";

        //公告
        $adv = new Adv();

        $advlist = $adv->getAll(array('status' => 1, 'type' => 1), array('title'));

        foreach ($advlist as $k => $va) {
            $notice[] = $va['title'];

        }

        $url = newConfig::getField('config', 'gzh_host_url');

        if ($os == 2) {
            $cat = array(
                [
                    'title' => '短动态',
                    'img' => $url . '/home/img/conn_img/duandongtai.png',
                    'type' => 'duandongtai',
                ], [
                    'title' => '长动态',
                    'img' => $url . '/home/img/conn_img/changdongtai.png',
                    'type' => 'fabuwenzhang',
                ], [
                    'title' => '文章转换',
                    'img' => $url . '/home/img/conn_img/wzzh.png',
                    'type' => 'wenzhangzhuanhuan',
                ], [
                    'title' => '动态管理',
                    'img' => $url . '/home/img/conn_img/seewho.png',
                    'type' => 'dongtaiguanli',
                ]
            );
        } else {
            $cat = array(
                [
                    'title' => '短动态',
                    'img' => $url . '/home/img/conn_img/duandongtai.png',
                    'type' => 'duandongtai',
                ], [
                    'title' => '长动态',
                    'img' => $url . '/home/img/conn_img/changdongtai.png',
                    'type' => 'fabuwenzhang',
                ], [
                    'title' => '文章转换',
                    'img' => $url . '/home/img/conn_img/wzzh.png',
                    'type' => 'wenzhangzhuanhuan',
                ], [
                    'title' => '动态管理',
                    'img' => $url . '/home/img/conn_img/seewho.png',
                    'type' => 'dongtaiguanli',
                ]
            );
        }


        Response::apiJsonResult(array(
            'all' => $all,
            'today_all' => $today_all,
            'cat' => $cat,
            'alist' => $alist,
            'notice' => $notice
        ), 1);

    }

    public function c_auth_url()
    {

        $url = Input::request('url', "", 'urldecode');
        $os = Input::request('os', 0, 'intval');

        if (empty($url)) {
            Response::apiJsonResult([], 0, '请填写链接地址');
        }

        $s = preg_match("/https\:\/\/mp\.weixin\.qq\.com\/s\/.*?/", $url);
        if (!$s) {
            Response::apiJsonResult([], 0, '链接有误，请查看「使用帮助」粘贴正确链接。');
        }

        $Account = new Account();
        $userinfo = $Account->findone(array('id' => UID));

        $Account_vip = new Account_vip();
        $dinfo = $Account_vip->findone(array('uid' => UID));

        if (empty($userinfo['vip_expire_time']) || $userinfo['vip_expire_time'] < time()) {
            if (!empty($dinfo['id']) && $dinfo['add_art_num'] <= 0) {
                Response::apiJsonResult(array(), 0, "转换次数已用完,请完成奖励任务获取次数");
            }
        }

        $data = Functions::PHPCURL($url)['body'];
        if (empty($data)) {
            Response::apiJsonResult([], 0, '地址有误');
        }
        preg_match("/var msg_title = [\"|'](.*?)[\"|'][\.|;]/",$data,$title);
        if (empty($title)) {
            Response::apiJsonResult([], 1, '文章标题不存在');
        }
        preg_match("/var msg_desc = [\"|'](.*?)[\"|'][\.|;]/",$data,$desc);
        if (empty($desc)) {
            Response::apiJsonResult([], 1, '文章描述不存在');
        }
        $gzh_host_url = newConfig::getField('config', 'gzh_host_url');
        preg_match("/var msg_cdn_url = \"(.*?)\";/", $data, $thumb);
        if (empty($thumb) || empty($thumb[1])) {
            Response::apiJsonResult([], 1, '地址有误');
        } else {
            $thumbdata = Functions::PHPCURL($thumb[1], array(), array(
                'Referer' => $url,
                'User-Agent' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36",
            ));
            if ($thumbdata['httpcode'] == 200) {
                $dir = "/upload/wximg/" . date("Y") . "/" . date("Ym") . "/";

                $filedir = SsdPHP::getAppDir() . "/www" . $dir;


                $filename = date("YmdHis") . mt_rand() . ".jpeg";
                if (!is_dir($filedir)) {
                    $s = mkdir($filedir, 0777, true);
                }
                $s = file_put_contents($filedir . $filename, $thumbdata['body'], LOCK_EX);
                $thumb[1] = $gzh_host_url . $dir . $filename;
            }
        }


        $p = "/<div class=\"rich_media_content.*?id=\"js_content\".*?>([\s\S]*?)<\/div>/";
        preg_match($p, $data, $content);
        if (!empty($content[1])) {
            $content[1] = preg_replace("/^([\s\S\n]*?)</", "<", $content[1]);
            $html = preg_replace_callback_array(array(
                //替换图片
                "~<img.*?data-src=\"(.*?)\".*?>~" => function ($matches) use ($url, $gzh_host_url) {

                    if (!empty($matches[1])) {
                        $imgurl = $matches[1];
                        $data = Functions::PHPCURL($imgurl, array(), array(
                            'Referer' => $url,
                            'User-Agent' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36",
                        ));
                        if ($data['httpcode'] == 200) {

                            $dir = "/upload/wximg/" . date("Y") . "/" . date("Ym") . "/";

                            $filedir = SsdPHP::getAppDir() . "/www" . $dir;


                            $filename = date("YmdHis") . mt_rand() . ".jpeg";
                            if (!is_dir($filedir)) {
                                $s = mkdir($filedir, 0777, true);
                            }
                            $s = file_put_contents($filedir . $filename, $data['body'], LOCK_EX);
                            $imgurl = $gzh_host_url . $dir . $filename;

                        }
                        $p = "/(data-src)=\"(.*?)\"/";
                        $a = preg_replace($p, "src=\"$imgurl\"", $matches[0]);
                        $a = str_replace("!important","",$a);
                        return $a;
                    }
                },
                "~<iframe.*?data-cover=\"(.*?)\".*?data-src=\"(.*?)\".*?\><\/iframe>~" => function ($matches) use ($url, $gzh_host_url) {

                    if (!empty($matches[1]) || !empty($matches[2])) {
                        parse_str($vurl = htmlspecialchars_decode($matches[2] ?? ""), $ret);


                        if (stripos("v.qq.com", $vurl) === false) {
                            $vqq_json_url = "https://mp.weixin.qq.com/mp/videoplayer?action=get_mp_video_play_url&preview=0&__biz=&mid=&idx=&vid={$ret['vid']}&uin=&key=&pass_ticket=&wxtoken=&appmsg_token=&x5=0&f=json";
                            $json = Functions::PHPCURL($vqq_json_url, array(), array(
                                'Referer' => $url,
                                'User-Agent' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36",
                            ));


                            if (!empty($json['body'])) {
                                $_json = json_decode($json['body'], true);
                                if (!empty($_json['url_info']) && !empty($_json['url_info'][1])) {
                                    $_vurl = $_json['url_info'][1]['url'];
                                }
                                $poster = urldecode($matches[1] ?? "");
                                if ($poster) {
                                    $dir = "/upload/wximg/" . date("Y") . "/" . date("Ym") . "/";

                                    $filedir = SsdPHP::getAppDir() . "/www" . $dir;


                                    $filename = date("YmdHis") . mt_rand() . ".jpeg";
                                    if (!is_dir($filedir)) {
                                        $s = mkdir($filedir, 0777, true);
                                    }
                                    $thumbdata = Functions::PHPCURL($poster, array(), array(
                                        'Referer' => $url,
                                        'User-Agent' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36",
                                    ));
                                    $s = file_put_contents($filedir . $filename, $thumbdata['body'], LOCK_EX);

                                    $poster = $gzh_host_url . $dir . $filename;
                                }
                                return "<video controls  preload=\"auto\"
            webkit-playsinline=\"true\"
            playsinline=\"true\"
            x-webkit-airplay=\"allow\"
            x5-video-player-type=\"h5\"

            playsinline
            style=\"object-fit:fill\" src=\"{$_vurl}\"  poster='$poster'></video>";
                            }

                            return $matches[0];
                        }
                        $url = "src=\"https://v.qq.com/txp/iframe/player.html?vid={$ret['vid']}\"";

                        $p = "/(data-src)=\"(.*?)\"/";
                        $a = preg_replace($p, $url, $matches[0]);

                        return $a;
                    }
                    return $matches[0];


                },
                "~background-image: url\((.*?)\)~" => function ($matches) use ($url, $gzh_host_url) {

                    if (!empty($matches[1])) {
                        $matches[1] = str_replace("\"", "", htmlspecialchars_decode($matches[1]));
                        $data = Functions::PHPCURL($matches[1], array(), array(
                            'Referer' => $url,
                            'User-Agent' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36",
                        ));
                        if ($data['httpcode'] == 200) {
                            $dir = "/upload/wximg/" . date("Y") . "/" . date("Ym") . "/";

                            $filedir = SsdPHP::getAppDir() . "/www" . $dir;


                            $filename = date("YmdHis") . mt_rand() . ".jpeg";
                            if (!is_dir($filedir)) {
                                $s = mkdir($filedir, 0777, true);
                            }
                            $s = file_put_contents($filedir . $filename, $data['body'], LOCK_EX);
                            $imgurl = $gzh_host_url . $dir . $filename;

                            if ($s) {

                                return "background-image: url($imgurl)";
                            }

                        }
                        Response::apiJsonResult([], 102, '转换失败');

                    }
                }
            ), $content[1]);

            $Account_Article_List = new Account_Article_List();
            $info = $Account_Article_List->findone(array('uid' => $userinfo['id'], 'fromurl' => $url), array("*"));

            if (empty($info['id'])) {

                $id = $Account_Article_List->add(array(
                    'cid' => 0,
                    'uid' => $userinfo['id'] ?? 0,
                    'class' => 2,
                    'status' => 1,
                    'update_time' => time(),
                    'create_time' => time(),
                    'title' => $title[1] ?? "",
                    'desc' => $desc[1] ?? "",
                    'fromurl' => $url,
                    'content' => ($html),
                    'share_pic' => $thumb[1] ?? "",
                ));

            } else {
                $Account_Article_List->updateInfo(array('id' => $info['id']), array(
                    'cid' => 0,
                    'uid' => $userinfo['id'] ?? 0,
                    'status' => 1,
                    'update_time' => time(),
                    'is_del' => 0,
                    'title' => $title[1] ?? "",
                    'desc' => $desc[1] ?? "",
                    'content' => ($html),
                    'share_pic' => $thumb[1] ?? "",
                ));
                $id = $info['id'];
                $mark = 2;
            }

            if ($id) {
                $key = newConfig::getField('config', 'UID_AES_KEY');
                $jid = Functions::opensslEncrypt($id, $key);
                $share_url = $gzh_host_url . "/article/auth_sharetw/id/{$jid}.html";
            } else {
                //更新或添加失败
                Response::apiJsonResult([], 102, '转换失败');
            }

        } else {
            Response::apiJsonResult([], 103, '转换失败');
        }

        //发布检测
        //if (empty($userinfo['vip_expire_time']) || $userinfo['vip_expire_time'] < time()) {


        //    $today = strtotime(date("Y-m-d"), time());
        //    $num = newConfig::getField('config', 'morenfabucishu');

        //    if (!empty($dinfo['id'])) {
        //        if ($dinfo['add_art_time'] < $today) {
        //            $d = array(
        //                'add_art_num' => $num - 1,
        //                'add_art_time' => time(),
        //                'share_num' => 0,
        //                'share_time' => time(),
        //                'update_time' => time()
        //            );
        //        } else {
        //            $d = array(
        //                'add_art_num' => $dinfo['add_art_num'] - 1,
        //                'add_art_time' => time(),
        //                'update_time' => time()
        //            );
        //        }
        //        $u = $Account_vip->updateInfo(array('id' => $dinfo['id'], 'uid' => UID), $d);
        //    } else {
        //        $d = array(
        //            'uid' => UID,
        //            'add_art_num' => $num - 1,
        //            'add_art_time' => time(),
        //            'share_time' => time(),
        //            'create_time' => time()
        //        );
        //        $r = $Account_vip->add($d);
        //    }
        //}


        $s = Wxsdk::sendKFmsg($a = json_encode(array(
            "touser" => UID,
            "msgtype" => "news",
            "news" => array(
                "articles" => array(
                    array(
                        "title" => $title[1] ?? "",
                        "description" => $desc[1] ?? "",
                        "url" => $share_url ?? "",
                        "picurl" => $thumb[1] ?? ""
                    )
                )
            )
        ), JSON_UNESCAPED_UNICODE));

        $gzh_host_url = newConfig::getField('config', 'gzh_host_url');
        $userLoginInfo = Session::get("UserAuthSession");

        Response::apiJsonResult(array(
            'wid' => $id,
            'total_type' => 2,
            'ai_class' => 'wz',
            'title' => $title[1],
            'desc' => $desc[1],
            //'content' => $html,
            'share_pic' => $thumb[1],
            'create_time_str' => date('Y-m-s h:i:s', time()),
            'status_str' => $Account_Article_List->status[1]['str'],
            'status_color' => $Account_Article_List->status[1]['color'],
            'share_url' => $share_url,
            'url' => $gzh_host_url . '/article/auth_paydetail/id/' . $id . '?token=' . $userLoginInfo['token'] . '&os=' . $os,
            'mark' => $mark ?? '',
            //'release_num' => $d['add_art_num'] ?? 0
        ), 1, '转换成功');

    }

    public function c_course()
    {
        $this->display('index/appcourse');
    }

}