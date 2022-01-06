<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18
 * Time: 14:06
 */

namespace App\Admin\Model;

use SsdPHP\Core\Model;


class ReturnVal extends Model{
    /**
     * @var array
     */
    private $returnVal=array(
        'ret'=>array(),
        'code'=>1,
        'status'=>1,
    );

    /**
     * @return array
     */
    public function getReturnVal()
    {
        return $this->returnVal;
    }
}