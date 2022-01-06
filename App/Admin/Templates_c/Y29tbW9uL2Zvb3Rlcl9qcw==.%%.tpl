
<!-- jQuery -->
<script src="/admin/js/jquery-3.2.1.min.js"></script>
<!-- Bootstrap Core JS -->
<script src="/admin/js/popper.min.js"></script>
<script src="/admin/js/bootstrap.min.js"></script>
<!-- Slimscroll JS -->
<script src="/admin/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- Custom JS -->
<script  src="/admin/js/script.js"></script>
<script src="/admin/js/alertify.min.js"></script>
<script type="text/javascript">
    //override defaults
    alertify.defaults.transition = false;
    alertify.defaults.theme.ok = "btn btn-danger";
    alertify.defaults.theme.cancel = "btn btn-secondary";
    alertify.defaults.theme.input = "form-control";
</script>

    <script src="/admin/plugins/ueditor/ueditor.config.js"></script>
    <script src="/admin/plugins/ueditor/ueditor.all.js"></script>
    <script src="/admin/plugins/ueditor/lang/zh-cn/zh-cn.js"></script>
    <script src="/admin/plugins/plupload/plupload.min.js"></script>
    <script src="/admin/plugins/plupload/upload.js"></script>
    <script src="/admin/plugins/laydate/laydate.js"></script>
    <script>
        window.onbeforeunload=function(e){
            // var e = window.event||e;
            // e.returnValue=("确定离开当前页面吗？");
        }
        $(function(){

            //日期时间范围
            laydate.render({
                elem: '#showtime'
                ,type: 'datetime'
                ,range: true
            });
            //日期时间范围
            laydate.render({
                elem: '#runtime'
                ,type: 'datetime'
                ,range: true
            });

            $('input[name="online"]').on('click',function (){
                if ($(this).val() == 1){
                    $('#changeContent').text("领奖须知")
                    $('#changeIntergral').text("消耗幸运值")
                    $('input[name="need_integral"]').attr('placeholder',"请输入消耗幸运值数量")
                }else{
                    $('#changeContent').text("活动介绍")
                    $('#changeIntergral').text("消耗积分")
                    $('input[name="need_integral"]').attr('placeholder',"请输入消耗积分数量")
                }
            })

            var uploadss = upload({
                id:'upload-banner',
                name:'banner',
                token:'<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["qiniucfg"]["uptoken"]; ?>',
                domain:'<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["qiniucfg"]["domain"]; ?>/',
                init: {
                    FilesAdded: function(up, files) {
                        console.log(files)
                        ctx = []

                        $('#progress').css('display','inline-block')
                        $('#progress').find(".progress-text").html("已选择 "+files[0].name+",共"+((files[0].size/1024/1024).toFixed(2))+"Mb")
                        $('#progress').find(".progress-num").text("已上传: 0%")
                        $('input[name="banner"]').val("")
                        uploadss.start()
                    },
                }
            });

            var ue = UE.getEditor('content', {
                autoHeight: false,
                initialFrameHeight:300,
                serverUrl:  "/system/auth_upload_qiniu",
                toolbars: [
                    ['undo', 'redo','bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist',  'cleardoc','inserttable','simpleupload','insertimage','fullscreen']
                ]

            });
            $('#submit').on('click',function () {

                $('#submit').text("添加中...")
                if ($('input[name="name"]').val() == "") {
                    alertify.error("请填写活动名称")
                    return
                }
                $.ajax({
                    'type':'post',
                    'url':'/active/add',
                    'data':$('#form1').serialize(),
                    'dataType':'json',
                    'success':function (ret) {
                        if(ret.code == 1){
                            alertify.confirm().setting({
                                'invokeOnCloseOff': false,
                                'labels':{ok:'添加奖品', cancel:'稍后在加'},
                                'title':"操作提示",
                                'message': '添加活动成功，是否添加该活动的奖品？' ,
                                'onok': function(){
                                    $('form')[0].reset()
                                    window.location.href="/active/add_prize/id/"+ret.data.id
                                },
                                'oncancel':function () {
                                    window.history.back()
                                }
                            }).show();

                        }else{
                            alertify.error(ret.code_str);
                        }
                        $('#submit').text("新增活动")
                    },'error':function (err) {
                        console.log(err);
                        $('#submit').text("新增活动")
                    }
                });


            });
        });
    </script>



