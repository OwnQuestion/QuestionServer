<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午4:21
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserTags extends Model {

    protected $table = 'user_tags';
    protected $guarded = ['id'];
    public $timestamps = false;

}