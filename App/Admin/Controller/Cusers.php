<?php
namespace App\Admin\Controller;
use App\Admin\Model\Account;
use App\Admin\Model\Channel_ID;
use App\Admin\Model\Softwaretemplate;
use App\Admin\Model\FeedBack;
use App\Admin\Model\Jifen_Record;
use App\Admin\Model\Order;
use App\Admin\Model\Shua_Record;
use App\Admin\Model\Software;
use App\Admin\Model\Task_black_log;
use App\Admin\Model\Msg;
use App\Admin\Model\Login_log;
use App\Admin\Model\Account_dynamic;
use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Cache\Cache as Cache;
use SsdPHP\Page\Factory as Page;
use App\Admin\Model\User_shouzhi_log;
use App\Admin\Model\User_tixian_bind;
use App\Admin\Model\Account_photo_bind;
use App\Admin\Model\Account_ext;
use App\Admin\Model\Admin_Menu;
use SsdPHP\SsdPHP;
use App\Admin\Model\Operation_log;
use App\Admin\Model\Admin;
use SsdPHP\Session\Session;
use App\Admin\Model\Yewu_Product;
use App\Admin\Model\Vip_Order;
use App\Admin\Model\Ditch;

class Cusers extends Common {

    /**
     * 会员列表
     */
    public function c_list(){

        $_GET = Input::get();
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['id']    = Input::get('id',"");
        $_GET['did']    = Input::get('did',"");
        $_GET['create_time']    = Input::get('create_time',"");
        $_GET['vip_rank']    = Input::get('vip_rank','-1','intval');
        $_GET['login_type']    = Input::get('login_type','0','intval');
        $_GET['did_type']    = Input::get('did_type','0','intval');
        $_GET['os']    = Input::get('os','-1','intval');
        $Account = new Account();
        $cond=array();

        if($_GET['did_type']==1){
            $cond[]="did in ('46eaeac','de962e7','b4ef6e3','35289a6','3a51330','632a30d','0228171','7e6dd4c','33582ad','12295cf')";
        }

        if($_GET['did_type']==2){
            $cond[]="did in ('eb042c4','e1a398b')";
        }

        if($_GET['did_type']==3){
            $cond[]="did in ('40925a0')";
        }

        if($_GET['os']>-1){
            $cond['os']=$_GET['os'];
        }
        if(!empty($_GET['id'])){
            $cond['id']=$_GET['id'];
        }
        if(!empty($_GET['did'])){
            $cond['did']=$_GET['did'];
        }

        if($_GET['vip_rank']==0){
            $cond[]='vip_expire_time<'.time();
        }elseif ($_GET['vip_rank']==1){
            $cond[]='vip_expire_time>'.time();
        }
        if($_GET['login_type']>0){
            $cond['login_type']=$_GET['login_type'];
        }
        if(!empty($_GET['create_time'])){
            $tiem = strtotime($_GET['create_time']);
            $cond[]="create_time>$tiem and create_time<$tiem+86400";
        }
        //define('DEBUG',1);
        $list = $Account->_getList($cond,$_GET['page'],$_GET['pagesize'],array('*'),"id desc");

        $Ditch = new Ditch();
        foreach ($list->items as $k=>&$v){
            $d_name=$Ditch->findone(array('did'=>$v['did']),array('d_name'));
            $v['did']=$d_name['d_name']??'--';
        }

        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $ditch_list = $Ditch->getAll();

        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'sex'=>array(-2=>'全部')+$Account->sex,
            'status'=>$Account->status,
            'vip_rank'=>$Account->vip_rank,
            'os'=>$Account->os,
            'did_type'=>$Account->did,
            'login_type'=>$Account->login_type,
            'ditch_list'=>$ditch_list,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 登录日志
     */
    public function c_login_log(){

        $_GET = Input::get();
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['uid']    = Input::get('uid',"");
        $_GET['did']    = Input::get('did',"");
        $_GET['create_time']    = Input::get('create_time',"");
        $_GET['login_type']    = Input::get('login_type','0','intval');
        $_GET['did_type']    = Input::get('did_type','0','intval');
        $_GET['os']    = Input::get('os','-1','intval');
        $login_log=new Login_log();
        $cond=array();

        if($_GET['did_type']==1){
            $cond[]="did in ('46eaeac','de962e7','b4ef6e3','35289a6','3a51330','632a30d','0228171','7e6dd4c','33582ad','12295cf')";
        }

        if($_GET['did_type']==2){
            $cond[]="did in ('eb042c4','e1a398b')";
        }

        if($_GET['did_type']==3){
            $cond[]="did in ('40925a0')";
        }

        if($_GET['os']>-1){
            $cond['os']=$_GET['os'];
        }
        if(!empty($_GET['uid'])){
            $cond['uid']=$_GET['uid'];
        }
        if(!empty($_GET['did'])){
            $cond['did']=$_GET['did'];
        }

        if($_GET['login_type']>0){
            $cond['login_type']=$_GET['login_type'];
        }
        if(!empty($_GET['create_time'])){
            $tiem = strtotime($_GET['create_time']);
            $cond[]="create_time>$tiem and create_time<$tiem+86400";
        }
        //define('DEBUG',1);
        $list = $login_log->getList($cond,$_GET['page'],$_GET['pagesize'],array('*'),"id desc");

        $Ditch = new Ditch();
        foreach ($list->items as $k=>&$v){
            $d_name=$Ditch->findone(array('did'=>$v['did']),array('d_name'));
            $v['did']=$d_name['d_name']??'--';
        }

        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $ditch_list = $Ditch->getAll();

        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'os'=>$login_log->os,
            'did_type'=>$login_log->did,
            'login_type'=>$login_log->login_type,
            'ditch_list'=>$ditch_list,
            'page'=>$Page->show()
        ))->base();
    }

    //查看用户推送ID
    public function c_channel_id_list(){
        $Channel_ID = new Channel_ID();
        $id = Input::request('id');
        $ret = $Channel_ID->getall(array("uid"=>$id));
        Response::apiJsonResult($ret,1);
    }

    //用户消息推送记录
    public function c_msg_log(){

        $_GET['uid'] = Input::get('uid','');
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['t_status']       = Input::get('t_status',-1,'intval');

        $cond=array();

        if ($_GET['uid']>0){
            $cond['uid']=$_GET['uid'];
        }
        if ($_GET['t_status']>-1){
            $cond['t_status']=$_GET['t_status'];
        }
        if(!empty($_GET['time_start']) && !empty($_GET['time_end'])){
            $start = strtotime($_GET['time_start']);
            $end = strtotime($_GET['time_end']);
            $cond[]="create_time>$start and create_time<$end+86400";
        }else{
            $start = strtotime(date("Ymd",time()));
            $end = strtotime(date("Ymd",time()));
            $cond[]="create_time>$start and create_time<$end+86400";
        }

        $Msg = new Msg();
        $list = $Msg->getList($cond,$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $this->assign(array(
            '_GET'=>$_GET,
            'list'=>$list,
            'page'=>$Page->show(),
            't_status'=>$Msg->t_status,
        ))->base();
    }
	
	//修改用户状态
    public function c_updata_status(){
        $Account = new Account();
        if(Input::isAJAX()){
            $id=Input::post('id',0,'intval');
            $status=Input::post('status',0,'intval');
            if($id<1){
                Response::apiJsonResult(array(),1010);
            }
            $_POST['update_time']=time();
            $ret = $Account->updateInfo(array(
                'id'=>$id
            ),array(
                'status'=>$status,
                'update_time'=>$_POST['update_time']
            ));
            if($ret == 1){
                Response::apiJsonResult(array(
                    'id'=>$id,
                    'status'=>$Account->status[$status]['title'],
                ),1,"修改成功");
            }
            Response::apiJsonResult(array(),1008);
        }
        Response::apiJsonResult(array(),0);
    }

    /**
     * 个人详情
     */
    public function c_detail(){
        $Account = new Account();

        $table = $Account->cat_table_struct();
        $tid = Input::request('id',0,'intval');
        $info = $Account->findone(['id'=>$tid]);

        $this->assign(array(
            'info'=>$info,
            'table'=>$table,
            '_GET'=>$_GET,
        ))->base();
    }

    /**
     * 用户会员开通记录
     *xu
     */
    public function c_vip_list(){
        $_GET = Input::get();
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['uid']    = Input::get('uid',"");
        $_GET['os']    = Input::get('os','0','intval');
        $_GET['pay_status']    = Input::get('pay_status','-1','intval');
        $_GET['pay_type']    = Input::get('pay_type',"");
        $_GET['create_time']    = Input::get('create_time',"");
        $Vip_order = new Vip_Order();
        $cond=array();
        if($_GET['os']>0){
            $cond['os']=$_GET['os'];
        }
        if(!empty($_GET['uid'])){
            $cond['uid']=$_GET['uid'];
        }
        if(!empty($_GET['pay_type'])){
            $cond['pay_type']=$_GET['pay_type'];
        }
        if($_GET['pay_status']>-1){
            $cond['pay_status']=$_GET['pay_status'];
        }
        if(!empty($_GET['time_start']) && !empty($_GET['time_end'])){
            $start = strtotime($_GET['time_start']);
            $end = strtotime($_GET['time_end']);
            $cond[]="create_time>$start and create_time<$end+86400";
        }

        $list = $Vip_order->getList($cond,$_GET['page'],$_GET['pagesize'],array('*'),"id desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'pay_status'=>$Vip_order->pay_status,
            'pay_type'=>$Vip_order->pay_type,
            'os'=>$Vip_order->os,
            'page'=>$Page->show()
        ))->base();
    }

    //修改用户会员时间
    //xu
    public function c_updata_user_vip()
    {
        $Account = new Account();
        if (Input::isAJAX()) {
            $hid = Input::post('id', 0, 'intval');
            $vip_expire_time = Input::post('vip_expire_time', '');
            if ($hid < 0) {
                Response::apiJsonResult(array(), 1010);
            }
            $ret = $Account->updateInfo(array(
                'id' => $hid
            ), array(
                'vip_expire_time' => strtotime($vip_expire_time),
                'update_time' => time()
            ));
            if ($ret == 1) {
                Response::apiJsonResult(array(), 1, "修改成功");
            }
            Response::apiJsonResult(array(), 1008);
        }
        Response::apiJsonResult(array(), 0);
    }

    /**
     * 动态列表
     */
    public function c_dynamic_list(){

        $_GET = Input::get();
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['uid']    = Input::get('uid','');
        $_GET['did']    = Input::get('did',"");
        $_GET['id']    = Input::get('id','');
        $_GET['status']    = Input::get('status',0,'intval');
        $_GET['create_time']    = Input::get('create_time',"");
        $_GET['did_type']    = Input::get('did_type',0,'intval');
        $_GET['os']    = Input::get('os','-1','intval');
        $Account_dynamic=new Account_dynamic();
        $cond=array();

        if($_GET['did_type']==1){
            $cond[]="did in ('46eaeac','de962e7','b4ef6e3','35289a6','3a51330','632a30d','0228171','7e6dd4c','33582ad','12295cf')";
        }

        if($_GET['did_type']==2){
            $cond[]="did in ('eb042c4','e1a398b')";
        }

        if($_GET['did_type']==3){
            $cond[]="did in ('40925a0')";
        }

        if($_GET['os']>-1){
            $cond['os']=$_GET['os'];
        }
        if($_GET['uid']>0){
            $cond['uid']=$_GET['uid'];
        }
        if($_GET['id']>0){
            $cond['id']=$_GET['id'];
        }

        if($_GET['status']>-1){
            $cond['status']=$_GET['status'];
        }

        if(!empty($_GET['did'])){
            $cond['did']=$_GET['did'];
        }

        if(!empty($_GET['create_time'])){
            $tiem = strtotime($_GET['create_time']);
            $cond[]="create_time>$tiem and create_time<$tiem+86400";
        }
        //define('DEBUG',1);
        $list = $Account_dynamic->getList($cond,$_GET['page'],$_GET['pagesize'],array('*'),"id desc");

        $Account = new Account();
        foreach ($list->items as $k=>&$v){
            $uinfo=$Account->findone(array('id'=>$v['uid']),array('wx_name'));
            $v['name']=$uinfo['wx_name']??'--';
            $v['img']=json_decode($v['img'],true)??'';
            $Admin = new Admin();
            $a = $Admin->findone(array('uid' => $v['aid']), array('nickname'));
            $v['aid'] = empty($a['nickname']) ? '--' : $a['nickname'];

        }

        $Page = new Page($list->totalSize,$_GET['pagesize']);


        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$Account_dynamic->status,
            'page'=>$Page->show()
        ))->base();
    }

    //修改动态状态
    public function c_updata_dynamic_status(){
        $Account_dynamic=new Account_dynamic();
        if(Input::isAJAX()){
            $id=Input::post('id',0,'intval');
            $status=Input::post('status',0,'intval');
            if($id<1){
                Response::apiJsonResult(array(),1010);
            }
            $_POST['update_time']=time();
            $ret = $Account_dynamic->updateInfo(array(
                'id'=>$id
            ),array(
                'status'=>$status,
                'update_time'=>$_POST['update_time']
            ));
            if($ret == 1){
                Response::apiJsonResult(array(
                    'id'=>$id,
                    'status'=>$status,
                    'title'=>$Account_dynamic->status[$status]['title'],
                ),1,"修改成功");
            }
            Response::apiJsonResult(array(),1008);
        }
        Response::apiJsonResult(array(),0);
    }

}