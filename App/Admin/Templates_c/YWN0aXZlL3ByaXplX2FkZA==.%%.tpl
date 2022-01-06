<style>
    #prize_config,#product_set,#game_set{display: none}
    #prize_config.active,
    #product_set.active,
    #game_set.active{display: block}
    .h100{height: 100% !important}
    .prize_rule{display: none}
    .prize-icon{width: 50px;height: 50px;border: 1px solid #ccc;padding: 1px}
    .prize-table td{vertical-align: middle!important;}
    .prize-data-one-0{display: table-row}
    .prize-data-one-1{display: none}

</style>
<div class="row">

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header" style="display: flex;align-items: center;justify-content: space-between">

                <div class="nav">
                    <a class="btn btn-outline-primary " id="next1" href="#prize_config" data-toggle="tab">第一步:中奖配置</a>
                    <a class="btn btn-outline-primary mx-2 active" id="next2" href="#product_set" data-toggle="tab">第二步:奖品设置</a>
                    <a class="btn btn-outline-primary" href="#game_set" id="next3" data-toggle="tab">第三步:抽奖页面设置</a>
                </div>

                <span class="mx-2">正在设置 <a class="text-info mx-1" href="/active/edit?id=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["ainfo"]['id']; ?>">[<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["ainfo"]['name']; ?>]</a>活动奖品</span>
            </div>
            <div class="card-body">
                <form id="form_sumbit">
                    <div id="prize_config" class="fade">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <b>中奖基础配置</b>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <label class="col-form-label">每人每天最大中奖
                                            </label>
                                            <div class="col-lg-1 mx-2">
                                                <input type="text" name="code" placeholder="每人每天中奖最大次数" class="form-control text-center" value="1"  autocomplete="off"  maxlength="200">

                                            </div>
                                            <div class="col-lg-1 col-form-label">次</div>
                                            <div class="col-lg col-form-label">
                                                <span class="fa fa-question-circle-o"  data-toggle="tooltip" data-placement="right" title="A会员每天最大能中N次，大于N都为“谢谢参与”"></span>
                                            </div>

                                        </div>

                                        <div class="form-row py-4 ">
                                            <label class="col-form-label">
                                                重复中奖配置
                                            </label>
                                            <div class="col-lg-6 form-row mx-4">
                                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["status"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                                                <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]=SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]==1?"checked":""; ?>
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label"><input class="form-check-input" type="radio" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?> name="state" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]; ?></label>
                                                </div>
                                                <?php }; ?>
                                                <div class="col-lg col-form-label">
                                                    <span class="fa fa-question-circle-o"  data-toggle="tooltip" data-placement="right" title="设置中奖后，之后再参与活动都为“谢谢参与”"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <label class="col-form-label">选择中奖模式</label>

                                            <div class="col-lg-6 form-row mx-4">
                                                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["prize_rule"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                                                <?php SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]=SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]==0?"checked":""; ?>
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label"><input class="form-check-input" type="radio" <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["c"]; ?> name="isjg" value="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["k"]; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]; ?></label>
                                                </div>
                                                <?php }; ?>

                                                <div class="col-lg-2 mx-2 prize_rule">
                                                    <input type="number" name="prize_rule" placeholder="每人每天中奖最大次数" class="form-control text-center" value="10"  autocomplete="off"  maxlength="200">

                                                </div>
                                                <div class="col-lg-1 col-form-label prize_rule">人</div>

                                                <div class="col-lg col-form-label">
                                                    <span class="fa fa-question-circle-o"  data-toggle="tooltip" data-placement="right" title="设置该间隔中奖规则后，每隔N人才中奖，其他参与活动都为“谢谢参与”"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-row py-2">
                                            <button class="btn btn-primary" type="button" onclick="$('#next2').click();">
                                                下一步:奖品配置
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div id="product_set" class="active">

                        <div class="card">
                            <div class="card-header">
                                <p>当前中奖模式为： <b class="" id="prize_rule_name">概率中奖</b></p>
                                <p>选择的抽奖游戏为：<b class="">大转盘</b> <small class="text-warning">必须添加2个以上奖品，并且数量为偶数个</small></p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered prize-table">
                                        <thead>
                                        <th class="text-nowrap">奖品</th>
                                        <th class="text-nowrap">奖品名称</th>
                                        <th class="text-nowrap">奖品图标</th>
                                        <th class="text-nowrap">
                                            中奖概率
                                            <span class="fa fa-question-circle-o"  data-toggle="tooltip" data-placement="top" title="通过中奖基础配置设置是否是间隔人数中奖，不是的话将只能设置奖品为概率中奖,否则为设置人数间隔模式"></span>
                                        </th>
                                        <th class="text-nowrap">
                                            每日最大中奖数量
                                            <span class="fa fa-question-circle-o"  data-toggle="tooltip" data-placement="top" title="指每日投放的奖品数量，超过该数量，后面参与都为“谢谢参与”"></span></th>
                                        <th class="text-nowrap">
                                            奖品库存总数
                                            <span class="fa fa-question-circle-o"  data-toggle="tooltip" data-placement="top" title="指奖品库存数量，会员中奖时该数值会减少直至库存为0，超过该数量，后面参与均为“谢谢参与”"></span>
                                        </th>
                                        <th class="text-nowrap">操作</th>
                                        </thead>
                                        <tbody id="prizetbBody">
                                        <tr class="prize-data-one-0">
                                            <td class="text-nowrap">谢谢参与</td>
                                            <td>谢谢参与</td>
                                            <td class="text-center">
                                                <img src="http://trusteeship-zdgj.oss-cn-beijing.aliyuncs.com/55d962756608764a81f6a6c9f2a31544.png" class="prize-icon">
                                            </td>
                                            <td align="center">
                                                <div class="row px-0 mx-0">
                                                    <input type="text" name="goods[][chance]" value="100" class="form-control col-sm prize-data-one-0-chance" placeholder="设置中奖概率">
                                                    <span class="mx-1 col-form-label">%</span>

                                                </div>
                                            </td>
                                            <td align="center">无限</td>
                                            <td align="center">无限</td>
                                            <td align="center" class="text-nowrap"></td>
                                        </tr>
                                        <tr class="prize-data-one-1">
                                            <td class="text-nowrap">谢谢参与</td>
                                            <td class="text-nowrap">谢谢参与</td>
                                            <td class="text-center">
                                                <img class="prize-icon" src="http://trusteeship-zdgj.oss-cn-beijing.aliyuncs.com/55d962756608764a81f6a6c9f2a31544.png" alt="" >
                                            </td>
                                            <td align="center">
                                                <div class="row px-0 mx-0">间隔人数内必中</div>
                                            </td>
                                            <td align="center">无限</td>
                                            <td align="center">无限</td>
                                            <td align="center" class="text-nowrap"></td>
                                        </tr>
                                        <!--<tr style="display: none">
                                            <td class="text-nowrap">奖品一</td>
                                            <td>
                                                <input type="text" value="" class="form-control col-sm" placeholder="奖品名称">
                                            </td>
                                            <td>
                                                <img src="" alt="" class="prize-icon">
                                            </td>
                                            <td align="center">
                                                <div class="row px-0 mx-0">
                                                    <input type="text" class="form-control col-sm" placeholder="设置中奖概率">
                                                    <span class="mx-1 col-form-label">%</span>

                                                </div>
                                            </td>
                                            <td align="center">
                                                <div class="row px-0 mx-0">
                                                    <input type="text" value="" class="form-control col-sm" placeholder="每日最大中奖数量">
                                                </div>
                                            </td>
                                            <td align="center">
                                                <div class="row px-0 mx-0">
                                                    <input type="text" class="form-control col-sm" placeholder="奖品库存总数">
                                                </div>
                                            </td>
                                            <td align="center" class="text-nowrap">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" >
                                                    <i class="fa fa-trash-o"></i> 删除
                                                </button>
                                            </td>
                                        </tr>-->
                                        <tr id="prizetbData">
                                            <td colspan="7">
                                                <button class="btn btn-primary" type="button" id="addprize-btn">
                                                    <i class="fa fa-plus"></i>新增奖品
                                                </button>
                                                <button class="btn btn-primary" type="button" onclick="$('#next1').click()">
                                                    上一步：中奖配置
                                                </button>
                                                <button class="btn btn-primary" type="button" onclick="$('#next3').click()">
                                                    下一步：抽奖页面设置
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="game_set" class="fade">
                        <div class="row">
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
                                <input type="hidden" value="" name="gzh_menu" id="menu">
                                <button class="btn btn-primary" type="button" id="submit">
                                    新增项目
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



