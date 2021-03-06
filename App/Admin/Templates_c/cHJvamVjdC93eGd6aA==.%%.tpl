<link rel="stylesheet" href="/admin/css/wx-custom.css"/>
<div class="card">
    <div class="card-header">
      <span class="font-weight-bold">自定义菜单</span>
  </div>
  <div class="card-body">
    <div class="fluid">
      <div class="pannel">
        <div class="wx-card">
          <div class="wx-box">
            <div class="wx-head"><a href="javascript:;" class="left-icon"><i class="fa fa-chevron-left"></i></a><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]['wx_gzh_name']??''; ?></div>
            <div class="wx-body">
              <p class="content-tips">内容区域</p>
              <div class="wx-footer">
                <li class="add-menu"><i class="fa fa-plus"></i></li>
              </div>
            </div>
          </div>
          <div class="wx-editor">
            <a href="javascript:;" class="editor-load"><i class="fa fa-spinner"></i></a>
            <span class="editor-tips">请点击左侧菜单</span>
            <div class="wx-editor-show">
              <div class="wx-editor-head">菜单编辑区域<a class="menu-delete">删除该菜单</a></div>
              <div class="wx-editor-body">

                <div class="cjb-form-group">
                  <label >菜单名称:</label>
                  <div class="cjb-input-block">
                    <input type="text" class="cjb-input" name="cjb-name" placeholder="菜单名称">
                    <p class="xcx-input-tips">仅支持中英文和数字，字数不超过4个汉字或8个字母</p>
                  </div>
                </div>
                <div class="cjb-form-group cjb-radio-group">
                  <label >菜单类型:</label>
                  <div class="cjb-input-block">
                    <li class="cjb-radio select" type="view">跳转链接</li>
                    <li class="cjb-radio" type="click">发送消息</li>
                    <li class="cjb-radio" type="miniprogram">跳转小程序</li>
                  </div>
                </div>
                <div class="cjb-form-group cjb-val-group">
                  <label>菜单值:</label>
                  <div class="cjb-input-block">
                    <input type="text" class="cjb-input xcx" name="cjb-appid" placeholder="小程序的APPID">

                    <input type="text" class="cjb-input xcx" name="cjb-path" placeholder="跳转到小程序的页面路径">
                    <input type="text" class="cjb-input" name="cjb-val" placeholder="菜单包含的值">
                    <p class="cjb-input-tips">请输入包含http://或者https://的完整链接</p>




                  </div>
                </div>
                <div class="cjb-form-group">
                  <div class="cjb-input-block">
                    <a class="set_menu fluid-btn">保存</a>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
       <!-- <p style="text-align: center"><a class="big-btn fluid-btn create_menu">保存</a> <a class="big-btn fluid-btn" style="background-color: #ccc">取消</a></p>-->

      </div>
    </div>
  </div>
</div>

<script src="/admin/js/jquery1.10.2.js"></script>
<script src="/admin/js/gzh_menu.js"></script>
<script>
  console.log($().jquery); // => '1.11.0'
  $(function (){
    var d = '<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["info"]["gzh_menu"]??""; ?>';

    if (d!="") {
      setMenuList(JSON.parse(d))
    }

  })
</script>
