<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午7:31
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model {

    protected $table = 'answers';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function answers() {
        return $this->hasMany('App\Model\Answer', 'question_id', 'id');
    }

    public function ups() {
        return $this->hasMany('App\Model\AnswerUp', 'answer_id', 'id');
    }
}