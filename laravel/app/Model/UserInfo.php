<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/29
 * Time: 下午1:57
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model {

    protected $table = 'user_info';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function userAccount() {
        /*
         * 想要自己指定外键字段，可以在 belongsTo 方法里传入第二个参数
         * 第三个参数指定要参照上层数据库表的哪个字段
         */
        return $this->belongsTo('App\Model\UserAccount', 'user_id', 'id');
    }

}