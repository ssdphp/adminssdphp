<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>浙地国际-后台管理系统</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/admin/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/admin/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="/admin/css/font-awesome.min.css">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="/admin/css/feathericon.min.css">

    <link rel="stylesheet" href="/admin/plugins/morris/morris.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="/admin/css/style.css">

    <!--alertifyjs css-->
    <link rel="stylesheet" href="/admin/css/alertify.min.css"/>
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="/admin/css/themes/bootstrap.min.css"/>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!--[if lt IE 9]>
    <script src="/admin/js/html5shiv.min.js"></script>
    <script src="/admin/js/respond.min.js"></script>
    <![endif]-->
    <style>

        .dd,.dd-list {
            display: block;
            padding: 0;
            list-style: none
        }

        .dd,.dd-item>button,.dd-list {
            position: relative
        }
        .pull-right {
            float: right;
            display: flex;
        }

        @media only screen and (max-width: 767px) and (-webkit-min-device-pixel-ratio:0) {
            .ui-jqgrid .ui-jqgrid-pager>.ui-pager-control>.ui-pg-table>tbody>tr>td#grid-pager_center>.ui-pg-table {
                width:300px
            }
        }

        .dd {
            margin: 0;
            max-width: 600px;
            line-height: 20px
        }

        .dd-list {
            margin: 0
        }

        .dd-list .dd-list {
            padding-left: 30px
        }

        .dd-collapsed .dd-list {
            display: none
        }

        .dd-empty,.dd-item,.dd-placeholder {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            min-height: 20px;
            line-height: 20px
        }

        .dd-handle,.dd2-content {
            display: block;
            min-height: 38px;
            margin: 5px 0;
            padding: 8px 12px;
            background: #F8FAFF;
            border: 1px solid #DAE2EA;
            color: #7C9EB2;
            text-decoration: none;
            font-weight: 700;
            box-sizing: border-box
        }

        .dd-handle:hover,.dd2-content:hover {
            color: #438EB9;
            background: #F4F6F7;
            border-color: #DCE2E8
        }

        .dd-handle[class*=btn-],.dd2-content[class*=btn-] {
            color: #FFF;
            border: none;
            padding: 9px 12px
        }

        .dd-handle[class*=btn-]:hover,.dd2-content[class*=btn-]:hover {
            opacity: .85;
            color: #FFF
        }

        .dd2-handle+.dd2-content,.dd2-handle+.dd2-content[class*=btn-] {
            padding-left: 44px
        }

        .dd-handle[class*=btn-]:hover,.dd2-content[class*=btn-] .dd2-handle[class*=btn-]:hover+.dd2-content[class*=btn-] {
            color: #FFF
        }

        .dd-item>button:hover~.dd-handle,.dd-item>button:hover~.dd2-content {
            color: #438EB9;
            background: #F4F6F7;
            border-color: #DCE2E8
        }

        .dd-item>button:hover~.dd-handle[class*=btn-],.dd-item>button:hover~.dd2-content[class*=btn-] {
            opacity: .85;
            color: #FFF
        }

        .dd2-handle:hover~.dd2-content {
            color: #438EB9;
            background: #F4F6F7;
            border-color: #DCE2E8
        }

        .dd2-handle:hover~.dd2-content[class*=btn-] {
            opacity: .85;
            color: #FFF
        }

        .dd2-item.dd-item>button {
            margin-left: 34px
        }

        .dd-item>button {
            display: block;
            z-index: 1;
            cursor: pointer;
            float: left;
            width: 25px;
            height: 20px;
            margin: 5px 1px 5px 5px;
            padding: 0;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 0;
            background: 0 0;
            font-size: 12px;
            line-height: 1;
            text-align: center;
            font-weight: 700;
            top: 4px;
            left: 1px;
            color: #707070
        }

        .dd-item>button:before {
            font-family: FontAwesome;
            content: '\f067';
            display: block;
            position: absolute;
            width: 100%;
            text-align: center;
            text-indent: 0;
            font-weight: 400;
            font-size: 14px
        }

        .dd-item>button[data-action=collapse]:before {
            content: '\f068'
        }

        .dd-item>button:hover {
            color: #707070
        }

        .dd-item.dd-colored>button,.dd-item.dd-colored>button:hover {
            color: #EEE
        }

        .dd-empty,.dd-placeholder {
            margin: 5px 0;
            padding: 0;
            min-height: 30px;
            background: #F0F9FF;
            border: 2px dashed #BED2DB;
            box-sizing: border-box
        }

        .dd-empty {
            border-color: #AAA;
            border-style: solid;
            background-color: #e5e5e5
        }

        .dd-dragel {
            position: absolute;
            pointer-events: none;
            z-index: 999;
            opacity: .8
        }

        .dd-dragel>li>.dd-handle {
            color: #4B92BE;
            background: #F1F5FA;
            border-color: #D6E1EA;
            border-left: 2px solid #777;
            position: relative
        }

        .dd-dragel>li>.dd-handle[class*=btn-] {
            color: #FFF
        }

        .dd-dragel>.dd-item>.dd-handle {
            margin-top: 0
        }

        .dd-list>li[class*=item-] {
            border-width: 0;
            padding: 0
        }

        .dd-list>li[class*=item-]>.dd-handle {
            border-left: 2px solid;
            border-left-color: inherit
        }

        .dd-list>li>.dd-handle .sticker {
            position: absolute;
            right: 0;
            top: 0
        }

        .dd-dragel>li>.dd2-handle,.dd2-handle {
            left: 0;
            top: 0;
            width: 36px;
            margin: 0;
            text-align: center;
            padding: 0!important;
            line-height: 38px;
            height: 38px;
            background: #EBEDF2;
            border: 1px solid #DEE4EA;
            cursor: pointer;
            overflow: hidden;
            position: absolute;
            z-index: 1
        }

        .dd-dragel>li>.dd2-handle,.dd2-handle:hover {
            background: #E3E8ED
        }

        .dd2-handle[class*=btn-] {
            text-shadow: none!important;
            background: rgba(0,0,0,.1)!important;
            border-right: 1px solid #EEE
        }

        .dd2-handle[class*=btn-]:hover {
            background: rgba(0,0,0,.08)!important
        }

        .dd-dragel .dd2-handle[class*=btn-] {
            border-color: transparent #EEE transparent transparent
        }

        .dd2-handle.btn-yellow {
            background: rgba(0,0,0,.05)!important;
            border-right: 1px solid #FFF
        }

        .dd2-handle.btn-yellow:hover {
            background: rgba(0,0,0,.08)!important
        }

        .dd-dragel .dd2-handle.btn-yellow {
            border-color: transparent #FFF transparent transparent
        }

        .dd-item>.dd2-handle .drag-icon {
            display: none
        }

        .dd-dragel>.dd-item>.dd2-handle .drag-icon {
            display: inline
        }

        .dd-dragel>.dd-item>.dd2-handle .normal-icon {
            display: none
        }

        .dropzone {
            border-radius: 0;
            border: 1px solid rgba(0,0,0,.06)
        }

        .dropzone.well {
            background-color: #f5f5f5;
            border: 1px solid #e3e3e3
        }

        .dropzone .dz-default.dz-message {
            background-image: none;
            font-size: 24px;
            text-align: center;
            line-height: 32px;
            left: 0;
            width: 100%;
            margin-left: auto
        }

        .dropzone .dz-default.dz-message span {
            display: inline;
            color: #555
        }

        .dropzone .dz-default.dz-message span .upload-icon {
            opacity: .7;
            filter: alpha(opacity=70);
            margin-top: 8px;
            cursor: pointer
        }

        .dropzone .dz-default.dz-message span .upload-icon:hover {
            opacity: 1;
            filter: alpha(opacity=100)
        }

        .dropzone .dz-preview.dz-image-preview {
            background-color: transparent
        }

        .action-buttons a {
            margin: 0 3px;
            display: inline-block;
            opacity: .85;
            -webkit-transition: all .1s;
            -o-transition: all .1s;
            transition: all .1s
        }

        .action-buttons a:hover {
            text-decoration: none;
            opacity: 1;
            -moz-transform: scale(1.2);
            -webkit-transform: scale(1.2);
            -o-transform: scale(1.2);
            -ms-transform: scale(1.2);
            transform: scale(1.2)
        }

        /* Switch开关样式 */
        input[type='checkbox'].ace-switch {
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            position: relative;
            width: 40px;
            height: 20px;
            background: #ccc;
            border-radius: 10px;
            transition: border-color .3s, background-color .3s;
        }

        input[type='checkbox'].ace-switch::after {
            content: '';
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: #fff;
            transition: .4s;
            top: 2px;
            position: absolute;
            left: 2px;
        }

        input[type='checkbox'].ace-switch:checked {
            background: #8AB2C9;
        }

        /* 当input[type=checkbox]被选中时：伪元素显示下面样式 位置发生变化 */
        input[type='checkbox'].ace-switch:checked::after {
            content: '';
            position: absolute;
            left: 55%;
            top: 2px;
        }
        .tt{font-size: 14px}


    </style>
</head>
<body>

<!-- Main Wrapper -->
<div class="main-wrapper">

    <!-- Header -->
    <?php echo SsdPHP\View\Adaptor\tpl_function_include('common/header'); ?>
    <!-- /Header -->

    <!-- Sidebar -->
    <?php echo SsdPHP\View\Adaptor\tpl_function_include('common/sidebar'); ?>
    <!-- /Sidebar -->


    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <?php echo SsdPHP\View\Adaptor\tpl_function_include('common/nav'); ?>
            <!-- /Page Header -->
            <!-- Page Content -->
            <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["content"]; ?>
            <!-- /Page Content -->

        </div>
    </div>
    <!-- /Page Wrapper -->

</div>
<!-- /Main Wrapper -->
<?php echo SsdPHP\View\Adaptor\tpl_function_include('common/footer_js'); ?>
</body>
</html>
