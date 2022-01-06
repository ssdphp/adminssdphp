<?php
/**
 * Created by PhpStorm.
 * User: hteen
 * Date: 2018/7/25
 * Time: 11:56
 */

namespace App\Admin\Model;


use Carbon\Carbon;
use SsdPHP\Core\Model;

class Ditch_Log extends Model
{
    public function _add($did,$uid=0) {
        $model = new Ditch();
        $ditch = $model->findOne(['did' => $did]);

        if (!$ditch)
            return false;

        $data = [
            'ditch_id' => $ditch['id'],
            'did' => $ditch['did'],
            'd_url' => $ditch['d_url'],
            'in_v' => $ditch['in_v'],
            'v' => $ditch['v'],
            'v_str' => $ditch['v_str'],
            'desc' => $ditch['desc'],
            'os' => $ditch['os'],
            'rj' => $ditch['rj'],
            'uid' => $uid,
            'create_time' => time()
        ];

        return $this->insert($data);
    }

}