<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/24
 * Time: 下午9:44
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Test extends Controller {

    public function test() {

        $request = Request::capture();
        return $request->input('name');

    }
}