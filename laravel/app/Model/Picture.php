<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 16/2/18
 * Time: 下午7:55
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model {

    protected $table = 'pictures';
    protected $guarded = ['id'];
    public $timestamps = false;

}