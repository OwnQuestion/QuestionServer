<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/27
 * Time: 下午8:55
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Question extends Model {

    protected $table = 'questions';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function answers() {
        return $this->hasMany('App\Model\Answer', 'question_id', 'id');
    }

    public function questionPics() {
        return $this->belongsToMany('App\Model\QuestionPictures', 'questions_pics_relate', 'question_id', 'pic_id');
    }

    public function questionTags() {
        return $this->belongsToMany('App\Model\QuestionPictures', 'questions_tags_relate', 'question_id', 'tag_id');
    }
}