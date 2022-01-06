<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Video_Product extends ReturnVal {

    //0-正常，1-功能正在维护，3-禁用，4-未开通服务，敬请期待
    private $status=array(
        0=>'正常',
        1=>'功能正在维护',
        3=>'禁用',
        4=>'未开通服务，敬请期待',
    );

    //角标，0-无，1-hot,2-免费，3-赠送

    private $icon_type=array(
        0=>'无',
        1=>'hot',
        2=>'免费',
        3=>'赠送',
    );
    ////////////////////////////////////////////////
    //1-统一社区(1gege),2-新版post社区,3=>'qqbug社区',
    private $shua_pingtai=array(
        "id5299"=>'id5299系统',
        "95system"=>'95社区系统',
    );
    //1-只有qq号码，2-说说id赞，3-浏览量(说说id)，4-日志id赞
    private $page_type=array(
        1=>'正常购买商品',
        2=>'打开WEBurl',
    );

    //特殊产品用于充值
    private $teshu_product_type=array(
        0=>'不是',
        1=>'是',
    );

    /**
     * @return array
     */
    public function getTeshuProductType(): array
    {
        return $this->teshu_product_type;
    }
    //业务处理分类模块
    private $project_cate=array(
        'kuaishou_fans'=>'快手粉丝模块(只要用户id)',
        'kuaishou_zuoping'=>'快手作品链接模块(需要uid,photoid)',
        'qmkg_fans'=>'全民k歌主页UID(用户的主页链接)',
        'qmkg_zuoping'=>'全民k歌作品模块(要作品链接)',
        'douyin_zuoping'=>'抖音模块作品模块(要作品链接)',
        'huoshan_fans'=>'火山刷粉丝模块(只要火山id)',
        'huoshan_video'=>'火山刷作品模块(videoID)',
        'qq'=>'qq号码',
    );

    /**
     * @return array
     */
    public function getProjectCate(): array
    {
        return $this->project_cate;
    }
    //1-需要，2-不需要
    private $need_aid=array(
        1=>'需要',
        2=>'不需要',
    );

    private $soft_list =array(
        0=>'火粉丝商城',
        1=>'快火粉丝商城',
        2=>'抖快火粉丝商城',
        3=>'快手红人神器-南霸弯',
        4=>'快手红人粉丝-南霸弯',
        5=>'快手红人软件-程恒',
        6=>'快手网红加粉-南霸弯',
	    7=>'快手刷粉丝-OPPO',
	    8=>'快火粉丝商城-new',
    );

    /**
     * @return array
     */
    public function getSoftList(): array
    {
        return $this->soft_list;
    }

    /**
     * @return array
     */
    public function getIconType(): array
    {
        return $this->icon_type;
    }
    /**
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getShuaPingtai(): array
    {
        return $this->shua_pingtai;
    }

    /**
     * @return array
     */
    public function getPageType(): array
    {
        return $this->page_type;
    }

    /**
     * @return array
     */
    public function getNeedAid(): array
    {
        return $this->need_aid;
    }


    /**
     * 获取项目
     * @param array $cond
     * @param array $feild
     * @param string $orderby
     * @return array
     */
    public function getList($cond=array(),$page=1,$pagesize=50,$feild=['*'],$orderby="sort desc"){

        $ret = $this->setPage($page,$pagesize)->select($cond,$feild,"",$orderby);

        return $ret;
    }

    /**
     * 通过条件获取一条记录
     * @param array $cond
     * @param array $feild
     * @return array|mixed
     */
    public function findOne($cond=array(),$feild=['*']){
        $ret = $this->selectOne($cond,$feild);
        if(!empty($ret)){
            return $ret;
        }
        return [];
    }

    /**
     * 添加
     * @return array
     */
    public function add($data){
        $retval = $this->getReturnVal();
        if(empty($data)){
            $retval['ret']=0;
            $retval['code']=0;
            return $retval;
        }
        $data['create_time']=time();
        $id = $this->insert($data);

        if(!empty($id)){
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1004;
        return $retval;
    }


    /**
     * 修改
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $retval = $this->getReturnVal();
        if(empty($data) || empty($data['id'])){
            $retval['ret']=0;
            $retval['code']=1005;
            return $retval;
        }
        $id = $this->update(array("id"=>intval($data['id'])),$data);
        //echo $this->getlastsql();
        if(!empty($id)){
            $retval['ret']=$id;
            return $retval;
        }
        $retval['ret']=0;
        $retval['code']=1005;
        return $retval;
    }
}