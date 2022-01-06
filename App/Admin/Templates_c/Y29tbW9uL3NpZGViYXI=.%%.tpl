<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">

            <ul>
                <li class="menu-title">
                    <span>后台</span>
                </li>
                <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["admin_menu"] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["key"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]){; ?>
                <?php if(empty(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['child'])){; ?>
                <li class="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['hover']; ?>">
                    <a href="/<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['url']??''; ?>"><i class="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['icon_class']; ?>"></i> <span><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['title']; ?></span></a>
                </li>

                <?php }else{; ?>

                <li class="submenu ">
                    <a href="javascript:;" class="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['hover']; ?>" ><i class="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['icon_class']; ?>"></i> <span> <?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['title']; ?></span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <?php foreach(SsdPHP\View\Adaptor\Tpl::$_tpl_vars["v"]['child'] as SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_key"]=>SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]){; ?>
                        <li><a class="<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]['hover']; ?>" href="/<?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]['url']; ?>"><?php echo SsdPHP\View\Adaptor\Tpl::$_tpl_vars["_v"]['title']; ?></a></li>
                        <?php }; ?>
                    </ul>
                </li>

                <?php }; ?>

                <?php }; ?>
            </ul>
        </div>
    </div>
</div>