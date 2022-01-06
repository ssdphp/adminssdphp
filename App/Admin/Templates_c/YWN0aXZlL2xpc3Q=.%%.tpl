<style>
    .table-p{margin: 0;line-height: 1.3}
    .flex{display: flex;}
</style>
<div class="row">
    <div class="col-sm-12">

        <div class="card card-table">
            <div class="card-header py-4">

                <form action="">
                    <div class="row">
                        <div class="col-sm-3 flex">
                            <label class="col-form-label">活动名称</label>
                            <div class="col-sm">
                                <input type="text" name="name" placeholder="请填写活动名称" class="form-control" value=""  autocomplete="off"  maxlength="200">
                            </div>
                        </div>
                        <div class="col-sm-3 flex">
                            <label class="col-form-label">所属项目</label>
                            <div class="col-sm">
                                <select name=""  class="form-control">
                                    <option value="">请选择一个项目</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 flex">
                            <label class="col-form-label">活动类型</label>
                            <div class="col-sm">
                                <select name="" class="form-control">
                                    <option value="">请选择一个类型</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3 flex">
                            <label class="col-form-label">活动状态</label>
                            <div class="col-sm">
                                <select name="" class="form-control">
                                    <option value="">未开始</option>
                                    <option value="">已开始</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-sm-3 flex">
                            <label class="col-form-label">进行时间</label>
                            <div class="col-sm">
                                <input type="text" readonly name="name" placeholder="请选择进行时间" class="form-control" value=""  autocomplete="off" id="showtime"  maxlength="200">
                            </div>
                        </div>
                        <div class="col-sm-3 flex">
                            <label class="col-form-label">显示时间</label>
                            <div class="col-sm">
                                <input type="text" readonly name="name" placeholder="请选择进行时间" class="form-control" value=""  autocomplete="off" id="runtime"  maxlength="200">
                            </div>
                        </div>
                        <div class="col-sm">
                            <a href="/active/add" type="button" class="btn btn-info ">
                                <i class="fa fa-search"></i>
                                搜索
                            </a>
                            <a href="/active/add" type="button" class="btn btn-info ">
                                <i class="fa fa-refresh"></i>
                                重置
                            </a>
                            <a href="/active/add" type="button" class="btn btn-primary ">
                                <i class="fa fa-plus"></i>
                                新增活动
                            </a>
                        </div>

                    </div>

                </form>


            </div>
            <div class="card-body">
                <div class='table-responsive'>
                    <table class="table table-bordered">

                        <thead>

                        <th class="text-nowrap">ID</th>
                        <th class="text-nowrap">活动名称</th>
                        <th class="text-nowrap">所在项目</th>
                        <th class="text-nowrap">活动类型</th>
                        <th class="text-nowrap">所需积分/幸运值</th>
                        <th class="text-nowrap">显示时间</th>
                        <th class="text-nowrap">进行时间</th>
                        <th class="text-nowrap">活动状态</th>
                        <th class="text-nowrap">进行状态</th>
                        <th class="text-nowrap">创建时间</th>
                        <th class="text-nowrap">操作</th>
                        </thead>

                        <tbody>
                        <tr>
                            <td colspan="11">
                                总共发布了<span class="text-danger">10</span>个活动,
                                <span class="">线上<span class="text-danger">5</span>个</span>,
                                <span class="">线下<span class="text-danger">5</span>个</span>
                            </td>
                        </tr>
                        <?php if(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["list"])){; ?>
                        <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["list"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                        <tr>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?></td>
                            <td class="text-nowrap">
                                <a href="/active/edit?id=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" class="" title="编辑活动">
                                    <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['name']; ?>
                                </a>

                            </td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['project_info'])?SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['project_info']['name']:''); ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["online"][SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['online']]; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['need_integral']; ?></td>
                            <td class="text-nowrap">
                                <p class="table-p"><?php echo date('Y-m-d H:i:s',SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['show_start_time']); ?></p>
                                <p class="table-p text-center"> 至 </p>
                                <p class="table-p "><?php echo date('Y-m-d H:i:s',SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['show_end_time']); ?></p>
                            </td>
                            <td class="text-nowrap">
                                <p class="table-p "><?php echo date('Y-m-d H:i:s',SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['start_time']); ?></p>
                                <p class="table-p  text-center"> 至 </p>
                                <p class="table-p "><?php echo date('Y-m-d H:i:s',SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['end_time']); ?></p>
                            </td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["state"][SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['state']]; ?></td>
                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["status"][SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['status']]; ?></td>

                            <td class="text-nowrap"><?php echo SsdPHP\View\Adaptor\tpl_function_echo(!empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['create_time'])?date('Y-m-d H:i',SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['create_time']):'--'); ?></td>
                            <td class="text-nowrap">
                                <div class="btn-group" role="group" aria-label="Basic example">

                                    <a href="/active/edit?id=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" class="btn btn-secondary  btn-sm" data-rel="tooltip" title="修改" data-original-title="Edit">
                                        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i> 编辑活动
                                    </a>
                                    <a href="/active/prize_add?id=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" class="btn btn-secondary  btn-sm" data-rel="tooltip" data-original-title="Edit">
                                        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
                                        奖品配置
                                    </a>
                                    <a href="/project/edit?id=<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['id']; ?>" class="btn btn-secondary  btn-sm" data-rel="tooltip" data-original-title="Edit">
                                        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i> 商家管理
                                    </a>
                                </div>
                            </td>

                        </tr>
                        <?php }; ?>
                        <tr>
                            <td colspan="11">
                                <ul class="pagination">
                                    <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["page"]; ?>
                                </ul>
                            </td>
                        </tr>
                        <?php }else{; ?>
                        <tr>
                            <td colspan="9" class="text-muted" style="text-align: center;">
                                还没有发布过一条活动数据
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




