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
use Aws\S3\S3Client;

class CommonController extends Controller
{
    public function getTags() {

        $tags = Tags::all()->toArray();

        if ($tags != null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $tags, '请求成功');
        }
    }

    public function uploadFile() {

        $request = Request::capture();
        $file = $request->file('file');
        $fileName = $request->input('filename');

        if ($file->isValid())
        {
            $tmpName = $file->getFilename();
            $realPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            $fileType = $file->getMimeType();
        }

        $client = S3Client::factory(array(
            'region'      => 'us-west-2',
            'version'     => 'latest',
            'credentials' => [
                'key'    => 'AKIAICY5UKOXG57U6HGQ',
                'secret' => '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',
            ],
        ));

        $result = $client->putObject(array(
            'ACL'        => 'public-read',
            'Bucket'     => 'questionbucket',
            'Key'        => $fileName,
            'SourceFile' => $realPath
        ));

        $picURL = $result['ObjectURL'];

        if ($picURL != null)
        {
            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $picURL, '上传成功');
        }
    }
}