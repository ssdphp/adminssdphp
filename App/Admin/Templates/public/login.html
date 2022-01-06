<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>浙地后台管理系统 - 登录</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/admin/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="/admin/css/font-awesome.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="/admin/css/style.css">

    <!--[if lt IE 9]>
    <script src="/admin/js/html5shiv.min.js"></script>
    <script src="/admin/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<!-- Main Wrapper -->
<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                <div class="login-left">
                    <img class="img-fluid" src="/admin/img/logo.png" alt="Logo">
                </div>
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h1>登录</h1>
                        <p class="account-subtitle">浙地后台管理系统</p>

                        <!-- Form -->
                        <form action="#" id="loginForm">
                            <div class="form-group">
                                <input name="username" class="form-control" type="text" placeholder="请输入账号">
                            </div>
                            <div class="form-group">
                                <input name="pwd" class="form-control" type="password" placeholder="请输入密码">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" id="submit" type="button">登录</button>
                            </div>
                        </form>
                        <!-- /Form -->

                        <div class="text-center forgotpass"><a href="javascript:alert('请联系管理员');">忘记密码?</a></div>
                        <div class="login-or">
                            <span class="or-line"></span>
                            <span class="span-or">or</span>
                        </div>

                        <!-- Social Login -->
                        <div class="social-login">
                            <span>通过其他方式登录</span>
                            <a href="#" class="facebook"><i class="fa fa-wechat"></i></a><a href="#" class="google"><i class="fa fa-alipay"></i></a>
                        </div>
                        <!-- /Social Login -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="loginModal">
    <div class="modal-dialog modal-sm modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">登录提示</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex align-content-center">
                <p class="text-justify p-1"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<!-- /Main Wrapper -->

<!-- jQuery -->
<script src="/admin/js/jquery-3.2.1.min.js"></script>
<!-- Bootstrap Core JS -->
<script src="/admin/js/popper.min.js"></script>
<script src="/admin/js/bootstrap.min.js"></script>
<!-- Custom JS -->
<script src="/admin/js/script.js"></script>
<script>

    $(function () {

        $('#submit').on('click',function () {

            if($("input[name='username']").val() == ''){
                $('#loginModal').modal("show")
                $('#loginModal .modal-body').find('p').html("请输入账号")

                $('#loginModal').on('hidden.bs.modal', function (event) {
                    $("input[name='username']").focus()
                })
                return ;
            }
            if($("input[name='pwd']").val() == ''){
                $('#loginModal').modal("show")
                $('#loginModal .modal-body').find('p').html("请输入密码")
                $('#loginModal').on('hidden.bs.modal', function (event) {
                    $("input[name='pwd']").focus()
                })
                return ;
            }
            $('#submit').text("登录中...")
            $.ajax({
                'type':'post',
                'url':'/public/login',
                'data':$('#loginForm').serialize(),
                'dataType':'json',
                'success':function (ret) {
                    if(ret.code == 1){
                        if(ret.data.default_url){
                            window.location.href=ret.data.default_url;
                        }else{
                            window.location.href="/index/index";
                        }
                    }else{
                        $('#submit').text("登录")
                        $('#loginModal').modal("show")
                        $('#loginModal .modal-body').find('p').html(ret.code_str)
                        $("input[name='pwd']").focus()
                    }
                },error:function (){
                    $('#submit').text("登录")
                    $('#loginModal').modal("show")
                    $('#loginModal .modal-body').find('p').html("网络异常,请稍后再试.")
                }
            })
        });

    })
</script>
</body>
</html>
