<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午7:34
 */

namespace App\Http\Controllers;

use App\Functions\Utility;
use App\Http\Controllers\Auth\AuthController;
use App\Model\Answer;
use App\Model\AnswerUp;
use App\Model\Comment;
use App\Model\Question;
use App\Model\QuestionPictures;
use App\Model\QuestionTags;
use App\Model\UserAccount;
use App\Model\UserFollows;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;

class QuestionController extends Controller
{
    public function publishQuestion() {

        $request =  Request::capture();
        $token =    $request->input('token');
        $title =    $request->input('title');
        $content =  $request->input('content');
        $pictures = $request->input('picids');
        $tags =     $request->input('tagids');

        if ($title == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '标题不能为空');
        }

        if ($content == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '内容不能为空');
        }

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        DB::beginTransaction();
        try {

            $question = Question::create(['title' => $title,
                'content' => $content,
                'user_id' => $userId,
                'publish_time' => time()]);

            if ($pictures != null)
            {
                $picArray = explode(',', $pictures);
                foreach ($picArray as $pic)
                {
                    QuestionPictures::create(['question_id' => $question->id, 'pic_id' => $pic]);
                }
            }

            if ($tags != null) {
                $tagArray = explode(',', $tags);
                foreach ($tagArray as $tag) {
                    QuestionTags::create(['question_id' => $question->id, 'tag_id' => $tag]);
                }
            }

            DB::commit();
            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '发布成功');

        } catch (Exception $e) {
            DB::rollBack();
            return Utility::response_format(Utility::RESPONSE_CODE_DB_ERROR, '', $e->getMessage());
        }
    }

    public function publishAnswer() {

        $request =  Request::capture();
        $token =    $request->input('token');
        $content =    $request->input('content');
        $questionId =  $request->input('questionId');

        if ($content == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '内容不能为空');
        }

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        try {

            $answer = Answer::create(['answer_content' => $content,
                'question_id' => $questionId,
                'user_id' => $userId,
                'answer_time' => time()]);

            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '发布成功');

        } catch (Exception $e) {
            return Utility::response_format(Utility::RESPONSE_CODE_DB_ERROR, '', $e->getMessage());
        }


    }

    public function publishComment() {

        $request =  Request::capture();
        $token =    $request->input('token');
        $content =    $request->input('content');
        $answerId =  $request->input('answerId');

        if ($content == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '内容不能为空');
        }

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        try {

            $comment = Comment::create(['comment_content' => $content,
                'answer_id' => $answerId,
                'user_id' => $userId,
                'comment_time' => time()]);

            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '发布成功');

        } catch (Exception $e) {
            return Utility::response_format(Utility::RESPONSE_CODE_DB_ERROR, '', $e->getMessage());
        }
    }

    public function upAnswer() {

        $request =  Request::capture();
        $token =    $request->input('token');
        $answerId = $request->input('answerId');

        if ($answerId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', 'answerId不能为空');
        }

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        $answerUp = AnswerUp::where('user_id', $userId)->first();
        if ($answerUp != null) {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '不能重复提交');
        }

        $answerUp = AnswerUp::create(['answer_id' => $answerId, 'user_id' => $userId]);
        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '请求成功');
    }

    public function resolveQuestion() {

        $request =  Request::capture();
        $token =    $request->input('token');
        $answerId = $request->input('answerId');

        if ($answerId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', 'answerId不能为空');
        }

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        $answer = Answer::where('id', $answerId)->first();
        $question = Question::where('id', $answer->question_id)->first();

        if ($question->user_id != $userId)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '不是问题所属用户');
        }

        $isResolved = $answer->is_resolved;
        if ($isResolved)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '问题已解决');
        }

        $answer->is_resolved = 1;
        $answer->save();

        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '请求成功');;

    }

    public function getQuestions() {
        $request = Request::capture();
        $token = $request->input('token');
        $targetUserId = $request->input('userId'); // 指定某个用户提出的问题 如果为空 显示token用户关注人提出的问题
//        $page = $request->input('page');
        $size = $request->input('size');
        $tagId = $request->input('tagId');

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        if ($size == null || $size > 50) {
            $size = 20;
        }

        $questions = null;
        if ($targetUserId != null)
        {
            $questions = Question::where('user_id', $targetUserId);
        }
        else
        {
            $followUser = UserFollows::where('user_id', $userId)->lists('follow_user_id')->toArray();
            array_push($followUser, $userId);
            $questions = Question::whereIn('user_id', $followUser);
        }

        if ($tagId != null)
        {
            $questionsTag = QuestionTags::where('tag_id', $tagId)->lists('question_id');
            $questions = $questions->whereIn('id', $questionsTag);
        }

        $result = $questions->paginate($size)->toArray();

        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $result['data'], '请求成功');
    }

    public function getAnswers() {

        $request =  Request::capture();
        $token =    $request->input('token');
        $questionId = $request->input('questionId');
        $targetUserId = $request->input('userId');
        $size = $request->input('size');

        if ($questionId == null && $targetUserId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '问题id或用户id为空');
        }

        if ($size == null || $size > 50) {
            $size = 20;
        }

        $result = Answer::select('*');

        if ($questionId != null)
        {
            $result = $result->where('question_id', $questionId);
        }
        if ($targetUserId != null)
        {
            $result = $result->where('user_id', $targetUserId);
        }
        $result = $result->paginate($size)->toArray();

        $answers = $result['data'];
        $array = array();
        foreach ($answers as $answer) {

            $commentCount = Comment::where('answer_id', $answer['id'])->count();
            $answer['comments_count'] = $commentCount;
            array_push($array, $answer);
        }

        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $array, '请求成功');
    }

    public function getComments() {

        $request =  Request::capture();
        $token =    $request->input('token');
        $answerId = $request->input('answerId');
        $targetUserId = $request->input('userId');
        $size = $request->input('size');

        if ($answerId == null && $targetUserId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '问题id或用户id为空');
        }

        if ($size == null || $size > 50) {
            $size = 20;
        }

        $result = Comment::select('*');

        if ($answerId != null)
        {
            $result = $result->where('answer_id', $answerId);
        }
        if ($targetUserId != null)
        {
            $result = $result->where('user_id', $targetUserId);
        }
        $result = $result->paginate($size)->toArray();

        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $result['data'], '请求成功');

    }
}