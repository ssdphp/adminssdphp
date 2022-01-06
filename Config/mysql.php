<?php
/*
 * mysql配置
 * 如果配置得有Slave，那么认为开启主从读写分离模式
 */
return array(
    "mysql"=>array(
        //多主数据库配置,写分离
        "main"=>array(
            array(
                'host'      =>'127.0.0.1',
                'database'  =>'test',
                'user'      =>'test',
                'password'  =>'test',
                'prefix'    =>'zd_',
                'charset'   =>'utf8mb4',
                'port'      =>'3306',
                'engine'    =>'pdo_mysql',
            )
        )/*,"slave"=>array(
            array(
                'host'      =>'127.0.0.1',
                'database'  =>'test',
                'user'      =>'test',
                'password'  =>'test',
                'prefix'    =>'zd_',
                'charset'   =>'utf8mb4',
                'port'      =>'3306',
                'engine'    =>'pdo_mysql',
            )
        )*/
    )
);