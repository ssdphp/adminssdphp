<?php
namespace App\Admin\Controller;
use App\Admin\Model\Account;
use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Auth_Group_Access;
use App\Admin\Model\Admin_Menu;
use App\Admin\Model\Channel_ID;
use App\Admin\Model\Softwaretemplate;
use App\Admin\Model\FeedBack;
use App\Admin\Model\Jifen_Get_Record;
use App\Admin\Model\Jifen_Record;
use App\Admin\Model\Jifen_Use_Record;
use App\Admin\Model\Order;
use App\Admin\Model\Project;
use App\Admin\Model\Software;
use App\Api\Model\Msg;
use App\Api\Model\Shua_Record;
use App\Common\Tool\Functions;
use SsdPHP\Core\Config;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Cache\Cache as Cache;
use SsdPHP\Page\Factory as Page;
class Corder extends Common {


    /**
     * 会员列表
     */
    public function c_list(){

        $Ditch = new Softwaretemplate();
        $ditch_list = $Ditch->getAll();
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['rj']    = Input::get('rj',"");
        $_GET['did']    = Input::get('did',"");
        $_GET['id']    = Input::get('id',"");
        $_GET['order_id']    = Input::get('order_id',"");
        $_GET['pay_status']    = Input::get('pay_status',"1",'intval');
        $_GET['create_time']    = Input::get('create_time',"");
        $Order = new Order();
        $cond=array();

        if(!empty($_GET['rj'])){
            $cond['rj']=$_GET['rj'];
        }
        if(!empty($_GET['did'])){
            $cond['did']=$_GET['did'];
        }
        if(!empty($_GET['id'])){
            $cond['uid']=$_GET['id'];
        }

        if(!empty($_GET['order_id'])){
            $cond['order_id']=$_GET['order_id'];
        }

        if($_GET['pay_status']>-1){
            $cond['pay_status']=$_GET['pay_status'];
        }

        if(!empty($_GET['agent_uid'])){
            $cond['agent_uid']=$_GET['agent_uid'];
        }
        if(!empty($_GET['create_time'])){
            $tiem = strtotime($_GET['create_time']);
            $cond[]="create_time>$tiem and create_time<$tiem+86400";
        }

        $list = $Order->getList($cond,$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);
        //print_r($list);
        $Software = new Software();
        $slist = $Software->getAll();

        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            //0-未知，1-安卓，2-苹果
            'os'=>array(-1=>'全部')+$Software->os,
			//用户状态，1-正常，2-禁用(禁止登录),3-改账号为演示账号禁止登录。4-禁止支付
            'ditch_list'=>$ditch_list,
            'slist'=>$slist,
            'pay_status'=>$Order->pay_status,
            'pay_type'=>$Order->pay_type,
            'pay_client'=>$Order->pay_client,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 订单列表
     */
    public function c_order_list(){

        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');
        $_GET['oid']    = Input::get('oid',"");
        $_GET['addtime']    = Input::get('addtime',date("Y-m-d"));
        $_GET['addtime_end']    = Input::get('addtime_end',"");
        $_GET['pay_status']    = Input::get('pay_status',1,'intval');
        $_GET['pay_type']    = Input::get('pay_type',0,'intval');
        $_GET['pay_client']    = Input::get('pay_client',0,'intval');
        $_GET['uid']    = Input::get('uid',0,'intval');
        $_GET['hannels_id']    = Input::get('hannels_id',0);
        $cond = array();
        if($_GET['pay_status']>-1){
            $cond['pay_status']=$_GET['pay_status'];
        }
        if($_GET['pay_type']>0){
            $cond['pay_type']=$_GET['pay_type'];
        }
        if($_GET['pay_client']>0){
            $cond['pay_client']=$_GET['pay_client'];
        }

        if($_GET['oid']>0){
            $cond['order_id']=$_GET['oid'];
        }
        if($_GET['uid']>0){
            $cond['uid']=$_GET['uid'];
        }
        if(!empty($_GET['addtime']) && !empty($_GET['addtime_end'])){

            $addtime        = strtotime($_GET['addtime']);
            $addtime_end    = strtotime($_GET['addtime_end']);
            $cond[]="create_time>$addtime and create_time<$addtime_end+86400";
        }elseif(!empty($_GET['addtime'])) {
            $addtime        = date("Ymd",strtotime($_GET['addtime']));
            $cond[]="index_day=$addtime";
        }


        if(!empty($_GET['hannels_id'])){

            $cond['hannels_id']=$_GET['hannels_id'];
        }
        $Ditch = new Softwaretemplate();
        $ditch_list = $Ditch->getAll();
		#print_R($ditch_list);
        //if是渠道用户
        $is_ditch = Admin_Auth_Group_Access::is_ditch_user();
        if($is_ditch == true){
            $dinfo = $Ditch->findone(array("tui_uid"=>UID));
            $cond['hannels_id']=$dinfo['ditch_id'];
        }
        $model = new Order();
        $list = $model->getList($cond,$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $uidary=array();
        if (!empty($list->items)){
            $uids = array();
            foreach ($list->items as $v){
                $uids[$v['uid']]=$v['uid'];
            }
            $account = new Account();
            $ret = $account->getList(
                array("id in (".(implode(",",$uids)).")")
                ,1
                ,$_GET['pagesize']
            )->items;
            foreach ($ret as $v2){
                $uidary[$v2['id']]=$v2;
            }
        }
        $s = $model->findone(array('pay_status'=>1),array('sum(pay_amt) as pay_amt'));
        $ss = $model->findone($cond,array('sum(pay_amt) as pay_amt'));
        $total_money=floatval($s['pay_amt']);
        $total_money_s=floatval($ss['pay_amt']);
        //print_r($list);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
            'uidary'=>$uidary,
            'pay_status'=>$_GET['pay_status'],
            'pay_type'=>$_GET['pay_type'],
            'pay_client'=>$_GET['pay_client'],
            'ditch_list'=>$ditch_list,
            'is_ditch'=>$is_ditch,
            'total_money'=>$total_money,
            'total_cond_money'=>$total_money_s,
        ))->base();
    }

    /**
     * 积分获取日志
     */
    public function c_jifen_get_log(){
        $Jifen_Get_Record = new Jifen_Get_Record();
        $UID=Input::get('id',0,'intval');
        $page=Input::get('page',1,'intval');
        $pagesize=Input::get('pagesize',10,'intval');

        $ret = $Jifen_Get_Record->getList($UID,$page,$pagesize,array("*"));
        $Page = new Page($ret->totalSize,$pagesize);

        $this->assign(array(
            'list'=>$ret,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
            'type_str'=>$Jifen_Get_Record->type_str,
        ))->base();
    }

    /**
     * 积分使用日志
     */
    public function c_jifen_log(){
        $Jifen_Record = new Jifen_Record();
        $UID=Input::get('id',0,'intval');
        $page=Input::get('page',1,'intval');
        $pagesize=Input::get('pagesize',10,'intval');

        $ret = $Jifen_Record->getList($UID,$page,$pagesize,array("*"));
        $Page = new Page($ret->totalSize,$pagesize);

        $this->assign(array(
            'list'=>$ret,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
        ))->base();
    }
    /**
     * 积分使用日志
     */
    public function c_shua_list(){
        $Jifen_Use_Record = new Shua_Record();
        $Project=new Project();
        $_GET['addtime']    = Input::get('addtime',"");
        $_GET['uid']    = Input::get('uid',"");
        $_GET['p']    = Input::get('p',0);
        $page=Input::get('page',1,'intval');
        $_GET['status']=Input::get('status',2,'intval');
        $_GET['order_status']=Input::get('order_status',-1,'intval');
        $pagesize=Input::get('pagesize',100,'intval');
		$cond = array(
			//'shua_pingtai'=>1,
			'status'=>$_GET['status'],
		);
		
		if(!empty($_GET['uid'])){
			$cond['uid']=$_GET['uid'];
		}
		if(isset($_GET['order_status']) && $_GET['order_status']>-1){
			$cond['task_status']=$_GET['order_status'];
		}
		if(!empty($_GET['addtime'])){
            $tiem = strtotime($_GET['addtime']);
            $cond[]="create_time>$tiem and create_time<$tiem+86400";
        }

		if(!empty($_GET['p'])){
            $cond['project_id']=$_GET['p'];
        }

        $ret = $Jifen_Use_Record->getListCond($cond,$page,$pagesize,array("*"));
        $Page = new Page($ret->totalSize,$pagesize);
        $plist = $Project->getList();
        $this->assign(array(
            'list'=>$ret,
            '_GET'=>$_GET,
            'task_status'=>array(
                0=>['str'=>"等待下单",'color'=>"#333"],
                1=>['str'=>"下单成功",'color'=>"green"],
                2=>['str'=>"下单失败",'color'=>"red"],
                3=>['str'=>"重新下单中",'color'=>"red"]),
            'plist'=>$plist->items,
            'state_str'=>$Jifen_Use_Record->state_str,
            'shua_pingtai'=>$Project->getShuaPingtai(),
            'page'=>$Page->show(),
        ))->base();

    }


    /**
     * 修改业务状态
     */
    public function c_shua_status(){
        if(Input::isAJAX()){
            $_POST = Input::post();
            $Jifen_Use_Record = new Shua_Record();
            $_POST=Input::post();
            $_POST['update_time']=time();
            $ret = $Jifen_Use_Record->_update($_POST);
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1003);
            }
            Response::apiJsonResult(array(),$ret['code']);
        }
        Response::apiJsonResult(array(),0);
    }

    /**
     * 重新下单
     */
    public function c_reorder(){
        if(Input::isAJAX()){

            $cache = Cache::getInstance('Redis');
            $id = Input::request('_id');

            $Shua_Record = new Shua_Record();
            $data = $Shua_Record->findone(array('id'=>$id));
            if(empty($data)){
                Response::apiJsonResult(array(),0,"订单信息没有找到");
            }
            $s = $Shua_Record->_update_task(array("id"=>$id),array(
                'task_status'=>3,//正在提交中
                'cardpwd_id'=>0,
            ));
            $ASYNC_ORDER_KEY = Config::get('ASYNC_ORDER_KEY');
            //失败后重新进入队列。
            $d= json_encode(array(
                'uid'=>$data['uid']."",
                'system_id'=>$data['system_id'],
                'task_id'=>$data['task_id']."",
                'project_id'=>$data['project_id']."",
                'order_num'=>$data['order_num']."",
                'init_num'=>$data['init_num']."",
                'order_value'=>$data['order_value']."",//下单的值
                'time'=>time()."",//下单的值
                'count'=>"0",//下单次数
            ));

            $ss = $cache->rpush($ASYNC_ORDER_KEY,$d);

            if($ss){
                Response::apiJsonResult(array(),1);
            }
            Response::apiJsonResult(array($d),0);
        }
        Response::apiJsonResult(array(),0);
    }
    /**
     * 批量修改业务状态
     */
    public function c_list_shua_status(){
        if(Input::isAJAX()){

            $_POST = Input::post("id/a","0","intval");
            if(!empty($_POST)){

            }
            $Jifen_Use_Record = new Shua_Record();
            $_POST=Input::post();
            //$_POST['update_time']=time();
            $ret = $Jifen_Use_Record->_update(array('id'=>$_POST,'status'=>1));
            if($ret['code'] == 1){
                Response::apiJsonResult(array(),1,1003);
            }
            Response::apiJsonResult(array(),$ret['code']);
        }
        Response::apiJsonResult(array(),0);
    }

    public function c_feedback(){
        $Jifen_Use_Record = new FeedBack();
        $page=Input::get('page',1,'intval');
        $pagesize=Input::get('pagesize',100,'intval');
        $ret = $Jifen_Use_Record->getList(array(),$page,$pagesize,array("*"));
        $Page = new Page($ret->totalSize,$pagesize);

        $this->assign(array(
            'list'=>$ret,
            '_GET'=>$_GET,
            'page'=>$Page->show(),
        ))->base();
    }

    public function c_addjifen(){
        if(Input::isAJAX()){
            $Account = new Account();
            $id=Input::post('id',0,'intval');
            $jifen=Input::post('jifen',0,'intval');
            $desc=Input::post('descript',$jifen>0?"退单补积分+{$jifen}分":"协商扣掉{$jifen}积分");

            $_POST['update_time']=time();
            $ret = $Account->updateInfo(array(
                'id'=>$id
            ),array(
                'jifen=jifen+'.$jifen
            ));
            if($ret == 1){
                $Jifen_Get_Record = new Jifen_Record();
                $s = $Jifen_Get_Record->writeLog([
                    'uid'=>$id,
                    'type'=>$jifen>0?10:11,
                    'descript'=>$desc,
                    'jifen_num'=>$jifen,
                ]);

                Response::apiJsonResult(array(),1,1003);
            }
            Response::apiJsonResult(array(),1008);
        }
        Response::apiJsonResult(array(),0);
    }

    public function c_addmsg(){
        if(Input::isAJAX()){
            $Account = new Msg();
            $uid=Input::post('uid',0,'intval');
            $ispush=Input::post('ispush',0,'intval');

            $content=Input::post('content',"");
            if($uid<1){
                Response::apiJsonResult(array(),2002);
            }
            if(empty($content)){
                Response::apiJsonResult(array(),2001);
            }
            $_POST['update_time']=time();
            $ret = $Account->add(array(
                "content"=>$content,
                "uid"=>$uid,
                "status"=>1,
                "create_time"=>time(),
            ));
            if($ret>0){
                //如果要推送
                if($ispush==1){
                    $Channel_ID = new Channel_ID();
                    $ret = $Channel_ID->findone(array("uid"=>$uid));
                    if(!empty($ret['id'])){
                        $a = $ret['os']==1?1:0;
                        $i = $ret['os']==2?1:0;
                        $s = Functions::BaiduPush($content,$ret["p"],$a,$i,$ret['channel_id'],array(
                            "msg_type"=>"kefu"
                        ));
                    }

                }
                Response::apiJsonResult(array(),1,2003);
            }

        }
        Response::apiJsonResult(array(),0);
    }
	
	//退款表示
	public function c_tuikuan(){
        if(Input::isAJAX()){
            $Order = new Order();
            $id=Input::post('id',0,'intval');
            if($id<1){
                Response::apiJsonResult(array(),1010);
            }
            $_POST['update_time']=time();
            $ret = $Order->updateInfo(array(
                'id'=>$id
            ),array(
                'pay_status'=>2,
                'update_time'=>$_POST['update_time']
            ));
            if($ret == 1){
                Response::apiJsonResult(array(),1,1006);
            }
            Response::apiJsonResult(array(),1008);
        }
        Response::apiJsonResult(array(),0);
    }
	
	
	//修改用户状态
	public function c_update_status(){
        if(Input::isAJAX()){
            $Account = new Account();
            $id=Input::post('id',0,'intval');
            $status=Input::post('status',1,'intval');
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
                Response::apiJsonResult(array(),1,1006);
            }
            Response::apiJsonResult(array(),1008);
        }
        Response::apiJsonResult(array(),0);
    }

}