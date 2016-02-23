<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 16/2/23
 * Time: 下午1:21
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AnswerPictures extends Model {

    protected $table = 'answers_pics_relate';
    protected $guarded = ['id'];
    public $timestamps = false;

}