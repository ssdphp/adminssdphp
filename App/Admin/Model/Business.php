<?php
/**
 * Created by PhpStorm.
 * User: 许
 * Date: 2021-5-26
 * Time: 10:39
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;

class Business extends Model{

    /**
     * 营业状态
     */
    public $state = array(

        1=>'在营',
        0=>'闭店',
    );
}