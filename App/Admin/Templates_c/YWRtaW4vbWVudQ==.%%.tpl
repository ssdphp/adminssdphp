

<div class="row">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
                <div class="" role="group" aria-label="Basic example">
                    <button class="btn btn-outline-info btn-sm" id="add_menu">
                        <i class="normal-icon ace-icon fa fa-plus blue bigger-130"></i> 添加根菜单
                    </button>
                    <button class="btn  btn-outline-secondary btn-sm" autocomplete="off" data-loading-text="保存中..." id="save">
                        <i class="normal-icon ace-icon fa fa-save blue bigger-130"></i> 保存
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="zedie">
                        <i class="normal-icon ace-icon fa fa-angle-double-up blue bigger-130"></i> 折叠
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="zankai">
                        <i class="normal-icon ace-icon fa fa-angle-double-down blue bigger-130"></i> 展开
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- PAGE CONTENT BEGINS -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="dd dd-draghandle dd-menu">
                            <ol class="dd-list">
                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["list"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                                <li class="dd-item dd2-item" data-id="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>">
                                    <div class="dd-handle dd2-handle">
                                        <i class="normal-icon ace-icon <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['icon_class']; ?> blue bigger-130"></i>
                                        <i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
                                    </div>
                                    <div class="dd2-content">
                                        <span class="tt"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['title']; ?></span>
                                        <div class="pull-right action-buttons">
                                            <label class="inline" title="菜单开关控制">
                                                <input <?php SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]["status"]==1?"checked='checked'":""); ?> type="checkbox" class="ace ace-switch ace-switch-3">
                                                <span class="lbl middle"></span>
                                            </label>
                                            <a class="blue add_menu_nood" href="javascript:;" title="添加子节点">
                                                <i class="ace-icon fa fa-plus bigger-130"></i>
                                            </a>
                                            <a class="blue edit" href="javascript:;" title="编辑">
                                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                                            </a>

                                            <a class="red trash" href="javascript:;" title="删除">
                                                <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <?php if(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['child'])){; ?>
                                    <ol class="dd-list">
                                        <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['child'] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]){; ?>

                                        <li class="dd-item dd2-item" data-id="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]['id']; ?>">
                                            <div class="dd-handle dd2-handle">
                                                <i class="normal-icon ace-icon fa fa-angle-right red bigger-130"></i>
                                                <i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
                                            </div>
                                            <div class="dd2-content">
                                                <span class="tt"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]['title']; ?></span>
                                                <div class="pull-right action-buttons">
                                                    <label class="inline" title="菜单开关控制">
                                                        <input <?php SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]["status"]==1?"checked='checked'":""); ?> type="checkbox" class="ace ace-switch ace-switch-3">
                                                        <span class="lbl middle"></span>
                                                    </label>

                                                    <a class="blue add_menu_nood" href="javascript:;" title="添加子节点">
                                                        <i class="ace-icon fa fa-plus bigger-130"></i>
                                                    </a>

                                                    <a class="blue edit" href="javascript:;" title="编辑">
                                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                                    </a>

                                                    <a class="red trash" href="javascript:;" title="删除">
                                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php if(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]['child'])){; ?>
                                            <ol class="dd-list">
                                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]['child'] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["__v"]){; ?>

                                                <li class="dd-item dd2-item" data-id="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["__v"]['id']; ?>">
                                                    <div class="dd-handle dd2-handle">
                                                        <i class="normal-icon ace-icon fa fa-ellipsis-h bigger-130"></i>
                                                        <i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
                                                    </div>
                                                    <div class="dd2-content">
                                                        <span class="tt"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["__v"]['title']; ?></span>
                                                        <div class="pull-right action-buttons">

                                                            <label class="inline" title="菜单开关控制">
                                                                <input <?php SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["__v"]["status"]==1?"checked='checked'":""); ?> type="checkbox" class="ace ace-switch ace-switch-3">
                                                                <span class="lbl middle"></span>
                                                            </label>
                                                            <a class="blue edit" href="javascript:;" title="编辑">
                                                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                                                            </a>

                                                            <a class="red trash" href="javascript:;" title="删除">
                                                                <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>

                                                <?php }; ?>
                                            </ol>
                                            <?php }; ?>
                                        </li>

                                        <?php }; ?>
                                    </ol>
                                    <?php }; ?>

                                </li>
                                <?php }; ?>


                            </ol>
                        </div>
                    </div>

                </div><!-- PAGE CONTENT ENDS -->
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="ace-icon fa fa-recycle bigger-130"></i> 回收站</h5>
            </div>
            <div class="card-body">
                <div class="col-sm-12">

                    <div class="dd dd-draghandle">
                        <ol class="dd-list">
                            <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["del_list"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                            <li class="dd-item dd2-item" data-id="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>">
                                <div class="dd-handle dd2-handle">
                                    <i class="normal-icon ace-icon fa fa-trash-o blue bigger-130"></i>
                                    <i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
                                </div>
                                <div class="dd2-content">
                                    <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['title']; ?>
                                    <div class="pull-right action-buttons">
                                        <label class="inline" title="菜单开关控制">
                                            <input <?php SsdPHP\View\Adaptor\tpl_function_echo(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]["status"]==1?"checked='checked'":""); ?> type="checkbox" class="ace ace-switch ace-switch-3">
                                            <span class="lbl middle"></span>
                                        </label>
                                        <a class="blue edit" href="javascript:;" title="编辑">
                                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                                        </a>

                                        <a class="red retweet" href="javascript:;" title="恢复">
                                            <i class="ace-icon fa fa-retweet bigger-130"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <?php }; ?>


                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加菜单</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <form class="" id="menu_add_form">
                        <input type="hidden" name="pid" value="0">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">菜单名</label>
                            <div class="col-sm-5">
                                <input type="text" name="title" class="form-control"  placeholder="菜单名字">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">跳转URL</label>
                            <div class="col-sm-5">
                                <input type="text" name="url" class="form-control" id="add_label_id_menu_url" placeholder="Controller/action">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">ICON样式</label>
                            <div class="col-sm-5">
                                <input type="text" name="icon_class" class="form-control" placeholder="fa fa-user">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"  id="menu_add_form_btn" class="btn btn-primary btn-round btn-sm">添加</button>
                <button type="button" class="btn btn-secondary btn-round btn-sm" data-dismiss="modal">取消</button>

            </div>
        </div>
    </div>
</div>
<!-- edit menu Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">修改菜单名和URL</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <form class="" id="menu_edit_form">
                        <input type="hidden" name="id" id="id" value="0" >
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">菜单名</label>
                            <div class="col-sm-5">
                                <input type="text" name="title" class="form-control" id="label_id_menu" placeholder="菜单名字">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">跳转URL</label>
                            <div class="col-sm-5">
                                <input type="text" name="url" class="form-control" id="label_id_menu_url" placeholder="Controller/action">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">标题样式</label>
                            <div class="col-sm-5">
                                <input type="text" name="icon_class" class="form-control" placeholder="Controller/action">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"  id="menu_edit_form_btn" class="btn btn-primary btn-round">保存</button>
                <button type="button" class="btn btn-secondary btn-round" data-dismiss="modal">取消</button>

            </div>
        </div>
    </div>
</div>
<!-- page specific plugin scripts -->
