<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午1:28
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserFans extends Model {

    protected $table = 'user_fans';
    protected $guarded = ['id'];
    public $timestamps = false;

}