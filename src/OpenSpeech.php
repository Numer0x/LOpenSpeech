<?php

namespace Zacz\LOpenSpeech;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;


class OpenSpeech
{

    protected mixed $config;

    protected $errorCode = [
        'HuoShan' => [
            '3001' => '无效的请求',
            '3003' => '并发超限',
            '3005' => '后端服务忙',
            '3006' => '服务中断',
            '3010' => '文本长度超限',
            '3011' => '无效文本',
            '3030' => '处理超时',
            '3031' => '处理错误',
            '3032' => '等待获取音频超时',
            '3040' => '音色克隆链路网络异常',
            '3050' => '音色克隆音色查询失败',
        ]
    ];


    public function __construct(Repository $config)
    {
        $this->config = $config->get('OpenSpeech');

    }

    public function OpenSpeech(
        $service,
        $url,
        $uid,
        $voice_type,
        $reqId,
        $text,
        $emotion,
        $language,
        $options = [],

    )
    {
        $defaultOptions = [
            'operation' => 'query',
            'rate' => 24000,
            'encoding' => "mp3",
            'compressionRate' => 1,
            'speedRatio' => 1,
            'volumeRatio' => 1,
            'pitchRatio' => 1,
            'text_type' => "plain",
            'silenceDuration' => "125",
            'withFrontend' => 1,
            'frontendType' => "unitTson",
            'splitSentence' => 0,
            'pureEnglishOpt' => 1
        ];
        $options = array_merge($defaultOptions, $options);
        $client = new \GuzzleHttp\Client(['timeout' => 600.0]);
        $headers = [
            'Content-Type' => 'application/json',
            "Authorization" => "Bearer;" . $this->config[$service]['AccessToken'],
        ];
        $json = [
            "app" => [
                "appid" => $this->config[$service]['APPID'],
                "token" => $this->config[$service]['AccessToken'],
                "cluster" => $this->config[$service]['ClusterID'],
            ],
            "user" => [
                "uid" => $uid,
            ],
            "audio" => [
                "voice_type" => $voice_type,
                "rate" => $options['rate'],
                "encoding" => $options['encoding'],
                "compression_rate" => $options['compressionRate'],
                "speed_ratio" => $options['speedRatio'],
                "volume_ratio" => $options['volumeRatio'],
                "pitch_ratio" => $options['pitchRatio'],
                "emotion" => $emotion,
                "language" => $language,
            ],
            "request" => [
                "reqid" => $reqId,
                "text" => $text,
                "text_type" => $options['text_type'],
                "silence_duration" => $options['silenceDuration'],
                "operation" => $options['operation'],
                "with_frontend" => $options['withFrontend'],
                "frontend_type" => $options['frontendType'],
                "split_sentence" => $options['splitSentence'],
                "pure_english_opt" => $options['pureEnglishOpt'],
            ]
        ];


        try {
            $response = $client->request(
                'POST',
                $this->config[$service][$url],
                [
                    'headers' => $headers,
                    'json' => $json
                ]);
            $body = $response->getBody();
            $stringBody = (string)$body;
            return ['res' => json_decode($stringBody, true), 'code' => 200, 'message' => 'Success'];
        } catch (\Exception $e) {
            $res = json_decode(explode('response:', $e->getMessage())[1], true);
            return ['res' => $res, 'code' => $e->getCode(), 'message' => $this->errorCode['HuoShan'][$res['code']] ?? '请求失败'];
        }


    }


    public function saveFile($fileContent, $fileName)
    {

        if (!file_exists(public_path('/audio'))) {
            mkdir(public_path('/audio'), 0777, true);
        }

        $myfile = fopen(public_path('/audio/' . $fileName), "w");
        $txt = base64_decode($fileContent);
        fwrite($myfile, $txt);
        fclose($myfile);
        return public_path('/audio/' . $fileName);

    }


}
