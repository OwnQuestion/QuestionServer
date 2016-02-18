<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});



/*
|--------------------------------------------------------------------------
| 登录
|--------------------------------------------------------------------------
|
|@param username     用户名 (邮箱)
|@param password     密码
|
*/
Route::post('/login', 'Auth\AuthController@login');

/*
|--------------------------------------------------------------------------
| 注册
|--------------------------------------------------------------------------
|
|@param username        用户名 (邮箱)
|@param password        密码
|@param nickname        昵称
|@param age             年龄
|@param information     个人信息
|@param location        所在地
|@param headPicture     头像
|@param homePicture     主页图片
|
*/
Route::post('/register', 'Auth\AuthController@register');

/*
|--------------------------------------------------------------------------
| 获取关注列表
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param userId          目标用户id
|
*/
Route::post('/getUserFollows', 'UserController@getUserFollows');

/*
|--------------------------------------------------------------------------
| 获取粉丝列表
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param userId          目标用户id
|
*/
Route::post('/getUserFans', 'UserController@getUserFans');

/*
|--------------------------------------------------------------------------
| 关注用户
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param userId          目标用户id
|
*/
Route::post('/addFollowUser', 'UserController@addFollowUser');

/*
|--------------------------------------------------------------------------
| 取消关注用户
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param userId          目标用户id
|
*/
Route::post('/cancelFollowUser', 'UserController@deleteFollowUser');

/*
|--------------------------------------------------------------------------
| 获取用户信息
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param userId          目标用户id
|
*/
Route::post('/getUserInfo', 'UserController@getUserInfo');

/*
|--------------------------------------------------------------------------
| 设置用户标签
|--------------------------------------------------------------------------
|
| 替换操作
|
|@param token           身份验证标识
|@param tags            tagId逗号分隔 字符串
|
*/
Route::post('/setUserTags', 'UserController@addUserTags');

/*
|--------------------------------------------------------------------------
| 提问
|--------------------------------------------------------------------------
|
|
|
|@param token           身份验证标识
|@param title           标题
|@param content         内容
|@param picids          图片id 逗号分隔
|@param tagids         tagId 逗号分隔
|
*/
Route::post('/publishQuestion', 'QuestionController@publishQuestion');

/*
|--------------------------------------------------------------------------
| 回答问题
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param content         回答内容
|@param questionId      问题id
|
*/
Route::post('/publishAnswer', 'QuestionController@publishAnswer');

/*
|--------------------------------------------------------------------------
| 评论回答
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param content         评论内容
|@param answerId      问题id
|
*/
Route::post('/publishComment', 'QuestionController@publishComment');

/*
|--------------------------------------------------------------------------
| 赞 回答
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param answerId        问题id
|
*/
Route::post('/upAnswer', 'QuestionController@upAnswer');

/*
|--------------------------------------------------------------------------
| 解决问题
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param answerId        问题id
|
*/
Route::post('/resolveQuestion', 'QuestionController@resolveQuestion');

/*
|--------------------------------------------------------------------------
| 问题列表
|--------------------------------------------------------------------------
| 当userId不为空时 筛选指定用户的问题, 当userId为空时,取token用户中关注用户的问题列表
|
|@param token           身份验证标识
|@param userId          指定某个用户提出的问题 如果为空 显示token用户关注人提出的问题
|@param page
|@param size
|@param tagId
|
*/
Route::post('/getQuestions', 'QuestionController@getQuestions');

/*
|--------------------------------------------------------------------------
| 回答列表
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param quesitonId
|@param page
|@param size
|
*/
Route::post('/getAnswers', 'QuestionController@getAnswers');

/*
|--------------------------------------------------------------------------
| 评论列表
|--------------------------------------------------------------------------
|
|@param token           身份验证标识
|@param answerId
|@param page
|@param size
|
*/
Route::post('/getComments', 'QuestionController@getComments');

/*
|--------------------------------------------------------------------------
| 获取标签
|--------------------------------------------------------------------------
|
*/
Route::post('/getTags', 'CommonController@getTags');

/*
|--------------------------------------------------------------------------
| 上传文件
|--------------------------------------------------------------------------
|
|@param file           文件
|@param fileName       文件名
|
*/
Route::post('/uploadFile', 'CommonController@uploadFile');

Route::post('/user', [
    'middleware' => 'auth',
    'uses' => 'Test@test'
]);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

//Route::group(['middleware' => ['web']], function () {
//    //
////    Route::post('/user', 'Test@test');
//
//});
