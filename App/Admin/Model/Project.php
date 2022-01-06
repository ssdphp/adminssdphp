<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/4
 * Time: 15:17
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Project extends Model{

    public $state = array(

        1=>'正常',
        0=>'禁用',
    );
}