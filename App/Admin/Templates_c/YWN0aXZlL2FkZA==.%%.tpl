
<div class="row">

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                新增商场活动
            </div>
            <div class="card-body">
                <div>
                    <form action="" id="form1">
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">所属项目</label>

                        <div class="col-lg-3">
                            <select class="form-control" name="project_id">
                                <option value="0">请选择一个项目</option>
                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["project"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>

                                <option value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]["name"]; ?></option>
                                <?php }; ?>
                            </select>
                            <small class="form-text text-danger">*必选，一个活动只能对应在一个项目上</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">活动名称</label>
                        <div class="col-lg-5">
                            <input type="text" name="name" placeholder="请填写活动名称" class="form-control" value=""  autocomplete="off"  maxlength="200">
                            <small class="form-text text-danger">*必填</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">活动背景图片</label>
                        <div class="col-lg-5">
                            <input type="hidden" name="banner" value="">
                            <button id="upload-banner" style="width: auto" class="btn btn-outline-info">
                                <span class="fa fa-cloud-upload"></span>
                                上传图片
                            </button>

                            <p id="progress" style="display: none">
                                <span class="spinner-border text-primary" style="vertical-align:text-top;width: 1rem;height: 1rem;border-width: 2px" role="status"></span>
                                <span class="progress-num text-primary"></span>
                            </p>


                            <div class="form-text" style="width: 410px;height: 164px">
                                <img id="img" style="min-width: 410px;min-height: 164px;width: auto;height: 100%;border: 1px solid #ccc;padding: 2px" src="" alt="">
                            </div>
                            <small class="form-text text-danger">注：图片推荐大小 710 * 228</small>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">活动类型</label>

                        <div class="col-sm-5 py-2">
                            <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["online"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                            <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]=SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]==1?"checked":""; ?>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label"><input class="form-check-input" type="radio" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?> name="online" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]; ?></label>
                            </div>
                            <?php }; ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2" id="changeIntergral">消耗幸运值</label>
                        <div class="col-lg-5">
                            <input type="text" name="need_integral" placeholder="请输入消耗幸运值" class="form-control" value=""  autocomplete="off"  maxlength="200">
                            <small class="form-text text-danger">*参与活动每次需要消耗积分数量</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">活动展示时间</label>

                        <div class="col-sm-5">
                            <input type="text" id="showtime" name="show_time" placeholder="年-月-日 时:分:秒 - 年-月-日 时:分:秒" class="form-control" value=""  autocomplete="off"  maxlength="200">
                            <small class="form-text text-muted">活动在客户端活动页面显示的时间范围</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">活动进行时间</label>

                        <div class="col-sm-5">
                            <input type="text" id="runtime" name="run_time" placeholder="年-月-日 时:分:秒 - 年-月-日 时:分:秒" class="form-control" value=""  autocomplete="off"  maxlength="200">
                            <small class="form-text text-muted">用户可以参与活动的时间时间范围</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">活动状态</label>

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
                        <label class="col-form-label col-lg-2">是否置顶</label>

                        <div class="col-sm-5 py-2">
                            <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["top"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                            <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]=SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]==0?"checked":""; ?>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label"><input class="form-check-input" type="radio" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?> name="top" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]; ?></label>
                            </div>
                            <?php }; ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-form-label col-lg-2" id="changeContent">领奖须知</label>

                        <div class="col-sm-8">
                            <textarea id="content" name="content"></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-lg-2">排序值</label>
                        <div class="col-lg-2">
                            <input type="number" name="sort" placeholder="" class="form-control" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['sort']??'0'; ?>" autocomplete="off"  maxlength="200">
                            <small class="form-text text-muted">数值越大越靠前</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2"></label>
                        <div class="col-lg-8 py-2">
                            <button  class="btn btn-primary" id="submit" type="button" >新增活动</button>
                            <button class="btn btn-secondary" type="button" onclick="window.history.back();"><< 返回</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



