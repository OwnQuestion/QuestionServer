<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/31
 * Time: 上午10:12
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AnswerUp extends Model {

    protected $table = 'answer_ups';
    protected $guarded = ['id'];
    public $timestamps = false;

}