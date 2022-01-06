
<div class="row">
    <div class="col-sm-12">

        <div class="card card-table">
            <div class="card-header">
                <a href="/business/add" type="button" class="btn btn-primary btn-bold">
                    <i class="fa fa-plus bigger-110 blue"></i>
                    新增商家
                </a>
            </div>
            <div class="card-body">
                <div class='table-responsive'>
                    <table class="table table-bordered">
                        <thead>
                        <th class="text-nowrap">商家ID</th>
                        <th class="text-nowrap">商家名称</th>
                        <th class="text-nowrap">所属项目</th>
                        <th class="text-nowrap">所属业态</th>
                        <th class="text-nowrap">联系人</th>
                        <th class="text-nowrap">联系电话</th>
                        <th class="text-nowrap">商家状态</th>
                        <th class="text-nowrap">创建时间</th>
                        <th class="text-nowrap">操作</th>
                        </thead>
                        <tbody>

                        <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["list"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                        <tr>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['name']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['department_id']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['industry_id']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['contacts']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['phone']; ?></td>
                            <td class="text-nowrap">
                                <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["s"]=SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['state']==1?'btn-success':'btn-danger'; ?>
                                <a href="javascript:;" class="btn <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["s"]; ?> btn-sm c_state" role="button" sid='<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>' zid="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['state']; ?>" id="state<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>">
                                    <?php SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['state']==1?'在营':'闭店'); ?>
                                </a>
                            </td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['created_at'])?date('Y-m-d H:i',SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['created_at']):'--'); ?></td>
                            <td class="text-nowrap">
                                <div class="btn-group" role="group" aria-label="Basic example">

                                    <a href="/business/edit?id=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" class="btn btn-secondary  btn-sm" data-rel="tooltip" title="修改" data-original-title="Edit">
                                        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i> 修改
                                    </a>
                                    <a href="/business/edit?id=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" class="btn btn-secondary  btn-sm" data-rel="tooltip" data-original-title="Edit">
                                        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i> 业态管理
                                    </a>
                                </div>
                            </td>

                        </tr>
                        <?php }; ?>
                        <tr>
                            <td colspan="7">
                                <ul class="pagination">
                                    <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["page"]; ?>
                                </ul>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<div class="modal" tabindex="-1" id="wxcfgModal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">公众号密钥配置</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-content-center">

                <div class="col-sm-12 row py-2">
                    <label class="col-sm-3 font-weight-bold">公众号</label>
                    <div class="col-sm-9 gzh_name"></div>
                </div>
                <div class="col-sm-12 row py-2">
                    <label class="col-sm-3 font-weight-bold">APPID</label>
                    <div class="col-sm-9 appid"></div>
                </div>
                <div class="col-sm-12 row py-2">
                    <label class="col-sm-3 font-weight-bold">SECRET</label>
                    <div class="col-sm-9 secret"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="craete_gzh_menu">生成公众号菜单</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>


