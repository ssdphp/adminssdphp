<?php
namespace App\Admin\Controller;
use App\Admin\Model\Softwaretemplate;
use App\Admin\Model\Software;
use App\Admin\Model\Task;
use App\Admin\Model\Task_List;
use SsdPHP\Cache\Cache;
use SsdPHP\Core\Config;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\Page\Factory as Page;
use App\Admin\Model\User_Pinglun;
use App\Admin\Model\User_pinglun_type;
use App\Admin\Model\Yewu_Product;
use App\Admin\Model\Account_photo_bind;
use App\Admin\Model\Account_task_photo_log;
use App\Common\Tool\Functions;

class Ctask extends Common {

    /**
 * 获取缓存数据
 */
    public function c_get_task_list_queue_num(){
        $tuid = Input::request('task_uuid');
        $Cache = Cache::getInstance();
        $order_cache_key = Config::getField('task','task_order_queue_key');
        $order_list_cache_key = Config::getField('task','task_order_list_queue_key');
        $d['complete_num'] = $Cache->Llen($order_list_cache_key.$tuid);

        Response::apiJsonResult($d,1,"获取成功");
    }

    /**
     * 业务list
     */
    public function c_list(){

        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');
        $_GET['status']    = Input::get('status','-1','intval');
        $_GET['did']    = Input::get('did',"");
        $_GET['tid']    = Input::get('tid','','intval');
        $_GET['uid']    = Input::get('uid','','intval');
        $_GET['pay_status']    = Input::get('pay_status','1','intval');
        $_GET['create_time']    = Input::get('create_time',"");
        $_GET['pay_type']    = Input::get('pay_type','');
        $_GET['status']    = Input::get('status','-1','intval');
        $_GET['p_id']    = Input::get('p_id','','intval');
        $_GET['task_uuid']    = Input::get('task_uuid',"");
        $_GET['photo_uid']    = Input::get('photo_uid',"");
        $_GET['photo_pid']    = Input::get('photo_pid',"");
        $cond=array();
        if(!empty($_GET['task_uuid'])){
            $cond['task_uuid']=$_GET['task_uuid'];
        }
        if($_GET['p_id']>0){
            $cond['p_id']=$_GET['p_id'];
        }
        if(!empty($_GET['did'])){
            $cond['did']=$_GET['did'];
        }

        if(!empty($_GET['tid'])){
            $cond['tid']=$_GET['tid'];
        }

        if(!empty($_GET['photo_uid'])){
            $cond['photo_uid']=$_GET['photo_uid'];
        }

        if(!empty($_GET['photo_pid'])){
            $cond['photo_pid']=$_GET['photo_pid'];
        }

        if($_GET['pay_type']){
            $cond['pay_type']=$_GET['pay_type'];
        }

        if($_GET['uid']>0){
            $cond['uid']=$_GET['uid'];
        }else{
            $_GET['uid']="";
        }

        if($_GET['pay_status']>-1){
            $cond['pay_status']=$_GET['pay_status'];
        }
        if($_GET['status']>-1){
            $cond['status']=$_GET['status'];
        }

        if(!empty($_GET['time_start']) && !empty($_GET['time_end'])){
            $start = strtotime($_GET['time_start']);
            $end = strtotime($_GET['time_end']);
            $cond[]="create_time>$start and create_time<$end+86400";
        }
        $Software = new Software();
        $slist = $Software->getAll();
        $Ditch = new Softwaretemplate();
        $ditch_list = $Ditch->getAll();
        $Task = new Task();
        $list = $Task->getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"tid desc");
        $Yewu_Product=new Yewu_Product();
        $clist=$Yewu_Product->getAll(array('status'=>1));
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $User_pinglun_type=new User_pinglun_type();
        $pinglun_type=$User_pinglun_type->getAll(array(),array('id','name'));

        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'os'=>array(-1=>'全部')+$Software->os,
            'ditch_list'=>$ditch_list,
            'slist'=>$slist,
            'clist'=>$clist,
            'status'=>$Task->status,
            'pay_type'=>$Task->pay_type,
            'pay_status'=>$Task->pay_status,
            'pinglun_type'=>$pinglun_type,
            'page'=>$Page->show()
        ))->base();
    }
    /**
     * 业务list
     */
    public function c_order_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',1000,'intval');
        $_GET['status']    = Input::get('status','-1',"intval");
        $_GET['get_status']    = Input::get('get_status','-1',"intval");
        $_GET['id']    = Input::get('id','0',"intval");
        $cond = array();
        if($_GET['status']>-1){
            $cond['status'] = $_GET['status'];
        }
        if($_GET['get_status']>-1){
            $cond['get_status'] = $_GET['get_status'];
        }
        if($_GET['id']>-1){
            $cond['tid'] = $_GET['id'];
        }
        $Task = new Task_List();
        $list = $Task->getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"id desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$Task->status,
            'get_status'=>$Task->get_status,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 子任务list
     */
    public function c_zi_list(){
        $tid = Input::request('tid');
        $Task = new Task_List();
        $list = $Task->getList(array('tid'=>$tid,'status'=>0),array("*"));
        Response::apiJsonResult($list->items,1,"获取成功");

    }

    /**
     * 异常子任务一键缓存
     */
    public function c_set_list_task(){
        $Task_List = new Task_List();
        $Task = new Task();
        $id = Input::post('id',0,'intval');
        $list=$Task_List->getList(array("tid"=>$id,'get_status'=>1,'status'=>0),array("*"));
        $info=$Task->findOne(array("tid"=>$id),array("task_uuid"));
        if (!empty($list->items) && !empty($info)){
            $Cache = Cache::getInstance();
            $cache_list_key = config::getField('task','task_order_list_queue_key');
            $listkey = $cache_list_key.$info['task_uuid'];
            foreach($list->items as $v){
                if(abs(time()-$v['get_time'])>60){
                    $cdata=array(
                        "id"=>$v['id'],
                        "uid"=>$v['uid'],
                        "toid"=>$v['toid'],
                        "tid"=>$v['tid'],
                        "task_uuid"=>$v['task_uuid']."",
                        "photo_uuid"=>$v['photo_uuid'],
                        "task_validtime"=>intval($v['task_validtime']),
                        'index_year_month_day'=>$v['index_year_month_day'],
                    );
                    $Cache->Lrem($listkey,json_encode($cdata),0);
                    $Cache->Lpush($listkey,json_encode($cdata));
                    //更新任务信息
                    $task_data = $Task_List->findOne(array('id'=>$v['id']),array("*"));
                    $s = $Task_List->edit(array(
                        'id'=>$task_data['id'],
                        'end_uid'=>0,
                        'get_status'=>0,
                        'get_time'=>0,
                        'update_time'=>time(),
                    ));
                }
            }
            Response::apiJsonResult(array(),1,"缓存成功");
        }
        Response::apiJsonResult(array(),0,"无需缓存");

    }


    //任务添加标签
    public function c_updata_task_label(){
        $Task = new Task();
        $User_pinglun_type=new User_pinglun_type();
        $Task_List = new Task_List();
        $Cache = Cache::getInstance();
        $task_ks_pl_pid = config::getField('task','task_ks_pl_pid');
        if(Input::isAJAX()){
            $tid=Input::post('id',0,'intval');
            $works_label=Input::post('works_label',"",'intval');
            $data = $Task->findone(array('tid'=>$tid),array('*'));
            if(!empty($data) && $data['p_id']==$task_ks_pl_pid && empty($data['works_label'])){
                $ret=$Task_List->findone(array('tid'=>$tid),array('*'));
                if(!empty($ret)){
                    Response::apiJsonResult(array(),0,"缓存失败，任务已存在");
                }
                $ret = $Task->edit(array(
                    'tid' => $tid,
                    'works_label' => $works_label,
                ));
                if($ret != 1){
                    Response::apiJsonResult(array(),0,"设置失败");
                }
                $cache_pinglun_key = config::getField('task','task_list_pinglun_key');
                $cache_list_key = config::getField('task','task_order_list_queue_key');
                $cache_order_key = config::getField('task','task_order_queue_key');
                $d = array(
                    'tid'=>$tid,
                    'task_uuid'=>$data['task_uuid'],
                    'photo_uuid'=>$data['photo_uuid'],
                    'yw_id'=>$data['yw_id'],
                    'yw_name'=>$data['yw_name'],
                    'yw_img'=>$data['yw_img'],
                    'p_id'=>$data['p_id'],
                    'p_name'=>$data['p_name'],
                    'p_img'=>$data['p_img'],
                    'unit_price'=>$data['unit_price'],
                    'photo_init_num'=>$data['photo_init_num'],
                    'photo_need_num'=>1,//完成量都是一个
                    'photo_url'=>$data['photo_url'],
                    'photo_protocol'=>$data['photo_protocol'],
                    'photo_uid' => $data['photo_uid'],
                    'photo_pid' => $data['photo_pid'],
                    'task_validtime'=>$data['task_validtime'],
                    'status'=>0,
                    'end_uid'=>0,
                    'uid' => $data['uid'],
                    'rj' =>$data['rj'],
                    'os' =>$data['os'],
                    'did'=> $data['did'],
                    'is_jiaji'=>$data['is_jiaji'],
                    'from_app'=>$data['from_app'],
                    'index_year'=>$data['index_year'],
                    'index_year_month'=>$data['index_year_month'],
                    'index_year_month_day'=>$data['index_year_month_day'],
                    'get_status'=>0,
                    'get_time'=>0,
                    'create_time'=>$data['create_time'],
                    'update_time'=>0,
                );

                $s = $Cache->ZADD($cache_order_key.$data['p_id'],0,json_encode(array(
                    "tid"=>$tid,//订单id
                    "uid"=>$data['uid'],//任务发布的UID
                    "task_uuid"=>$data['task_uuid'],//订单唯一id
                    "photo_uuid"=>$data['photo_uuid'],
                    "index_year_month_day"=>$data['index_year_month_day'],
                )));

                if(!$s ){
                    Response::apiJsonResult(array(),0,"缓存失败,父任务已存在");
                }
                //查询评论作品总需求
                $photo_need_num = $Task->findone(array('photo_uuid' => $data['photo_uuid'], 'p_id' => $task_ks_pl_pid,'works_label'=>$works_label), array('sum(photo_need_num) as num'))['num'];
                //评论作品已使用数量
                $shiyong_num=$photo_need_num-$data['photo_need_num'];
                if(empty($shiyong_num)){
                    //查询出对应类型缓存评论
                    $Cache_list=$Cache->ZRANGE($cache_pinglun_key.$works_label,0,$photo_need_num-1,'WITHSCORES');
                }else{
                    $Cache_list=$Cache->ZRANGE($cache_pinglun_key.$works_label,$shiyong_num,$photo_need_num-1,'WITHSCORES');
                }
                $pl_list=array();
                foreach ($Cache_list as $k=>$v){
                    $pl_list[]=$k;
                }
                //添加任务队列详细
                $listkey = $cache_list_key.$data['task_uuid'];
                for($i=0;$i<$data['photo_need_num'];$i++){
                    //写入评论语句
                    if(!empty($pl_list[$i])){
                        $pinglun=$pl_list[$i];
                        $d['pinglun']=$pinglun;
                    }else{
                        $pinglun=$this->randomOne($works_label);
                        $d['pinglun']=$pinglun['content'];
                    }

                    $d['toid']=\App\Common\Tool\Functions::uniqidReal(32);
                    $id = $Task_List->add($d);
                    if($id>0){
                        $cdata=array(
                            "id"=>$id,
                            "uid"=>$data['uid'],
                            "toid"=>$d['toid'],
                            "tid"=>$tid,
                            "task_uuid"=>$data['task_uuid']."",
                            "photo_uuid"=>$data['photo_uuid'],
                            "task_validtime"=>intval($data['task_validtime']),
                            'index_year_month_day'=>$data['index_year_month_day'],
                        );
                        $s = $Cache->Lpush($listkey,json_encode($cdata));
                    }

                }
                $name=$User_pinglun_type->findOne(array('id'=>$works_label),array('name'));
                Response::apiJsonResult(array(
                    'tid'=>$tid,
                    'name'=>$name['name']
                ),1,"设置成功");
            }
            Response::apiJsonResult(array(),0,"设置失败");
        }
        Response::apiJsonResult(array(),0,"设置失败");
    }

    //缓存评论任务备用
    public function c_set_pinglun_task(){
        $Task = new Task();
        $Task_List = new Task_List();
        $Cache = Cache::getInstance();
        $task_ks_pl_pid = config::getField('task','task_ks_pl_pid');
        if(Input::isAJAX()){
            $tid=Input::post('id',0,'intval');
            $data = $Task->findone(array('tid'=>$tid),array('*'));
            if(!empty($data) && $data['p_id']==$task_ks_pl_pid){
                $ret=$Task_List->findone(array('tid'=>$tid),array('*'));
                if(!empty($ret)){
                    Response::apiJsonResult(array(),0,"缓存失败，任务已存在");
                }
                $cache_pinglun_key = config::getField('task','task_list_pinglun_key');
                $cache_list_key = config::getField('task','task_order_list_queue_key');
                $cache_order_key = config::getField('task','task_order_queue_key');
                $d = array(
                    'tid'=>$tid,
                    'task_uuid'=>$data['task_uuid'],
                    'photo_uuid'=>$data['photo_uuid'],
                    'yw_id'=>$data['yw_id'],
                    'yw_name'=>$data['yw_name'],
                    'yw_img'=>$data['yw_img'],
                    'p_id'=>$data['p_id'],
                    'p_name'=>$data['p_name'],
                    'p_img'=>$data['p_img'],
                    'unit_price'=>$data['unit_price'],
                    'photo_init_num'=>$data['photo_init_num'],
                    'photo_need_num'=>1,//完成量都是一个
                    'photo_url'=>$data['photo_url'],
                    'photo_protocol'=>$data['photo_protocol'],
                    'photo_uid' => $data['photo_uid'],
                    'photo_pid' => $data['photo_pid'],
                    'task_validtime'=>$data['task_validtime'],
                    'status'=>0,
                    'end_uid'=>0,
                    'uid' => $data['uid'],
                    'rj' =>$data['rj'],
                    'os' =>$data['os'],
                    'did'=> $data['did'],
                    'is_jiaji'=>$data['is_jiaji'],
                    'from_app'=>$data['from_app'],
                    'index_year'=>$data['index_year'],
                    'index_year_month'=>$data['index_year_month'],
                    'index_year_month_day'=>$data['index_year_month_day'],
                    'get_status'=>0,
                    'get_time'=>0,
                    'create_time'=>$data['create_time'],
                    'update_time'=>0,
                );

                $s = $Cache->ZADD($cache_order_key.$data['p_id'],0,json_encode(array(
                    "tid"=>$tid,//订单id
                    "uid"=>$data['uid'],//任务发布的UID
                    "task_uuid"=>$data['task_uuid'],//订单唯一id
                    "photo_uuid"=>$data['photo_uuid'],
                    "index_year_month_day"=>$data['index_year_month_day'],
                )));

                if(!$s ){
                    Response::apiJsonResult(array(),0,"缓存失败,父任务已存在");
                }
                //查询评论作品总需求
                $photo_need_num = $Task->findone(array('photo_uuid' => $data['photo_uuid'], 'p_id' => $task_ks_pl_pid,'works_label'=>$data['works_label']), array('sum(photo_need_num) as num'))['num'];
                //评论作品已使用数量
                $shiyong_num=$photo_need_num-$data['photo_need_num'];
                if(empty($shiyong_num)){
                    //查询出对应类型缓存评论
                    $Cache_list=$Cache->ZRANGE($cache_pinglun_key.$data['works_label'],0,$photo_need_num-1,'WITHSCORES');
                }else{
                    $Cache_list=$Cache->ZRANGE($cache_pinglun_key.$data['works_label'],$shiyong_num,$photo_need_num-1,'WITHSCORES');
                }
                $pl_list=array();
                foreach ($Cache_list as $k=>$v){
                    $pl_list[]=$k;
                }
                //添加任务队列详细
                $listkey = $cache_list_key.$data['task_uuid'];
                for($i=0;$i<$data['photo_need_num'];$i++){
                    //写入评论语句
                    if(!empty($pl_list[$i])){
                        $pinglun=$pl_list[$i];
                        $d['pinglun']=$pinglun;
                    }else{
                        $pinglun=$this->randomOne($data['works_label']);
                        $d['pinglun']=$pinglun['content'];
                    }

                    $d['toid']=\App\Common\Tool\Functions::uniqidReal(32);
                    $id = $Task_List->add($d);
                    if($id>0){
                        $cdata=array(
                            "id"=>$id,
                            "uid"=>$data['uid'],
                            "toid"=>$d['toid'],
                            "tid"=>$tid,
                            "task_uuid"=>$data['task_uuid']."",
                            "photo_uuid"=>$data['photo_uuid'],
                            "task_validtime"=>intval($data['task_validtime']),
                            'index_year_month_day'=>$data['index_year_month_day'],
                        );
                        $s = $Cache->Lpush($listkey,json_encode($cdata));
                    }

                }
                Response::apiJsonResult(array(),1,"缓存成功");
            }
            Response::apiJsonResult(array(),0,"缓存失败，请检查任务");
        }
        Response::apiJsonResult(array(),0,"缓存失败，请检查任务");
    }

    /**
     * 获取评论语句
     * @param array $cond
     * @param array $feild
     * @return array|mixed
     */
    public function randomOne($type){

        $User_Pinglun = new User_Pinglun();

        $d=$User_Pinglun->randomOne(array('type'=>$type),array("content","id"),"rand()");
        $data=array();
        $chars= config::getField('task','task_pinglun_content');
        $strlen=mb_strlen($chars);
        $char_count=mt_rand(1,2);
        for ($i = 0, $str = ''; $i < $char_count; $i++) {
            $char_pos=mt_rand(1,$strlen);
            $str.=mb_substr($chars,$char_pos,1);
        }
        $len=mb_strlen($d['content']);
        $tmp = mb_substr($d['content'],1,mt_rand(1,$len));
        $content=str_replace($tmp,$tmp.$str,$d['content']);
        $data['id']=$d['id'];
        $data['content']=$content;

        return $data;


    }


    /**
     * 单个子任务缓存
     */
    public function c_set_list_d_task(){
        $Task_List = new Task_List();
        $Task = new Task();
        $id = Input::post('id',0,'intval');
        $tid = Input::post('tid',0,'intval');
        $list=$Task_List->findOne(array("id"=>$id),array("*"));
        $info=$Task->findOne(array("tid"=>$tid),array("task_uuid"));
        if (!empty($list) && !empty($info) && $list['status']==0){
            $Cache = Cache::getInstance();
            $cache_list_key = config::getField('task','task_order_list_queue_key');
            $listkey = $cache_list_key.$info['task_uuid'];
                if(abs(time()-$list['get_time'])>$list['task_validtime']) {
                    $cdata = array(
                        "id" => $list['id'],
                        "uid" => $list['uid'],
                        "toid" => $list['toid'],
                        "tid" => $list['tid'],
                        "task_uuid" => $list['task_uuid'] . "",
                        "photo_uuid" => $list['photo_uuid'],
                        "task_validtime" => intval($list['task_validtime']),
                        'index_year_month_day' => $list['index_year_month_day'],
                    );
                    $Cache->Lrem($listkey, json_encode($cdata), 0);
                    $Cache->Lpush($listkey, json_encode($cdata));
                    //更新任务信息
                    $s = $Task_List->edit(array(
                        'id' => $list['id'],
                        'end_uid' => 0,
                        'get_status' => 0,
                        'get_time' => 0,
                        'status'=>0,
                        'update_time' => time(),
                    ));
                }
            Response::apiJsonResult(array(
                'id'=>$id,
                'end_uid' => 0,
                'get_status' => "未接取",
                'get_time' => '--',
                'status'=>"进行中",
            ),1,"缓存成功");
        }
        Response::apiJsonResult(array(),0,"无需缓存");

    }

    //修改任务状态
    public function c_updata_task_status(){
        $Task = new Task();
        if(Input::isAJAX()){
            $tid=Input::post('id',0,'intval');
            $status=Input::post('status',"",'intval');
            $_POST['update_time']=time();
            $ret = $Task->edit(array(
                'tid' => $tid,
                'status' => $status,
            ));
            if($ret == 1){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),0,"修改失败");
        }
        Response::apiJsonResult(array(),0,"修改失败");
    }

    /**
     * 单个父类任务缓存
     */
    public function c_set_d_task(){
        $Task = new Task();
        $tid = Input::post('cid',0,'intval');
        $info=$Task->findOne(array("tid"=>$tid),array("*"));
        if (!empty($info) && $info['status']!=1){
            $Cache = Cache::getInstance();
            $cache_order_key = config::getField('task','task_order_queue_key');
            $listkey = $cache_order_key.$info['p_id'];
            //添加任务
            $s = $Cache->ZADD($listkey,0,json_encode(array(
                "tid"=>$tid,//订单id
                "uid"=>$info['uid'],//任务发布的UID
                "task_uuid"=>$info['task_uuid'],//订单唯一id
                "photo_uuid"=>$info['photo_uuid'],
                "index_year_month_day"=>$info['index_year_month_day'],
            )));
            //更新任务信息
            $Task->edit(array(
                'tid' => $tid,
                'status' => 0,
                'update_time' => time(),
            ));
            Response::apiJsonResult(array(),1,"缓存成功");
        }
        Response::apiJsonResult(array(),0,"无需缓存");

    }

    /**
     * 单个父类任务删除缓存
     */
    public function c_del_d_task(){
        $Task = new Task();
        $tid = Input::post('id',0,'intval');
        $info=$Task->findone(array("tid"=>$tid),array("*"));
        if (!empty($info)){
            $Cache = Cache::getInstance();
            $cache_order_key = config::getField('task','task_order_queue_key');
            $listkey = $cache_order_key.$info['p_id'];
            $data=array(
                "tid"=>$tid,//订单id
                "uid"=>$info['uid'],//任务发布的UID
                "task_uuid"=>$info['task_uuid'],//订单唯一id
                "photo_uuid"=>$info['photo_uuid'],
                "index_year_month_day"=>$info['index_year_month_day'],
            );
            $Cache->Zrem($listkey, json_encode($data));
            Response::apiJsonResult(array(),1,"删除成功");
        }
        Response::apiJsonResult(array(),0,"无需删除");

    }

    /**
     * 单个任务缓存清空
     */
    public function c_empty_d_task(){
        $Task = new Task();
        $qid = Input::post('qid',0,'intval');
        $info=$Task->findone(array("tid"=>$qid),array("*"));
        $ret=$Task->edit(array("tid"=>$qid,"status"=>3));
        if (!empty($info) && $ret>0){
            $Cache = Cache::getInstance();
            $cache_order_key = config::getField('task','task_order_queue_key');
            $cache_list_key = config::getField('task','task_order_list_queue_key');
            $zlistkey = $cache_list_key.$info['task_uuid'];
            $flistkey = $cache_order_key.$info['p_id'];
            $data=array(
                "tid"=>$qid,//订单id
                "uid"=>$info['uid'],//任务发布的UID
                "task_uuid"=>$info['task_uuid'],//订单唯一id
                "photo_uuid"=>$info['photo_uuid'],
                "index_year_month_day"=>$info['index_year_month_day'],
            );

            $Cache->Zrem($flistkey, json_encode($data));
            $Cache->Del($zlistkey);
            Response::apiJsonResult(array(),1,"清空成功");
        }
        Response::apiJsonResult(array(),0,"无需清空");

    }

    /**
     * 禁用缓存清空
     */
    public function c_del_task(){
        $Task = new Task();
        $info=$Task->getList(array('status'=>3),array("*"));
        if (!empty($info->items)){
            foreach ($info->items as $v){
                $Cache = Cache::getInstance();
                $cache_order_key = config::getField('task','task_order_queue_key');
                $cache_list_key = config::getField('task','task_order_list_queue_key');
                $zlistkey = $cache_list_key.$v['task_uuid'];
                $flistkey = $cache_order_key.$v['p_id'];
                $data=array(
                    "tid"=>$v['tid'],//订单id
                    "uid"=>$v['uid'],//任务发布的UID
                    "task_uuid"=>$v['task_uuid'],//订单唯一id
                    "photo_uuid"=>$v['photo_uuid'],
                    "index_year_month_day"=>$v['index_year_month_day'],
                );

                $Cache->Zrem($flistkey, json_encode($data));
                $Cache->Del($zlistkey);
            }

            Response::apiJsonResult(array(),1,"清空成功");
        }
        Response::apiJsonResult(array(),0,"无需清空");

    }




    /**
     * 任务详情
     */
    public function c_detail(){
        $Task = new Task();

        $table = $Task->cat_table_struct();
        $tid = Input::request('id',0,'intval');
        $info = $Task->findone(['tid'=>$tid]);
        $this->assign(array(
            'info'=>$info,
            'table'=>$table,
            '_GET'=>$_GET,
            'status'=>$Task->status,
        ))->base();
    }
    /**
     * 任务列表详情
     */
    public function c_order_detail(){
        $Task_list = new \App\Admin\Model\Task_List();


        $table = $Task_list->cat_table_struct();
        $tid = Input::request('id',0,'intval');
        $info = $Task_list->findone(['id'=>$tid]);
        $this->assign(array(
            'info'=>$info,
            'table'=>$table,
            '_GET'=>$_GET,
            'status'=>$Task_list->status,
        ))->base();
    }

    //处理定时器异常。导致的异常任务
    public function c_order_timerh(){
        $id = Input::request('id',0,'intval');

        $Task_list = new \App\Admin\Model\Task_List();
        $info = $Task_list->findone(array('id'=>$id));
        if(empty($info)){
            Response::apiJsonResult(array(),0,'id有误');
        }
        $Cache = Cache::getInstance();
        $cdata=array(
            "id"=>$id,
            "uid"=>$info['uid'],
            'index_year_month_day'=>$info['index_year_month_day'],
        );
        $Qk = Config::getField('task','queue_key');
        $key = $Qk.$info['p_id'];
        if($info['is_jiaji'] == 1){
            $s = $Cache->Lpush($key,json_encode($cdata));
        }else{
            $s = $Cache->Rpush($key,json_encode($cdata));
        }
        if($s){
            $s = $Task_list->edit(array(
                'id'=>$id,
                'end_uid'=>0,
                'get_status'=>0,
                'get_time'=>0,
                'update_time'=>time(),
            ));
            Response::apiJsonResult(array(),1,"处理成功");
        }else{
            Response::apiJsonResult(array(

            ),0,"处理失败");
        }


    }

    /**
     * 评论类型列表
     */
    public function c_pinglun_type_list(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');
        $_GET['status']=Input::get('status',-1,'intval');
        $cond=array();
        if($_GET['status']>-1){
            $cond['status']=$_GET['status'];
        }

        $User_pinglun_type= new User_pinglun_type();
        $list = $User_pinglun_type->getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"id desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $this->assign(array(
            'list'=>$list,
            '_GET'=>$_GET,
            'status'=>$User_pinglun_type->status,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 添加评论类型
     */
    public function c_pinglun_type_add(){
        $User_pinglun_type= new User_pinglun_type();
        if(Input::isPost()){
            $_POST = Input::post();
            $ret = $User_pinglun_type->_add($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"添加成功");
            }
            Response::apiJsonResult(array(),2,"添加失败");
        }
        $this->assign(array(
            'status'=>$User_pinglun_type->status,
        ))->base();
    }

    /**
     * 编辑评论类型
     */
    public function c_pinglun_type_edit(){
        $User_pinglun_type= new User_pinglun_type();
        if(Input::isPost()){
            $_POST = Input::post();
            $ret = $User_pinglun_type->edit($_POST);
            if($ret>0){
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),2,"修改失败");
        }

        $id = Input::request("id",0,'intval');

        $info = $User_pinglun_type->findOne(array('id'=>$id),array('*'));
        $this->assign(array(
            'status'=>$User_pinglun_type->status,
            'info'=>$info,
        ))->base("task/pinglun_type_add");
    }

    /**
     * 评论列表
     */
    public function c_pinglun(){
        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',100,'intval');
        $_GET['type']    = Input::get('type',0,'intval');
        $cond=array();
        if($_GET['type']>0){
            $cond['type']=$_GET['type'];
        }

        $User_Pinglun= new \App\Admin\Model\User_Pinglun();
        $list = $User_Pinglun->getList($cond,$_GET['page'],$_GET['pagesize'],array("*"),"id desc");
        $Page = new Page($list->totalSize,$_GET['pagesize']);
        $User_pinglun_type= new User_pinglun_type();
        $type_list = $User_pinglun_type->getList(array(''),array("*"));
        $this->assign(array(
            'list'=>$list,
            'type_list'=>$type_list,
            '_GET'=>$_GET,
            'page'=>$Page->show()
        ))->base();
    }

    /**
     * 评论缓存
     */
    public function c_set_list_pinglun(){
        $id= Input::post('id',0,'intval');
        $pinglun = new User_Pinglun();
        $list=$pinglun->getList(array('type'=>$id),array("type","content",'create_time'));
        if (!empty($list->items)){
            $Cache = Cache::getInstance();
            $pinglun_cache_key = Config::getField('task','task_list_pinglun_key');
            foreach ($list->items as $v){
                $Cache->ZADD($pinglun_cache_key.$v['type'],$v['create_time'],$v['content']);
            }
            Response::apiJsonResult(array(),1,"缓存成功");
        }
        Response::apiJsonResult(array(),0,"缓存失败");

    }

    /**
     * 添加评论
     */
    public function c_pinglun_add(){
        $pinglun = new User_Pinglun();
        if(Input::isPost()){
            $_POST = Input::post();
            $type=Input::post('type','');
            $content=Input::post('content','');
            $_POST['create_time']=time();
            $ret = $pinglun->_add($_POST);
            if($ret>0){
                $Cache = Cache::getInstance();
                $pinglun_cache_key = Config::getField('task','task_list_pinglun_key');
                $Cache->ZADD($pinglun_cache_key.$type,$_POST['create_time'],$content);
                Response::apiJsonResult(array(),1,"添加成功");
            }
            Response::apiJsonResult(array(),2,"添加失败");
        }
        $User_pinglun_type= new User_pinglun_type();
        $tlist = $User_pinglun_type->getAll(array('status'=>0),array("*"));
        $this->assign(array(
            'tlist'=>$tlist,
        ))->base();
    }

    /**
     * 编辑评论
     */
    public function c_pinglun_edit(){
        $pinglun = new User_Pinglun();
        if(Input::isPost()){
            $_POST = Input::post();
            $type=Input::post('type','');
            $content=Input::post('content','');
            $time=Input::post('create_time','');
            $id = Input::request("id",0,'intval');
            $details = $pinglun->findone(array('id'=>$id),array('content'));
            $ret = $pinglun->edit($_POST);
            if($ret>0){
                $Cache = Cache::getInstance();
                $pinglun_cache_key = Config::getField('task','task_list_pinglun_key');
                $Cache->ZREM($pinglun_cache_key.$type,$time,$details['content']);
                $Cache->ZADD($pinglun_cache_key.$type,$time,$content);
                Response::apiJsonResult(array(),1,"修改成功");
            }
            Response::apiJsonResult(array(),2,"修改失败");
        }

        $id = Input::request("id",0,'intval');
        $info = $pinglun->findone(array('id'=>$id),array('*'));
        $User_pinglun_type= new User_pinglun_type();
        $tlist = $User_pinglun_type->getAll(array('status'=>0),array("*"));
        $this->assign(array(
            'info'=>$info,
            'tlist'=>$tlist,
        ))->base("task/pinglun_add");
    }

    /**
     * 用户任务后的关注双击记录
     *xu
     */
    public function c_task_photo_log(){

        $uid=Input::request('uid',0,'intval');
        $type=Input::request('type',0,'intval');
        $Account_task_photo_log=new Account_task_photo_log();

        $_GET['page']       = Input::get('page',1,'intval');
        $_GET['pagesize']    = Input::get('pagesize',50,'intval');


        $list = $Account_task_photo_log->getList(array('uid'=>$uid,'type'=>$type),$_GET['page'],$_GET['pagesize']);

        $Page = new Page($list->totalSize,$_GET['pagesize']);

        $this->assign(array(
            'list'=>$list,
            'page'=>$Page->show(),
            'is_used'=>$Account_task_photo_log->is_used,
            'type'=>$Account_task_photo_log->type,
        ))->base();
    }


}