
<div class="row">
    <div class="col-sm-12">

        <div class="card card-table">
            <div class="card-header">
                <a href="/admin/admin_add" type="button" class="btn btn-primary btn-bold">
                    <i class="fa fa-plus bigger-110 blue"></i>
                    添加新管理员
                </a>
            </div>
            <div class="card-body">
                <div class='table-responsive'>
                    <table class="table table-bordered">
                        <thead>
                        <th class="text-nowrap">UID</th>
                        <th class="text-nowrap">用户帐号</th>
                        <th class="text-nowrap">用户昵称</th>
                        <th class="text-nowrap">所属项目</th>
                        <th class="text-nowrap">用户权限</th>
                        <th class="text-nowrap">性别</th>
                        <th class="text-nowrap">帐号状态</th>
                        <th class="text-nowrap">创建时间</th>
                        <th class="text-nowrap">操作</th>
                        </thead>
                        <tbody>

                        <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["list"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                        <tr>
                            <td><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['uid']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['username']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['nickname']; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['pid']; ?></td>
                            <td class="text-nowrap">

                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['access_group_ids'] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["va"]){; ?>
                                    <?php if(empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["va"]['title'])){; ?>
                                        未设置
                                    <?php }else{; ?>
                                        <button class="btn btn-sm btn-info" style="margin-top: 6px;">
                                            <span class="fa fa-user-circle-o"></span>
                                            <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["va"]['title']; ?>
                                        </button><br>
                                     <?php }; ?>
                                <?php }; ?>
                            </td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["sex"][SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['sex']]); ?></td>
                            <td class="text-nowrap">
                                <span><?php echo SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["status"][SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['status']]); ?></span>
                            </td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['create_time'])?date('Y-m-d H:i',SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['create_time']):'--'); ?></td>
                            <td class="text-nowrap">
                                <div class="btn-group" role="group" aria-label="Basic example">

                                    <a href="/admin/admin_edit?uid=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['uid']; ?>" class="btn btn-secondary  btn-sm" data-rel="tooltip" title="修改" data-original-title="Edit">
                                        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i> 修改
                                    </a>
                                    <a class="btn btn-sm btn-danger del"  data-uid="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['uid']; ?>" href="javascript:;">
                                        <i class="fa fa-trash-o bigger-120"></i> 删除
                                    </a>
                                    <a class="btn btn-sm btn-info" href="javascript:;">
                                        <i class="fa fa-info"></i> 操作日志
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




