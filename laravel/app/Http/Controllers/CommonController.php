<?php
/**
 * Created by PhpStorm.
 * User: caijingpeng
 * Date: 15/12/30
 * Time: 下午4:01
 */

namespace App\Http\Controllers;

use App\Model\Picture;
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
        $width = $request->input('width');
        $height = $request->input('height');


        if ($file->isValid())
        {
            $tmpName = $file->getFilename();
            $realPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            $fileType = $file->getMimeType();
        }
        $picURL = 'urla-------';
        /*
        $client = S3Client::factory(array(
            'region'      => 'us-west-2',
            'version'     => 'latest',
            'credentials' => [
                'key'    => 'AKIAICY5UKOXG57U6HGQ',
                'secret' => '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',
            ],
        ));

        $s3key = 'tempKey';
        $result = $client->putObject(array(
            'ACL'        => 'public-read',
            'Bucket'     => 'questionbucket',
            'Key'        => $s3key,
            'SourceFile' => $realPath
        ));

        $picURL = $result['ObjectURL'];
        */
        try {

            $picture = Picture::create(['original_pic' => $picURL,
                'bmiddle_pic' => $picURL,
                'thumbnail_pic' => $picURL,
                'pictureName' => $fileName,
                'width' => $width,
                'height' => $height]);

            return Utility::response_format(Utility::RESPONSE_CODE_SUCCESS, $picture->getAttributes(), '上传成功');

        } catch (Exception $e) {
            return Utility::response_format(Utility::RESPONSE_CODE_DB_ERROR, '', $e->getMessage());
        }


    }
}