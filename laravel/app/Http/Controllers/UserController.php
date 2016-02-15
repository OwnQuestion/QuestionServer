<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 上午11:14
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthController;
use App\Model\UserAccount;
use App\Model\UserFans;
use App\Model\UserInfo;
use App\Model\UserFollows;
use App\Model\UserTags;
use Illuminate\Http\Request;
use App\Functions\Utility;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;

class UserController extends Controller
{
    public function getUserFollows()
    {
        $request = Request::capture();
        $token = $request->input('token');
        $followUserId = $request->input('userId');

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        if ($followUserId == null)
        {
            // 如果参数userId为空 则赋值token身份中的userId
            $followUserId = $userId;
        }

        // token 认证

        $result = array();
        $userFollows = UserAccount::find($followUserId)->userFollows;

        foreach ($userFollows as $follow)
        {
            $info = UserInfo::where('user_id', $follow->id)->first();
            if ($info != null) {
                array_push($result, $info);
            }
        };

        return $result;
    }

    public function getUserFans()
    {
        $request = Request::capture();
        $token = $request->input('token');
        $userFansId = $request->input('userId');

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        if ($userFansId == null)
        {
            // 如果参数userId为空 则赋值token身份中的userId
            $userFansId = $userId;

        }

        // token 认证

        $result = array();
        $userFans = UserAccount::find($userFansId)->userFans;

        foreach ($userFans as $fans)
        {
            $info = UserInfo::where('user_id', $fans->id)->first();
            if ($info != null) {
                array_push($result, $info);
            }
        };

        return $result;
    }

    public function addFollowUser()
    {
        $request = Request::capture();
        $token = $request->input('token');
        // 被关注人id
        $followUserId = $request->input('userId');

        if ($followUserId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', 'userId不能为空');
        }

        // 用户id
        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        DB::beginTransaction();
        try {
            $follow = UserFollows::create(['user_id' => $userId, 'follow_user_id' => $followUserId]);
            $fans = UserFans::create(['user_id' => $followUserId, 'fans_user_id' => $userId]);

            DB::commit();
            if ($follow == null || $fans == null)
            {
                return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '关注失败');
            }
            else
            {
                return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '关注成功');
            }

        } catch (Exception $e) {
            DB::rollBack();
            return Utility::response_format(Utility::RESPONSE_CODE_DB_ERROR, '', $e->getMessage());
        }
    }

    public function deleteFollowUser() {

        $request = Request::capture();
        $token = $request->input('token');
        // 被关注人id
        $followUserId = $request->input('userId');

        if ($followUserId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', 'userId不能为空');
        }

        // 用户id
        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        DB::beginTransaction();
        try {
            $follow = UserFollows::where('user_id', $userId)->where('follow_user_id', $followUserId)->delete();
            $fans = UserFans::where('user_id', $followUserId)->where('fans_user_id', $userId)->delete();

            DB::commit();
            if ($follow == null || $fans == null)
            {
                return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '取消关注失败');
            }
            else
            {
                return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '取消关注成功');
            }

        } catch (Exception $e) {
            DB::rollBack();
            return Utility::response_format(Utility::RESPONSE_CODE_DB_ERROR, '', $e->getMessage());
        }
    }

    public function getUserInfo() {

        $request = Request::capture();
        $token = $request->input('token');
        $userId = $request->input('userId');

        if ($userId == null)
        {
            // 如果参数userId为空 则赋值token身份中的userId
            $userId = AuthController::getUserIdByToken($token);
            if ($userId == null)
            {
                return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
            }
        }

        // token 认证

        $userInfo = UserAccount::find($userId)->userInfo;
        $userTags = UserAccount::find($userId)->userTags;
        $userInfo['tags'] = $userTags;

        return $userInfo;

    }

    public function addUserTags() {

        $request = Request::capture();
        $token = $request->input('token');
        $tags = $request->input('tags');

        if ($tags == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '标签不能为空');
        }

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        DB::beginTransaction();
        try {
            $array = explode(',', $tags);

            $result = UserTags::where('user_id', $userId)->delete();
            foreach ($array as $tag)
            {
                $result = UserTags::create(['user_id' => $userId, 'tag_id' => $tag]);
            }
            DB::commit();
            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '添加成功');

        } catch (Exception $e) {
            DB::rollBack();
            return Utility::response_format(Utility::RESPONSE_CODE_DB_ERROR, '', $e->getMessage());
        }



    }

    public function getUserFocusQuestion() {

    }

    public function addFocusQuestion() {

    }

    public function deleteFocusQuestion() {

    }

}