<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["nav_url"][0]["title"]; ?></h3>
            <ul class="breadcrumb">
                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["nav_url"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["key"] => SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>

                <li class="breadcrumb-item"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['title']; ?></li>

                <?php }; ?>
            </ul>
        </div>
    </div>
</div>