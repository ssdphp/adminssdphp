<?php
namespace App\Admin\Controller;
use App\Admin\Model\Adv;
use App\Admin\Model\Card_No;
use App\Admin\Model\Product;
use App\Admin\Model\Project;
use App\Admin\Model\Software;
use App\Admin\Model\Upload_Log;
use App\Admin\Model\User_notice;
use App\Admin\model\SoftwareVersion;
use App\Admin\Model\Video_Product_Catgroy;
use App\Api\Model\Sys_Notice;
use App\Common\Tool\Functions;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use SsdPHP\Core\Config;
use SsdPHP\Core\Model;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Page\Factory as Page;
use SsdPHP\SsdPHP;
use SsdPHP\Pulgins\PushBaiduNew\PushSDK;
class Csystem extends Common {

    //七牛图片上传
    public function c_auth_upload_qiniu(){

        if(Input::get("action") == 'config'){
            return json_encode(array(
                "imageActionName"=>"uploadimage",
                'imageActionUrl'=>'/system/auth_upload?action=uploadimage',
                'imageUrl'=>'/system/auth_upload?action=uploadimage',
                //'imagePath'=>'/upload/image/',
                'imagePath'=>'/upload/image/',
                'imageUrlPrefix'=> "",
                "imageFieldName"=> "upfile",
                'imageInsertAlign'=>'none',
                "imageMaxSize"=> 2048000,
                "imageAllowFiles"=> [".png", ".jpg", ".jpeg", ".gif", ".bmp"]
            ));
        }
        $accessKey = Config::getField("upload","qiniu")['ak'];
        $secretKey = Config::getField("upload","qiniu")['sk'];
        $auth = new Auth($accessKey, $secretKey);

        // 空间名  http://developer.qiniu.com/docs/v6/api/overview/concepts.html#bucket
        $bucket = Config::getField("upload","qiniu")['bucket'];

        //$key = Input::request('key',null);
        //$key = empty($key) ? "zhedi/uploadimg/".md5_file($_FILES['upfile']['tmp_name']).".png":$key;
        $key = "zhedi/uploadimg/".md5_file($_FILES['upfile']['tmp_name']).".png";
        // 生成上传Token
        $token = $auth->uploadToken($bucket,$key,3600,array());
        $t = array(
            "uptoken"=>$token,
            "domain"=>Config::getField("upload","qiniu")['domain'],
        );
        if (empty($_FILES['upfile'])){
            return json_encode(array(
                'state'=>'ERROR',
            ));
        }

        $Upload_Log = new Upload_Log();
        $info = $Upload_Log->_findone(['path'=>$key,'upload_type'=>1]);
        if (!empty($info)){
            return json_encode(array(
                'state'=>'SUCCESS',
                'url'=>$t["domain"]."/".$info['path'],
                'title'=>$info['file_name'],
                'original'=>$info['file_name'],

            ));
        }
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $_FILES['upfile']['tmp_name']);

        if ($err !== null) {
            return json_encode(array(
                'state'=>$err
            ));
        } else {
            if (empty($info)){
                $s = $Upload_Log->_add([
                    'path'=>$key,
                    'upload_type'=>1,
                    'file_name'=>$_FILES['upfile']['name'],
                    'file_url'=>$t["domain"]."/".$key,
                    'file_size'=>$_FILES['upfile']['size'],
                    'create_time'=>time(),
                ]);
            }
            return json_encode(array(
                'state'=>'SUCCESS',
                'url'=>$t["domain"]."/".$ret['key'],
                'title'=>$_FILES['upfile']['name'],
                'original'=>$_FILES['upfile']['name'],
            ));
        }
    }

    /**
     * 上传处理
     */
    public function c_auth_upload(){

        if(Input::get("action") == 'config'){
            return json_encode(array(
                "imageActionName"=>"uploadimage",
                'imageActionUrl'=>'/system/auth_upload?action=uploadimage',
                'imageUrl'=>'/system/auth_upload?action=uploadimage',
                //'imagePath'=>'/upload/image/',
                'imagePath'=>'/upload/image/',
                'imageUrlPrefix'=> "",
                "imageFieldName"=> "upfile",
                'imageInsertAlign'=>'none',
                "imageMaxSize"=> 2048000,
                "imageAllowFiles"=> [".png", ".jpg", ".jpeg", ".gif", ".bmp"]
            ));
        }


        $upload = \SsdPHP\Upload\Factory::getInstance();

        $fileinfo = $upload->uploadOne($_FILES['upfile']);
        if($fileinfo==false){
            Response::apiJsonResult(array(),403,"文件目录不存在，请联系管理员");
        }

        return json_encode(array(
            'state'=>'SUCCESS',
            'url'=>"/upload/".$fileinfo['savepath'].$fileinfo['savename'],
            'title'=>$fileinfo['name'],
            'original'=>$fileinfo['name'],
        ));
    }

    /**
     * 广告列表
     */
    public function c_adv(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',10,'intval');

        $_GET['is_shangjia']    = Input::get('is_shangjia',0,'intval');
        $Software = new Software();
        $slist = $Software->getAll();
        $_GET['rj']    = Input::get('rj',$slist[0]['appid']);

        $model = new Adv();
        $Project = new Project();

        $cond = array(
            'rj'=>$_GET['rj'],
            'is_shangjia'=>$_GET['is_shangjia']
        );

        $list = $model->getList($cond,$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $Ditch = new Software();
        $this->assign(array(
            'list'=>$list,
            'slist'=>$slist,
            '_GET'=>$_GET,
            'type'=>$model->getType(),
            'is_shangjia'=>$Ditch->is_shangjia,
            'page'=>$Page->show()
        ))->base();
    }

    private $status=array(
        '1'=>'正常',
        '2'=>'禁用',
    );


    public function c_card_change(){
        $pid = Input::request('pid',0,'intval');
        $card_id = Input::request("card_id",0,'intval');

        $Card_No = new Card_No();
        $time =time();

        $s = $Card_No->_update(['project_id'=>$pid,'use_status'=>1],['use_status'=>0,'update_time'=>$time]);
        $s = $Card_No->_update(['id'=>$card_id,'project_id'=>$pid],['use_status'=>1,'update_time'=>$time]);
        if($s !== false){
            Response::apiJsonResult(array(),1,1006);
        }
        Response::apiJsonResult(array(),1005);
    }

    /**
     * 添加
     */
    public function c_adv_add(){
        $Adv = new Adv();
        $_GET['rj']    = Input::request('rj');
        if(empty($_GET['rj'])){
            return "软件没有";
        }
        if(Input::isPost()){

            $_POST=Input::post();
            if(isset($_POST['id'])){
                unset($_POST['id']);
            }
            $ret = $Adv->add($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,'添加成功');
            }
            Response::apiJsonResult(array(),0,'添加失败');
            return ;
        }
        $Ditch = new Softwaretemplate();
        $this->assign(array(
            'status'=>$this->status,
            '_GET'=>$_GET,
            'rj'=>$_GET['rj'],
            'type'=>$Adv->getType(),
            'is_shangjia'=>$Ditch->is_shangjia,
        ))->base('system/adv_edit');
    }

    /**
     * 修改
     */
    public function c_adv_edit(){
        $id = Input::get('id');
        $_GET['rj']    = Input::get('rj',0,'intval');
        $model = new Adv();
        $info = $model->findOne(array('id'=>$id));
        if(Input::isPost()){
            $_POST=Input::post();
            $_POST['update_time']=time();
            $ret = $model->edit($_POST);
            if($ret > 0){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),0,"修改失败");
            return ;
        }
        $Product_Category = new Video_Product_Catgroy();
        $Project = new Project();
        $this->assign(array(
            '_GET'=>$_GET,
            'info'=>$info,
            'rj'=>$_GET['rj'],
            'type'=>$model->getType(),
            'is_shangjia'=>$Product_Category->is_shangjia,
            'project'=>$Project->getSoftList(),
            'status'=>$this->status
        ))->base();
    }




    /**
     * 添加
     */
    public function c_product_add(){
        $Adv = new Product();
        if(Input::isPost()){

            $_POST=Input::post();
            if(isset($_POST['id'])){
                unset($_POST['id']);
            }
            $ret = $Adv->add($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1002);
            }
            Response::apiJsonResult(array(),$ret['code']);
            return ;
        }
        $this->assign(array(
            'status'=>$Adv->status
        ))->base('system/product_edit');
    }

    /**
     * 修改
     */
    public function c_product_edit(){
        $id = Input::request('id');
        $model = new Product();
        $info = $model->findOne(array('id'=>$id));
        if(Input::isPost()){
            $_POST=Input::post();
            $_POST['update_time']=time();
            $ret = $model->edit($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1003);
            }
            Response::apiJsonResult(array(),$ret['code']);

        }
        $this->assign(array(
            '_GET'=>$_GET,
            'info'=>$info,
            'status'=>$model->status
        ))->base();
    }

    /**
     * 积分充值配置
     */
    public function c_product(){

        $Product = new Product();
        $list = $Product->findall();
        $this->assign(array(
            '_GET'=>$_GET,
            'list'=>$list,
            'status'=>$this->status
        ))->base();
    }




    /**
     * 粉丝百度推送
     */
    public function c_fans_baidu_push(){
        $this->c_baidu_push();
    }

    /**
     * 七牛上传token
     * @author  xiaohuihui  <xzh_tx@163.com>
     */
    public function c_jstoken(){


        $accessKey = Config::getField("upload","qiniu")['ak'];
        $secretKey = Config::getField("upload","qiniu")['sk'];
        $auth = new Auth($accessKey, $secretKey);

        // 空间名  http://developer.qiniu.com/docs/v6/api/overview/concepts.html#bucket
        $bucket = Config::getField("upload","qiniu")['bucket'];

        $key = Input::request('key',null);
        $key = empty($key) ? null:$key;
        // 生成上传Token
        $token = $auth->uploadToken($bucket,$key,3600*3,array());
        $t = array(
            "uptoken"=>$token,
            "domain"=>Config::getField("upload","qiniu")['domain'],
        );
        $callback = !empty($_GET['callback']) ? $_GET['callback']:"JS_UpTokenGet";

        Response::apiJsonResult($t,1,'获取token成功');

    }

    /**
     * 向第三方获取卡密信息
     */
    public function c_card_select(){
        $card_id = Input::request('card_id',0,'intval');
        $Card_No = new Card_No();
        $cardInfo = $Card_No->findOne(array('id'=>$card_id));

        $set_array = array(
            CURLOPT_URL => "http://{$cardInfo['domain']}/getslkminfo.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "workkmKey=".$cardInfo['card_no'],
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
                "Accept-Encoding: gzip, deflate",
                "Accept-Language: zh-CN,zh;q=0.8",
                "Connection: keep-alive",
                "Host: {$cardInfo['domain']}",
                "Origin: http://{$cardInfo['domain']}",
                "Referer: http://{$cardInfo['domain']}/rq_order.html",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36",
                "X-Requested-With: XMLHttpRequest"
            ),
        );
        $ret = Functions::curl($set_array);
        $r = iconv('gb2312','utf-8',$ret);
        preg_match('/剩余数量: (.*)/',$r,$data);
        $n = isset($data[1])?str_replace(",","",$data[1]):0;
        $id = $Card_No->edit(array(
            'id'=>$card_id,
            'num'=>$n,
            'update_time'=>time()
        ));
        if($id['ret'] > 0 ){
            Response::apiJsonResult(array('num'=>$n),1);
        }
        Response::apiJsonResult(array(),1005);
    }

    /**
     * 公告列表
     * xu
     */
    public function c_notice_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',20,'intval');
        $User_notice=new User_notice();
        $list = $User_notice->getList(array(),$_GET['page'],$_GET['pagesize'],array("*"),"id desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
            'status'=>$User_notice->status,
        ))->base();
    }

    /**
     * 添加公告
     * xu
     */
    public function c_notice_add(){
        $User_notice = new User_notice();
        if(Input::isPost()){
            $_POST=Input::post();
            print_r($_POST);
            $ret = $User_notice->_add($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"添加成功");
            }
            Response::apiJsonResult(array(),2,"添加失败");
        }
        $this->assign(array(
            'status'=>$User_notice->status,
        ))->base();
    }

    /**
     * 编辑公告
     * xu
     */
    public function c_notice_edit(){
        $User_notice = new User_notice();
        if(Input::isPost()){

            $ret = $User_notice->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),2,"修改失败");
        }

        $id = Input::request("id",0,'intval');

        $info = $User_notice->findone(array('id'=>$id),array('*'));
        $this->assign(array(
            'status'=>$User_notice->status,
            'info'=>$info,
        ))->base("system/notice_add");
    }
}