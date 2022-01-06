
<div class="row">

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <p class="card-text">修改管理员账号</p>
            </div>
            <div class="card-body">
                <form id="form1">
                    <input type="hidden" name="uid" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['uid'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['uid']:'0'); ?>">

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">登录账号</label>
                        <div class="col-lg-5">
                            <input type="text" name="username" placeholder="用户登录系统的账号" class="form-control" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['username'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['username']:''); ?>"  autocomplete="off"  maxlength="100">
                            <small class="form-text text-muted"><span class="text-danger">*必填</span> 登录账号为字母数字组合 </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">用户昵称</label>

                        <div class="col-lg-5">
                            <input type="text" class="form-control" placeholder="请输入用户昵称" name="nickname" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['nickname'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['nickname']:''); ?>">
                            <small class="form-text text-danger">*必填</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">联系电话</label>

                        <div class="col-lg-5">
                            <input type="text" class="form-control" placeholder="请输入联系电话" name="link_phone" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['link_phone'])?(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['link_phone']):''); ?>">
                            <small class="form-text text-danger">*必填</small>
                        </div>
                    </div>



                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">密码</label>

                        <div class="col-lg-5">
                            <input type="text" class="form-control" placeholder="请输入密码" name="password" value="" maxlength="32" minlength="6">

                            <small class="form-text text-danger"><span class="text-danger">*必填</span> 密码为数字，字母，下划线组合6-32个字符</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">所属项目</label>

                        <div class="col-lg-3">
                            <select class="form-control" name="pid">
                                <option value="0">请选择一个项目</option>
                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["project"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                                <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["s"]=SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]["id"]==SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['pid']?"selected":""; ?>
                                <option <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["s"]; ?> value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]["name"]; ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">拥有角色权限</label>

                        <div class="col-lg-8 py-2">
                            <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["ids"] = \explode(",",SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['access_group_ids']); ?>
                            <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["access"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                            <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"] = in_array(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id'],SsdPHP\View\Adaptor\Tpl::$_tpl_vars["ids"])?"checked":""; ?>
                            <div class="form-check form-check-inline">

                                <label class="form-check-label"><input class="form-check-input" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?> type="checkbox" name="group_id[]" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]["title"]; ?></label>
                            </div>
                            <?php }; ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">账号状态</label>

                        <div class="col-sm-5 py-2">
                            <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["status"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                            <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"] = (isset(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['status'])&&SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['status']==SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"])?'checked':''; ?>
                            <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["b"] = (empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['status'])&&SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]==1)?'checked':''; ?>
                            <div class="form-check form-check-inline">

                                <label class="form-check-label"><input class="form-check-input" type="radio" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["b"]; ?> name="status" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]; ?></label>
                            </div>
                            <?php }; ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-form-label col-lg-2"></label>
                        <div class="col-lg-8 py-2">
                            <button class="btn btn-primary" type="button" id="submit">
                                保存管理员
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


