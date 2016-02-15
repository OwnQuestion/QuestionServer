<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午7:46
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuestionTags extends Model {

    protected $table = 'questions_tags_relate';
    protected $guarded = ['id'];
    public $timestamps = false;

}