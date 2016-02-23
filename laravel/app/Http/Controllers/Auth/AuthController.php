<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use App\Model\UserInfo;
use App\Model\UserAccount;
use App\Functions\Utility;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    public function login() {

        $request = Request::capture();
        $username = $request->input('username');
        $password = $request->input('password');

        $user = UserAccount::select('id', 'username', 'token')
            ->where('username', $username)
            ->where('password', $password)
            ->first();

        if ($user == null)
        {

            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '账号密码错误');
        }
        else
        {
            $result = $user->UserInfo->toArray();
            $result['token'] = $user->token;
            $userTags = $user->userTags;
            $result['tags'] = $userTags;
            return Utility::response_format(1, $result, '登录成功');
        }



        /*
        $request = Request::capture();
        $username = $request->input('username');
        $password = $request->input('password');

        $file = $request->file('file');


        $clientName = $file->getClientOriginalName();

        $tmpName = $file->getFileName(); // 缓存在tmp文件夹中的文件名 例如 php8933.tmp 这种类型的.

        $realPath = $file->getRealPath();

        $path = '/Applications/MAMP/htdocs/laravel/storage/app';
        $file->move($path, 'newname');
        */

//        echo $realPath.'  '.$tmpName;
//        Storage::move($file, 'file');
//        Storage::copy($file, 'file1.png');
//        Storage::disk('local')->put('file', $file);

//
//        var_dump($user);

//        $question = Question::all();
//        echo $question;
//        return $user;

    }

    public function register() {

        $request =      Request::capture();
        $username =     $request->input('username');
        $password =     $request->input('password');
        $nickname =     $request->input('nickname');
        $age =          $request->input('age');
        $information =  $request->input('information');
        $location =     $request->input('location');
        $headPic =      $request->input('headPicture');
        $homePic =      $request->input('homePicture');

        if ($username == null || $password == null)
        {
            // 账号 密码 不能为空
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '账号密码不能为空');
        }
        else if (UserAccount::where('username', $username)->first() != null)
        {

            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '账号已存在');
        }
        else if (Utility::isValidateUsername($username) == false)
        {
            // 用户名无效
        }
        else if (Utility::isValidatePassword($password) == false)
        {
            // 密码无效
        }

        $token = Utility::genToken();

        DB::beginTransaction();
        try {
            $user = UserAccount::create(['username' => $username, 'password' => $password, 'token' => $token]);
            $userInfo = UserInfo::create(['user_name' => $nickname,
                'user_age' => $age,
                'user_information' => $information,
                'user_location' => $location,
                'user_id' => $user->id,
                'head_pic' => $headPic,
                'home_pic' => $homePic]);
            DB::commit();

            $userInfo['token'] = $user->token;
            $userInfo['tags'] = $user->userTags;

            return $userInfo;

        } catch (Exception $e) {
            DB::rollBack();
            return Utility::response_format(Utility::RESPONSE_CODE_DB_ERROR, '', $e->getMessage());
        }
    }


    static public function getUserIdByToken($token) {

        $user = UserAccount::select('id', 'username', 'token')
            ->where('token', $token)
            ->first();

        if ($user == null)
        {
            return null;
        }
        else
        {
            return $user->id;
        }

    }

}
