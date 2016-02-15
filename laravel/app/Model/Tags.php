<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午4:03
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model {

    protected $table = 'tags';
    protected $guarded = ['id'];
    public $timestamps = false;

}