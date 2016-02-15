<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午4:01
 */

namespace App\Http\Controllers;

use App\Model\Tags;
use Illuminate\Http\Request;
use App\Functions\Utility;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    public function getTags() {

        $tags = Tags::all()->toArray();

        if ($tags != null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $tags, '请求成功');
        }
    }

    public function uploadMedia() {

    }
}