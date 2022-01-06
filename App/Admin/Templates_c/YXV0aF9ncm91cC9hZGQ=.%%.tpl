
<div class="row">

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header"><p class="card-text">操作角色权限</p></div>
            <div class="card-body">
                <form id="form1">
                    <input type="hidden" name="id" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['id'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['id']:'0'); ?>">

                    <div class="form-group row">
                        <label class="col-sm-2">角色名称</label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control" placeholder="角色权限组名称" name="title" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['title'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['title']:''); ?>">
                        </div>

                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2">默认访问页</label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control" placeholder="默认URL如:admin/index?parm=x1" name="default_url" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['default_url'])?(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['default_url']):''); ?>">
                        </div>

                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2">描述</label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control" placeholder="描述内容" name="description" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['description'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['description']:''); ?>">

                        </div>

                    </div>


                    <div class="form-group row">
                        <label class="col-sm-2">状态</label>

                        <div class="col-sm-5">
                            <div class="col-xs-10 col-sm-5 radio radio-inline no-padding-left">
                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["status"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                                <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"] = (isset(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['status'])&&SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['status']==SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"])?'checked':''; ?>
                                <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["b"] = (!isset(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['status'])&&0==SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"])?'checked':''; ?>

                                <div class="form-check form-check-inline">

                                    <label class="form-check-label"><input class="form-check-input" type="radio" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["b"]; ?> name="status" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]; ?></label>
                                </div>

                                <?php }; ?>
                            </div>
                        </div>

                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2">排序值</label>

                        <div class="col-sm-5">
                            <input type="text" name="sort" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['sort'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['sort']:'0'); ?>" class="form-control">
                            <small class="form-text text-muted">值越大越靠前</small>

                        </div>

                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2"></label>
                        <div class="col-lg-8 py-2">
                            <button class="btn btn-primary" type="button" id="submit">
                                保存角色
                            </button>
                            <button class="btn btn-secondary" type="button" onclick="window.history.back();">
                                << 返回
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
