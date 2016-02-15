<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/29
 * Time: 下午2:21
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserFollows extends Model {

    protected $table = 'user_follows';
    protected $guarded = ['id'];
    public $timestamps = false;

}