
<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-header">


                <a class="btn btn-primary" title="添加" href="/auth_group/add">
                    <i class="fa fa-plus bigger-110 blue"></i>
                    添加角色
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover" role="grid" aria-describedby="dynamic-table_info">
                        <thead>
                        <th>ID</th>
                        <th class="text-nowrap">权限组名称</th>
                        <th class="text-nowrap">默认URL</th>
                        <th class="text-nowrap">描述</th>
                        <th class="text-nowrap">状态</th>
                        <th class="text-nowrap">授权管理</th>
                        <th class="table-set"></th>
                        </thead>

                        <tbody>


                        <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["list"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                        <tr role="row">
                            <td><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['title']; ?></td>
                            <td class="text-nowrap"><a href="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['default_url']?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['default_url']:"/"); ?>"><?php echo SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['default_url']?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['default_url']:"/"); ?></a></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['description']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["status"][SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['status']]; ?></td>
                            <td class="text-nowrap">
                                <div class="am-btn-group am-btn-group-xs">
                                    <a  href="/auth_group/access?qid=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" class="btn btn-info btn-round"><span
                                            class="fa fa-universal-access"></span> 访问授权
                                    </a>
                                    <a  href="/auth_group/user_access?qid=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" class="btn btn-info btn-round"><span
                                            class="fa fa-user-circle-o"></span> 用户授权
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="/auth_group/edit?id=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" type="button" class="btn btn-white btn-default btn-xs btn-round"><span class="am-icon-edit"></span>修改/查看</a>

                            </td>
                        </tr>
                        <?php }; ?>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
