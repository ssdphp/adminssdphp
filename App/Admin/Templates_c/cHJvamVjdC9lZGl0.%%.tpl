
<div class="row">

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <div class="nav">
                    <a  class="btn btn-outline-primary active" href="#form1" data-toggle="tab">第一步：填写项目信息</a> &nbsp;&nbsp;
                    <a  class="btn btn-outline-primary" href="#form2" id="click_next" data-toggle="tab">第二步：填写公众号配置</a>
                </div>
            </div>
            <div class="card-body">
                <form id="form_sumbit">
                    <div id="form1"  class="active">
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">项目编码</label>
                            <div class="col-lg-5">
                                <input type="text" name="code" placeholder="项目编码" class="form-control" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['code']??''; ?>"  autocomplete="off"  maxlength="200">
                                <small class="form-text text-danger">*必填</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">项目名称</label>
                            <div class="col-lg-5">
                                <input type="text" name="name" placeholder="项目名称" class="form-control" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['name']??''; ?>"  autocomplete="off"  maxlength="200">
                                <small class="form-text text-danger">*必填</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">项目状态</label>

                            <div class="col-sm-5 py-2">
                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["status"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                                <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]=SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]==SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['state']?"checked":""; ?>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label"><input class="form-check-input" type="radio" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?> name="state" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]; ?></label>
                                </div>
                                <?php }; ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">排序值</label>
                            <div class="col-lg-5">
                                <input type="number" name="sort" placeholder="" class="form-control" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['sort']??'0'; ?>" autocomplete="off"  maxlength="200">
                                <small class="form-text text-muted">数值越大越靠前</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2"></label>
                            <div class="col-lg-8 py-2">
                                <button onclick="$('#click_next').click()" class="btn btn-primary"  type="button" >下一步：填写公众号配置</button>
                            </div>
                        </div>
                    </div>
                    <div id="form2" class="fade">
                        <div style="" class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <span class="font-weight-bold">公众号基本配置填写</span> <span class="text-danger">*请由专门的管理人员使用此处</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-2">公众号名称</label>
                                            <div class="col-lg-5">
                                                <input type="text" name="wx_gzh_name" placeholder="微信公众号申请的名称" class="form-control" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['wx_gzh_name']??''; ?>"  autocomplete="off"  maxlength="200">
                                                <small class="form-text text-muted"></small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-2">APPID</label>
                                            <div class="col-lg-5">
                                                <input type="text" name="appid" placeholder="公众号开发的APPID" class="form-control" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['appid']??''; ?>"  autocomplete="off"  maxlength="200">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-2">SECRET</label>

                                            <div class="col-lg-5">
                                                <input type="text" class="form-control" placeholder="公众号开发的SECRET" name="secret" value="<?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['secret'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['secret']:''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo SsdPHP\View\Adaptor\tpl_function_include("project/wxgzh"); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-8 py-2">
                                <input type="hidden" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['gzh_menu']; ?>" name="gzh_menu" id="menu">
                                <input type="hidden" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['id']; ?>" name="id">
                                <button class="btn btn-primary" type="button" id="submit">
                                    保存项目
                                </button>
                                <button class="btn btn-secondary" type="button" onclick="window.history.back();">
                                    << 返回
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


