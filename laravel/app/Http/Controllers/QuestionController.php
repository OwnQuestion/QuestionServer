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
use App\Model\AnswerPictures;
use App\Model\AnswerUp;
use App\Model\Comment;
use App\Model\Picture;
use App\Model\Question;
use App\Model\QuestionPictures;
use App\Model\QuestionTags;
use App\Model\Tags;
use App\Model\UserAccount;
use App\Model\UserFollows;
use App\Model\UserInfo;
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
        $brief = $request->input('brief');
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
                'brief' => $brief,
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
        $brief = $request->input('brief');
        $pictures = $request->input('picids');

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

            $answer = Answer::create(['answer_content' => $content,
                'answer_brief' => $brief,
                'user_id' => $userId,
                'question_id' => $questionId,
                'answer_time' => time()]);

            if ($pictures != null)
            {
                $picArray = explode(',', $pictures);
                foreach ($picArray as $pic)
                {
                    AnswerPictures::create(['answer_id' => $answer->id, 'pic_id' => $pic]);
                }
            }

            DB::commit();
            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, '', '发布成功');

        } catch (Exception $e) {
            DB::rollBack();
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
        $page = $request->input('page');
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
            $questions = Question::select('id', 'title', 'brief', 'publish_time', 'user_id')
                ->where('user_id', $targetUserId)
                ->orderBy('publish_time', 'desc');
        }
        else
        {
            $followUser = UserFollows::where('user_id', $userId)->lists('follow_user_id')->toArray();
            array_push($followUser, $userId);
            $questions = Question::select('id', 'title', 'brief', 'publish_time', 'user_id')
                ->whereIn('user_id', $followUser)
                ->orderBy('publish_time', 'desc');
        }

        if ($tagId != null)
        {
            $questionsTag = QuestionTags::where('tag_id', $tagId)->lists('question_id');
            $questions = $questions
                ->whereIn('id', $questionsTag);
        }

        $questions_query = $questions->paginate($size, '*', 'page', $page)->toArray();
        $questions_result = $questions_query['data'];

        $result = array();
        foreach ($questions_result as $question)
        {
            // 个人信息
            $userInfo = UserInfo::select('user_name', 'head_pic')
                ->where('user_id', $question['user_id'])
                ->first()
                ->toArray();

            $question = array_merge($question, $userInfo);


            // 图片
            $picIds = QuestionPictures::select('pic_id')
                ->where('question_id', $question['id'])
                ->get()
                ->toArray();

            $pics = Picture::whereIn('id', $picIds)
                ->get()
                ->toArray();

            $question = array_merge($question, ['image' => $pics]);
//            print_r($question);

            // 标签
            $tagIds = QuestionTags::select('tag_id')
                ->where('question_id', $question['id'])
                ->get()
                ->toArray();
            $tags = Tags::whereIn('id', $tagIds)
                ->get()
                ->toArray();
            $question = array_merge($question, ['tags' => $tags]);

            array_push($result, $question);
        }

//        print_r($questions_result);

        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $result, '请求成功');
    }

    public function getAnswers() {

        $request =  Request::capture();
        $token =    $request->input('token');
        $questionId = $request->input('questionId');
        $targetUserId = $request->input('userId');
        $size = $request->input('size');
        $page = $request->input('page');

        if ($questionId == null && $targetUserId == null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', '问题id或用户id为空');
        }

        if ($size == null || $size > 50) {
            $size = 20;
        }


        $result = Answer::select('id', 'answer_brief', 'answer_time', 'question_id', 'user_id', 'is_resolved');

        if ($questionId != null)
        {
            $result = $result->where('question_id', $questionId);
        }
        if ($targetUserId != null)
        {
            $result = $result->where('user_id', $targetUserId);
        }
        $result = $result
            ->orderBy('answer_time', 'desc')
            ->paginate($size, '*', 'page', $page)
            ->toArray();

        $answers = $result['data'];
        $array = array();
        foreach ($answers as $answer) {

            // 个人信息
            $userInfo = UserInfo::select('user_name', 'head_pic')
                ->where('user_id', $answer['user_id'])
                ->first()
                ->toArray();

            $answer = array_merge($answer, $userInfo);

            // 评论数
            $commentCount = Comment::where('answer_id', $answer['id'])->count();
            $answer['commentNumber'] = $commentCount;

            // 赞数
            $upCount = AnswerUp::where('answer_id', $answer['id'])->count();
            $answer['upNumber'] = $upCount;

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

    public function getQuestionDetail() {
        $request = Request::capture();
        $token = $request->input('token');
        $questionId = $request->input('questionId');

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        if ($questionId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', 'questionId不能为空');
        }

        $question = Question::select('id', 'title', 'content', 'publish_time', 'user_id')
            ->where('id', $questionId)
            ->first()
            ->toArray();


//        print_r($question);
        // 个人信息
        $userInfo = UserInfo::select('user_name', 'head_pic')
            ->where('user_id', $question['user_id'])
            ->first()
            ->toArray();

        $question = array_merge($question, $userInfo);


        // 图片
        $picIds = QuestionPictures::select('pic_id')
            ->where('question_id', $question['id'])
            ->get()
            ->toArray();

        $pics = Picture::whereIn('id', $picIds)
            ->get()
            ->toArray();

        $question = array_merge($question, ['image' => $pics]);
//            print_r($question);

        // 标签
        $tagIds = QuestionTags::select('tag_id')
            ->where('question_id', $question['id'])
            ->get()
            ->toArray();
        $tags = Tags::whereIn('id', $tagIds)
            ->get()
            ->toArray();
        $question = array_merge($question, ['tags' => $tags]);

        // 回答 时间倒序 前三条

        $answers = Answer::select('*')
            ->where('question_id', $questionId)
            ->orderBy('answer_time', 'desc')
            ->paginate(3, '*', 'page', 1)
            ->toArray();

        $result = array();
        foreach ($answers['data'] as $answer) {
            // 个人信息
            $userInfo = UserInfo::select('user_name', 'head_pic')
                ->where('user_id', $answer['user_id'])
                ->first()
                ->toArray();

            $answer = array_merge($answer, $userInfo);

            // 回答的评论数

            $comments = Comment::select('*')
                ->where('answer_id', $answer['id'])
                ->get()
                ->toArray();
            $answer = array_merge($answer, ['commentNumber' => count($comments)]);

            array_push($result, $answer);
        }

        $question = array_merge($question, ['answers' => $result]);

        // 回答 最热门 三条
//        ....

        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $question, '请求成功');
    }

    public function getAnswerDetail() {

        $request = Request::capture();
        $token = $request->input('token');
        $answerId = $request->input('answerId');

        $userId = AuthController::getUserIdByToken($token);
        if ($userId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_AUTH_ERROR, '', '认证失败');
        }

        if ($answerId == null) {
            return Utility::response_format(Utility::RESPONSE_CODE_Error, '', 'answerId不能为空');
        }

        $answer = Answer::select('id', 'answer_content', 'answer_time', 'question_id', 'user_id', 'is_resolved')
            ->where('id', $answerId)
            ->first()
            ->toArray();


//        print_r($question);
        // 个人信息
        $userInfo = UserInfo::select('user_name', 'head_pic')
            ->where('user_id', $answer['user_id'])
            ->first()
            ->toArray();

        $answer = array_merge($answer, $userInfo);


        // 图片
        $picIds = AnswerPictures::select('pic_id')
            ->where('answer_id', $answer['id'])
            ->get()
            ->toArray();

        $pics = Picture::whereIn('id', $picIds)
            ->get()
            ->toArray();

        $answer = array_merge($answer, ['image' => $pics]);

        // 评论数
        $comments = Comment::where('answer_id', $answer['id'])
            ->count();
        $answer = array_merge($answer, ['commentNumber' => $comments]);

        // 赞数
        $upCount = AnswerUp::where('answer_id', $answer['id'])->count();
        $answer = array_merge($answer, ['upNumber' => $upCount]);

        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $answer, '请求成功');
    }

    public function getHotAnswer()
    {
        $request =  Request::capture();
        $token =    $request->input('token');
        $questionId = $request->input('questionId');

        $answersQuery = Answer::select('id', 'answer_brief', 'answer_time', 'question_id', 'user_id', 'is_resolved')
            ->where('question_id', $questionId);

        $answerIds = $answersQuery->lists('id')->toArray();

        $upAnswers = AnswerUp::select('answer_id', DB::raw('count(*) as up_number'))
            ->whereIn('answer_id', $answerIds)
            ->groupBy('answer_id')
            ->orderBy('up_number', 'desc')
            ->take(2);

//        print_r($upAnswers->lists('answer_id')->toArray());

        $answersQuery = Answer::select('id', 'answer_brief', 'answer_time', 'question_id', 'user_id', 'is_resolved')
            ->where('question_id', $questionId)
            ->whereIn('id', $upAnswers->lists('answer_id')->toArray());
        $answers = $answersQuery->get()->toArray();

        $upNumber = $upAnswers->get()->toArray();

        $result = array();
        foreach ($answers as $an)
        {
            foreach ($upNumber as $up)
            {
                if ($up['answer_id'] == $an['id'])
                {
                    $an = array_merge($an, ['upNumber' => $up['up_number']]);
                    array_push($result, $an);
                }
            }

        }

        return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $result, '请求成功');
    }
}