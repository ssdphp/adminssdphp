<div class="row">

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <p class="card-text">添加新的商家</p>
            </div>
            <div class="card-body">
                <form id="form1">
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">商家名称</label>
                        <div class="col-lg-5">
                            <input type="text" name="name" placeholder="商家名称" class="form-control"
                                   value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['name'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['name']:''); ?>" autocomplete="off" maxlength="200">
                            <small class="form-text text-danger">*必填</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">所属项目</label>

                        <div class="col-lg-3">
                            <select class="form-control" name="project_id">
                                <option value="0">请选择一个项目</option>
                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["project"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                                <option value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]["name"]; ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">所属业态</label>

                        <div class="col-lg-8 py-2">

                            <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["industry"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>

                            <div class="form-check form-check-inline">

                                <label class="form-check-label"><input class="form-check-input" type="checkbox" name="industry_id" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]["name"]; ?></label>
                            </div>
                            <?php }; ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">联系人昵称</label>
                        <div class="col-lg-5">
                            <input type="text" name="contacts" placeholder="联系人昵称" class="form-control"
                                   value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['contacts'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['contacts']:''); ?>" autocomplete="off" maxlength="200">
                            <small class="form-text text-danger">*必填</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">联系人电话</label>
                        <div class="col-lg-5">
                            <input type="text" name="phone" placeholder="联系人电话" class="form-control"
                                   value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['phone'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['phone']:''); ?>" autocomplete="off" maxlength="200">
                            <small class="form-text text-danger">*必填</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">商家状态</label>

                        <div class="col-sm-5 py-2">
                            <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["state"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                            <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]=SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]==1?"checked":""; ?>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label"><input class="form-check-input" type="radio" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?> name="state" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]; ?></label>
                            </div>
                            <?php }; ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">版本号</label>
                        <div class="col-lg-5">
                            <input type="text" name="version" placeholder="版本号" class="form-control"
                                   value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['version'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['version']:''); ?>" autocomplete="off" maxlength="200">
                            <small class="form-text text-danger">*必填</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-8 py-2">
                            <button class="btn btn-primary" type="button" id="submit">
                                新增商家
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
