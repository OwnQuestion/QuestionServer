<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/29
 * Time: 下午1:56
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model {

    protected $table = 'user_account';

    // 使用 create 方法存入新的模型数据，新增完后会返回新增的模型实例。
    //但是在新增前，需要先在模型类里设定好 fillable 或 guarded 属性，因为 Eloquent 默认会防止批量赋值。
    //guarded 与 fillable 相反，是作为「黑名单」而不是「白名单」
    protected $guarded = ['id'];

    //fillable 属性指定了哪些字段支持批量赋值 。可以设定在类的属性里或是实例化后设定。
//    protected $fillable = ['first_name', 'last_name', 'email'];

    // 默认情况下，Eloquent 在数据的表中自动地将维护 created_at 和 updated_at 字段。
    //只需简单的添加这些 timestamp 字段到表中，Eloquent 将为您做剩余的工作。
    //如果您不希望 Eloquent 维护这些字段，在模型中添加以下属性：public $timestamps = false;
    public $timestamps = false;

    public function userInfo() {
        return $this->hasOne('App\Model\UserInfo', 'user_id');
    }
    public function userFollows() {

        if ($this == null) {
            return 1;
        }

        return $this->belongsToMany('App\Model\UserAccount', 'user_follows', 'user_id', 'follow_user_id');
    }

    public function userFans() {

        if ($this == null) {
            return 1;
        }

        return $this->belongsToMany('App\Model\UserAccount', 'user_fans', 'user_id', 'fans_user_id');
    }

    public function userTags() {

        if ($this == null) {
            return 1;
        }

        return $this->belongsToMany('App\Model\Tags', 'user_tags', 'user_id', 'tag_id');
    }

    public function questions() {
        return $this->hasMany('App\Model\Question', 'user_id', 'id');
    }
}