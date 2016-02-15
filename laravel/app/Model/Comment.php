<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午7:32
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

    protected $table = 'comments';
    protected $guarded = ['id'];
    public $timestamps = false;

}