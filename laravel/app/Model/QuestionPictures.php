<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午7:45
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuestionPictures extends Model {

    protected $table = 'questions_pics_relate';
    protected $guarded = ['id'];
    public $timestamps = false;

}