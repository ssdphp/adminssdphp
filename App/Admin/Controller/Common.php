<?php
/**
 * Created by PhpStorm.
 * User: Young
 * Date: 2016/11/1
 * Time: 下午 17:52
 */

namespace App\Admin\Controller;

use App\Admin\Model\Admin;
use App\Admin\Model\Admin_Auth_Group_Access;
use App\Admin\Model\Admin_Menu;
use SsdPHP\Core\Controller;
use SsdPHP\Http\Input;
use SsdPHP\Http\Response;
use SsdPHP\SsdPHP;
use SsdPHP\Session\Session;
use App\Admin\Model\Operation_log;

class Common extends Controller
{
    static $white_list=[
        "admin/loginout",
        "public/login",
    ];

    public function __construct()
    {
        parent::__construct();
        $currententry = strtolower(SsdPHP::getController()."/".SsdPHP::getAction());


        $adminConfig = Admin::$AdminConfig;
        $adminInfo = Session::get($adminConfig['UserDataKey']);

        if(empty($adminInfo['uid'])){
            if(Input::isAJAX()){
                Response::apiJsonResult(array(), 403);
            }else{
                header("location:/public/login.html");
                exit;
            }
        }
        define("UID", $adminInfo['uid']);
        //权限检查
        $Admin_Auth_Group_Access = new Admin_Auth_Group_Access();
        //权限检测
        $chkRet = $Admin_Auth_Group_Access->AccessCheck($adminInfo['uid']);

        $Admin_Menu = new Admin_Menu();
        $url=$Admin_Menu->getMenuByUrl($currententry);
        if($url){
            $Record=array();
            if($url['pid']==0){
                $Record['c_project']=$url['title'];
                $Record['uid']=$adminInfo['uid'];
                $Record['c_desc']=$url['title'];
            }else{
                $arr=$Admin_Menu->getMenuById($url['pid']);
                $Record['c_project']=$arr['title'];
                $Record['uid']=$adminInfo['uid'];
                $Record['c_desc']=$url['title'];
                if (Input::post()){
                    $data = Input::post();
                    $Record['c_data']=json_encode(array(
                        'data'=>$data
                    ));
                }elseif (Input::get()){
                    $data = Input::get();
                    $Record['c_data']=json_encode(array(
                        'data'=>$data
                    ));
                }
            };

            $Operation_log = new Operation_log();
            $Operation_log->add($Record);
        }


        $ret =$Admin_Menu->handleAdminMenu($currententry);
        $data=array(
            'admin_menu'=>!empty($ret['admin_menu'])?$ret['admin_menu']:array(),
            'nav_url'=>!empty($ret['nav_url'])?$ret['nav_url']:"",
            'AdminInfo'=>$adminInfo,
        );

        $this->assign($data);


        if($chkRet==false && !in_array($currententry,self::$white_list)){
            if(Input::isAJAX()){
                Response::apiJsonResult(array(),1007,"对不起，你没有权限！");
            }else{
                $this->base('common/access',"common/base");
                exit;
            }
        }

    }
}