<?php
if($vendorFile = realpath(__DIR__.'/../vendor/autoload.php')){
    require $vendorFile;
}else{
    require realpath(__DIR__.'/../vendor/ssdphp/ssdphp/SsdPHP.php');
}

if(($r = SsdPHP\SsdPHP::Bootstrap(function (){
    date_default_timezone_set('PRC');
    $appRoot = dirname(__DIR__);

    SsdPHP\SsdPHP::setAppDir($appRoot);
    SsdPHP\SsdPHP::setDebug(true);
    SsdPHP\Core\Error::$CONSOLE =SsdPHP\SsdPHP::getDebug();
    //加载配置文件
    SsdPHP\Core\Config::load($appRoot."/Config");

    //加载model
    if (stripos($_SERVER['HTTP_HOST']??"", "admin") !== false) {
        $model = "Admin";
	SsdPHP\SsdPHP::setDefaultAction("index");
        SsdPHP\SsdPHP::setDefaultController("index");
    }elseif(stripos($_SERVER['HTTP_HOST']??"", "api") !== false) {
        $model = "Api";
	SsdPHP\SsdPHP::setDefaultAction("");
        SsdPHP\SsdPHP::setDefaultController("");
    }elseif(stripos($_SERVER['HTTP_HOST']??"", "www") !== false) {
        $model = "Web";
    }elseif(stripos($_SERVER['HTTP_HOST']??"", "home") !== false) {
        $model = "Home";
        SsdPHP\SsdPHP::setDefaultAction("auth_index");
        SsdPHP\SsdPHP::setDefaultController("index");
    } elseif (stripos($_SERVER['HTTP_HOST'] ?? "", "push") !== false) {
        $model = "Push";
        SsdPHP\SsdPHP::setDefaultAction("");
        SsdPHP\SsdPHP::setDefaultController("");
    }else {
        $model = "Home";
	SsdPHP\SsdPHP::setDefaultAction("auth_index");
        SsdPHP\SsdPHP::setDefaultController("index");
    }
    $lowerModel = strtolower($model);
    SsdPHP\SsdPHP::setModel($model);
    SsdPHP\Core\Route::set(SsdPHP\Core\Config::getField('route',$lowerModel));
    SsdPHP\Core\Language::load($appRoot."/resources/lang/zh/",$lowerModel);
    SsdPHP\Session\Session::Start(SsdPHP\Core\Config::getField('session',$lowerModel));
})->Run()) === false){
    header('HTTP/1.1 404 Not Found');
    header('Status: 404 Not Found');
    echo "404 error!";
}else{
    echo $r;
}
