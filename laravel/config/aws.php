<?php

use Aws\Laravel\AwsServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. The minimum
    | required options are declared here, but the full set of possible options
    | are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */

    'credentials' => [
        'key'       => 'AKIAICY5UKOXG57U6HGQ',
        'secret'    => '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',
    ],
    'region' => env('AWS_REGION', 'us-west-2'),
    'version' => 'latest',
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ],
//    'Ses' => [
//        'region' => 'us-east-1',
//    ],
];
