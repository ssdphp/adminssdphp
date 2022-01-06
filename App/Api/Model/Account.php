<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Api\Model;

use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Hleper\Hleper;
use SsdPHP\Pulgins\Http\Input;
use SsdPHP\SsdPHP;
use App\Common\Wxsdk\Wxsdk;
use SsdPHP\Cache\Cache;

class Account extends Model
{


    /**
     * 通过id获取一张一名片
     * @param $id
     * @param array $feild
     * @return array|mixed
     */
    public function ByUidGetInfo($id, $feild = ["*"])
    {
        if (empty($id)) {
            return [];
        }
        $cond = ['id' => $id];
        $ret = $this->selectOne($cond, $feild);
        if (!empty($ret)) {
            return $ret;
        }
        return [];
    }

    /**
     * 获取列表
     * @param array $cond
     * @param int $page
     * @param int $pagesize
     * @param array $field
     * @param string $order
     * @return mixed
     */
    public function getList($cond = array(), $page = 1, $pagesize = 10, $field = array("*"), $order = "id desc")
    {

        $a = $this->setPage($page, $pagesize)
            ->select($cond, $field, "", $order);

        return $a;
    }


    /**
     * 登录处理，微信登录，QQ登录
     * @param array $data 基础数据
     * @return array
     */
    public function ForAutoLogin($data = array())
    {
        //手机登录处理
        if ($data['login_type'] == 2) {
            return $this->ByPhonelogin($data);
        }
        //微信登录处理
        if ($data['login_type'] == 1) {
            return $this->wxDataAutoReg($data);
        }

    }

    public function autoCheckAccount($unionid, $login_type)
    {
        if (empty($unionid) || empty($login_type)) {
            return [];
        }
        if ($login_type == 1) {
            $cond = ['wx_unionid' => $unionid];
        } else if ($login_type == 2) {
            $cond = ['wx_unionid' => $unionid];
        } else {
            return [];
        }

        $ret = $this->selectOne($cond, ["*"]);
        if (!empty($ret)) {
            return $ret;
        }
        return [];
    }


    /**
     * 通过微信的unionid检查是否已经是注册过了
     * @author  xiaohuihui  <xzh_tx@163.com>
     * @param $Unionid
     * @return array
     */
    public function CheckWeixinUserExistByUnionid($Unionid)
    {
        if (empty($Unionid)) {
            return [];
        }
        $cond = ['wx_unionid' => $Unionid];
        $ret = $this->selectOne($cond, ["*"]);
        if (!empty($ret)) {
            return $ret;
        }
        return [];
    }

    /**
     * 检查手机号是否已经注册
     * @param $Unionid
     * @return array
     */
    public function CheckUserExistByPhone($phone)
    {
        if (empty($phone)) {
            return [];
        }
        $cond = ['phone' => $phone];
        $ret = $this->selectOne($cond, ["*"]);
        if (!empty($ret)) {
            return $ret;
        }
        return [];
    }

    /**
     * 登录
     * @param $Unionid
     * @return array
     */
    public function ByPhonelogin($data)
    {
        if (empty($data)) {
            return -1;
        }

        $userinfo = $this->findone(array('phone' => $data['phone']));

        if (!empty($userinfo['id'])) {

            if ($userinfo['pwd'] == $data['pwd']) {
                return $userinfo;
            } else {
                return 101;
            }

        }

        return 102;
    }


    /**
     * phone注册
     * @param $regData
     * @return mixed
     */
    public function ByPhoneReg($regData)
    {
        if (empty($regData)) {
            return -1;
        }

        //直接通过phone直接注册账号
        $reg_device = "Phone";
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $reg_device = mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 255);
        }
        $addData = array(
            'wx_name' => 'user_' . time() . mt_rand(10, 99),
            'wx_headimg' => "http://static.qiniu-static.65141.com/Fusx1gakFNYWZLESKHPM6DxLNTSb",
            'wx_unionid' => $regData['phone'],
            'wx_city' => $regData['city'] ?? '',
            'wx_province' => $regData['province'] ?? '',
            'wx_country' => $regData['country'] ?? '',
            'wx_sex' => $regData['sex'] ?? '0',

            'did' => 'ff1f84b',
            'reg_device' => $reg_device,
            'rj' => '273d7a9',
            'os' => $regData['os'] ?? '',
            'reg_ip' => Functions::getIP(),
            'phone' => $regData['phone'],
            'pwd' => $regData['pwd'],
            'pwd_str' => md5($regData['pwd']),
            'create_time' => time()
        );
        $uid = $this->insert($addData);
        if ($uid < 1) {
            return -3;
        }
        $Article_yindao_list = new Article_yindao_list();
        $Account_Article_List = new Account_Article_List();

        $yindaoarticle = $Article_yindao_list->findone(array('status' => 1));

        $data = array(
            'uid' => $uid,
            'status' => 1,
            'title' => $yindaoarticle['title'],
            'desc' => $yindaoarticle['desc'],
            'content' => $yindaoarticle['content'],
            'share_pic' => $yindaoarticle['img'],
            'create_time' => time()
        );

        $r = $Account_Article_List->add($data);

        if (empty($r)) {
            return -3;
        }

        $addData['id'] = $uid;
        $addData['eid'] = dechex($uid);
        //设置部分名片个人信息
        $Account_Card = new Account_Card();
        $Account_Card_Qiye = new Account_Card_Qiye();
        // define("DEBUG",true);
        $cid = $Account_Card->add(array(
            'uid' => $uid,
            'is_used' => 1,
            'info_name' => $addData['wx_name'],
            'info_sex' => $addData['wx_sex'],
            'info_headimg' => $addData['wx_headimg'],
            'create_time' => INIT_TIME,
        ));
        if ($cid > 0) {
            $s = $Account_Card_Qiye->add(array(
                'uid' => $uid,
                'cid' => $cid,
                'create_time' => INIT_TIME,
            ));
        }

        //设置部分名片企业信息
        $s = $this->updateInfo(array('id' => $uid), array('eid' => $addData['eid']));
        return $addData;

    }


    /**
     * 微信用户信息自动注册。
     * @param array $regData
     * @return mixed
     * //{"openid":"oc4qK1EMsbySrCdTQSA4ThBZmr60","nickname":"go-","sex":1,"language":"zh_CN","city":"","province":"","country":"中国","headimgurl":"http:\/\/thirdwx.qlogo.cn\/mmopen\/vi_32\/Q0j4TwGTfTIkLFmZdzwjUicxS0V9IvxKQn8ISmUjibTW9CTnQjQ65noFxC8fRzHq51Aed4zJj4Ey8YHojiabEZ2gg\/132","privilege":[],"unionid":"oInWl0Zw0l8C6WMPFlHkaSf74RBQ"}
     */
    public function wxDataAutoReg($regData = array())
    {

        if (empty($regData)) {
            return -1;
        }
        $reg_device = "weixin";
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $reg_device = mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 255);
        }
        $addData = array(
            'wx_unionid' => $regData['unionid'] ?? '',
            //app登录取消openid设置
            //'wx_openid' => $regData['openid'] ?? '',
            'wx_name' => $regData['nickname'] ?? '',
            'wx_headimg' => $regData['headimgurl'] ?? '',
            'wx_city' => $regData['city'] ?? '',
            'wx_province' => $regData['province'] ?? '',
            'wx_country' => $regData['country'] ?? '',
            'wx_sex' => $regData['sex'] ?? '0',

            'did' => $regData['did'] ?? '',
            'reg_device' => $reg_device,
            'rj' => $regData['rj'] ?? '',
            'os' => $regData['os'] ?? '',
            'reg_ip' => Functions::getIP(),
        );

        $u_ret = $this->CheckWeixinUserExistByUnionid($regData['unionid']);

        //{{{用户存在不能注册
        if (!empty($u_ret['id'])) {
            if (isset($addData['did']))
                unset($addData['did']);
            if (isset($addData['reg_device']))
                unset($addData['reg_device']);
            if (isset($addData['rj']))
                unset($addData['rj']);
            if (isset($addData['os']))
                unset($addData['os']);
            if (isset($addData['reg_ip']))
                unset($addData['reg_ip']);
            $addData['update_time'] = INIT_TIME;
            $s = $this->updateInfo(array('id' => $u_ret['id']), $addData);
            return $u_ret;
        }

        $addData['create_time'] = INIT_TIME;
        //添加账号数据
        $uid = $this->insert($addData);
        if ($uid < 1) {
            return -2;
        }

        $addData['id'] = $uid;
        $addData['eid'] = dechex($uid);
        //设置部分名片个人信息
        $Account_Card = new Account_Card();
        $Account_Card_Qiye = new Account_Card_Qiye();
        // define("DEBUG",true);
        $cid = $Account_Card->add(array(
            'uid' => $uid,
            'is_used' => 1,
            'info_name' => $addData['wx_name'],
            'info_sex' => $addData['wx_sex'],
            'info_headimg' => $addData['wx_headimg'],
            'create_time' => INIT_TIME,
        ));
        if ($cid > 0) {
            $s = $Account_Card_Qiye->add(array(
                'uid' => $uid,
                'cid' => $cid,
                'create_time' => INIT_TIME,
            ));
        }

        //设置部分名片企业信息
        $s = $this->updateInfo(array('id' => $uid), array('eid' => $addData['eid']));
        return $addData;

    }

    /**
     * 获取微信设备
     */
    public function wxequipment($data)
    {

        if (empty($data)) {
            return -1;
        }

        $Account_Wxyyy_Deviceid = new Account_Wxyyy_Deviceid();

        //查询超过2天未使用的设备
        $wxDevice = $Account_Wxyyy_Deviceid->findone(array(time() . '-update_time>172800'), array("*"));
        if (empty($wxDevice['id'])) {
            //没有就查询是否有未使用设备
            $wxDevice = $Account_Wxyyy_Deviceid->findone(array('uid' => ''), array("*"));
            if (empty($wxDevice['id'])) {
                return -2;
            }
        }

        //并发控制
        $Cache = Cache::getInstance();

        //设置锁的key
        $lockkey = Config::getField('config', 'lock_addDevice') . $wxDevice['id'];

        $locked = $Cache->SETNX($lockkey, time());

        //获取锁失败返回
        if ($locked == 0 || $locked == false) {
            return 101;
        }

        //10秒后自动释放锁
        $Cache->expire($lockkey, 10);

        //配置设备和页面关联
        $binddata = array(
            'device_identifier' => array(
                'uuid' => $wxDevice['uuid'],
                'major' => (int)$wxDevice['major'],
                'minor' => (int)$wxDevice['minor'],
            ),
            'page_ids' => [(int)$data['pageid']]
        );
        $jsonbinddata = json_encode($binddata);
        $bindPage = Wxsdk::gzh_DeviceBindPage($jsonbinddata);

        //配置成功更新设备信息返回设备数据
        if ($bindPage == 1) {
            $s = $Account_Wxyyy_Deviceid->updateInfo(array('id' => $wxDevice['id']), array('uid' => $data['uid'], 'update_time' => time()));
            $binddata['device_identifier']['update_time'] = time() + 172800;
            return $binddata['device_identifier'];
        }
        return -4;
    }

    /**
     * 编辑-更新摇一摇页面
     * @param $page
     * @return array
     */
    public function revisePage($page)
    {
        if (empty($page)) {
            return -1;
        }

        //获取常规头像120*120
        $imgurl = $this->DownloadEditimg($page['info_headimg']);
        //上传头像图片素材
        $media = Wxsdk::gzh_mediaAdd($imgurl);
        if ($media < 0) {
            return -2;
        }

        if (empty($page['page_id'])) {

            //生成用户页面
            $d = array(
                'title' => $page['info_name'],
                'description' => $page['qy_zhiwei'],
                'page_url' => $page['page_url'],
                'comment' => $page['qy_name'],
                'icon_url' => $media
            );
            $jsondata = json_encode($d);
            $pageid = Wxsdk::gzh_PageAdd($jsondata);

            if ($pageid < 0) {
                return -3;
            }

            unlink($imgurl);
            $Account_Card = new Account_Card();
            $r = $Account_Card->updateInfo(array('uid' => $page['uid']), array('pageid' => $pageid));

            return $pageid;
        }

        //更新用户页面
        $revisePage = array(
            'page_id' => (int)$page['page_id'],
            'title' => $page['info_name'],
            'description' => $page['qy_zhiwei'],
            'page_url' => $page['page_url'],
            'comment' => $page['qy_name'],
            'icon_url' => $media
        );


        $jsondata = json_encode($revisePage);
        $ret = Wxsdk::gzh_PageEdit($jsondata);

        if ($ret < 0) {
            return -4;
        }
        unlink($imgurl);

        return $ret;
    }

    /**
     * 下载编辑头像图片
     * @param string $url 图片的url地址
     */
    public function DownloadEditimg($url)
    {

        if (empty($url)) {
            return false;
        }
        $folder = SsdPHP::getAppDir() . '/www/upload/appimg';
        $picname = date("YmdHis") . mt_rand(1000, 9999) ;

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $json = Functions::PHPCURL($url, array(), array(
            'Referer' => $url,
            'User-Agent' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36",
        ));
        $img = !empty($json['body']) ? $json['body'] : "";
        if (!$img) {
            return false;
        }
        $type=getimagesizefromstring($img);
        $ext = explode("/", $type['mime']??"");
        $picname = $picname.".". ($ext[1]??"png");

        $imgfilepath = $folder . DIRECTORY_SEPARATOR . $picname;
        $b = file_put_contents($imgfilepath,$img);
	
        if ($b){
            $tmpname = $folder . '/' . $picname;
            $bool = $this->resize_image($picname, $tmpname, $xmax = 120, $ymax = 120);
            if($bool){
	    	return $tmpname;
	    }
        }
        return false;
        //$fp2 = fopen($folder . DIRECTORY_SEPARATOR . $picname, "a");
        //fwrite($fp2, $img);
        //fclose($fp2);

    }

    /**
     * 重置图片文件大小
     * @param string $filename 图片名称
     * @param string $tmpname 文件路径
     * @param int $xmax 修改后最大宽度
     * @param int $ymax 修改后最大高度
     */
    public function resize_image($filename, $tmpname, $xmax, $ymax)
    {
    	//header("Content-type: image/png");
        $ext = explode(".", $filename);
        $ext = $ext[count($ext) - 1];
        
        if ($ext == "jpg" || $ext == "jpeg")
            $im = imagecreatefromjpeg($tmpname);
        elseif ($ext == "png")
            $im = imagecreatefrompng($tmpname);
        elseif ($ext == "gif")
            $im = imagecreatefromgif($tmpname);

        $x = imagesx($im);
        $y = imagesy($im);

        $im2 = imagecreatetruecolor($xmax, $ymax);
        imagecopyresampled($im2, $im, 0, 0, 0, 0, $xmax, $ymax, $x, $y);
        return imagepng($im2, $tmpname);
    }

    public function SendGzhMsg($title=""){
        if (!defined("UID")){
            return ;
        }
        $tuserinfo = $this->findone(array('id' => UID));

        if (empty($tuserinfo) || $tuserinfo['is_gz_gzh'] != 1) {
            return;
        }
        /**
         * 开始通知主人 浏览信息
         */
        $body = array(
            "touser" => "",
            "template_id" => Config::getField('weixin', 'gzh')['article_shenhe_tpl_msg_id'],
            "url" => "http://admin.app.xinmaicard.com/users/user_article_list",
            "data" => array(
                "first" => array(
                    "value" => "有用户发布了文章，相关管理人员请前往后台审核文章",
                    "color" => "#173177"
                ),
                "keyword1" => array(
                    "value" => "文章发布",
                    "color" => "#173177"
                ),
                "keyword2" => array(
                    "value" => $tuserinfo['wx_name'] ?? "",
                    "color" => "#173177"
                ),
                "remark" => array(
                    "value" => "文章标题为[{$title}]",
                    "color" => "#173177"
                ),

            ),
        );
        $sysuids = array(
            'oulco1pRSADhktbHCJysDwHr1ETU',
            'oFLpWwwgY6QQYz2Bizlwh-BOnkBg',
            'oulco1tliENTmaBVIAerBu5Aoqtw',
            'oFLpWw_-DogQpTo7nXG-U-lgkk-Q',
            'oulco1jCqUmIwyVHdhjhHTfdf1bc',
            'oulco1jFfrdGUo8gD24PZnst-udY',
        );
        foreach ($sysuids as $sysuid) {
            $body['touser'] = $sysuid;
            $data = json_encode($body);
            $s = Wxsdk::sendtmsg($data);

        }
        return;
    }


}