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

    protected $timbre = [
        'normalChinese' => [
            '灿灿 2.0' => 'BV700_V2_streaming',
            '炀炀' => 'BV705_streaming',
            '擎苍 2.0' => 'BV701_V2_streaming',
            '通用女声 2.0' => 'BV001_V2_streaming',
            '灿灿' => 'BV700_streaming',
            '超自然音色-梓梓2.0' => 'BV406_V2_streaming',
            '超自然音色-梓梓' => 'BV406_streaming',
            '超自然音色-燃燃2.0' => 'BV407_V2_streaming',
            '超自然音色-燃燃' => 'BV407_streaming',
            '通用女声' => 'BV001_streaming',
            '通用男声' => 'BV002_streaming',
            '知性姐姐-双语EDU' => 'BV034_streaming',
            '温柔小哥EDU' => 'BV033_streaming',
        ],
        'normalAmericanEnglish' => [
            '美式女声-Amelia' => 'BV027_streaming',
            '讲述女声-Amanda' => 'BV502_streaming',
            '活力女声-Ariana	' => 'BV503_streaming',
            '活力男声-Jackson' => 'BV504_streaming',
            '天才少女' => 'BV421_streaming',
            'Stefan' => 'BV702_streaming',
            '天真萌娃-Lily' => 'BV506_streaming',

        ],
        'normalBritishEnglish' => [
            '亲切女声-Anna' => 'BV040_streaming',
        ],
        'normalAustralianEnglish' => [
            '澳洲男声-Henry' => 'BV516_streaming',
        ],
        'normalJapanese' => [
            '元气少女' => 'BV520_streaming',
            '萌系少女' => 'BV521_streaming',
            '天才少女' => 'BV421_streaming',
            '气质女声' => 'BV522_streaming',
            'Stefan' => 'BV702_streaming',
            '灿灿' => 'BV700_streaming',
            '日语男声' => 'BV524_streaming',
        ],
        'normalPortuguese' => [
            '活力男声Carlos（巴西地区）' => 'BV531_streaming',
            "活力女声（巴西地区）" => "BV530_streaming",
            "天才少女" => "BV421_streaming",
            "Stefan" => "BV702_streaming",
            "灿灿" => "BV700_streaming"
        ],
        'normalSpanish' => [
            "气质御姐（墨西哥地区）" => "BV065_streaming",
            "天才少女" => "BV421_streaming",
            "Stefan" => "BV702_streaming",
            "灿灿" => "BV700_streaming"
        ],
        'normalThai' => [
            '天才少女' => 'BV421_streaming'
        ],
        'normalVietnamese' => [
            '天才少女' => 'BV421_streaming',
        ],
        'normalIndonesian' => [
            "天才少女" => "BV421_streaming",
            "Stefan" => "BV702_streaming",
            "灿灿" => "BV700_streaming"
        ],
        'modelChinese' => [
            "爽快思思/Skye" => "zh_female_shuangkuaisisi_moon_bigtts",
            "温暖阿虎/Alvin" => "zh_male_wennuanahu_moon_bigtts",
            "少年梓辛/Brayan" => "zh_male_shaonianzixin_moon_bigtts",
            "邻家女孩" => "zh_female_linjianvhai_moon_bigtts",
            "渊博小叔" => "zh_male_yuanboxiaoshu_moon_bigtts",
            "阳光青年" => "zh_male_yangguangqingnian_moon_bigtts"
        ],
        'modelEnglish' => [
            "爽快思思/Skye" => "zh_female_shuangkuaisisi_moon_bigtts",
            "温暖阿虎/Alvin" => "zh_male_wennuanahu_moon_bigtts",
            "少年梓辛/Brayan" => "zh_male_shaonianzixin_moon_bigtts",
        ],
        'modelJapanese' => [
            "かずね（和音）/Javier or Álvaro" => "multi_male_jingqiangkanye_moon_bigtts",
            "はるこ（晴子）/Esmeralda" => "multi_female_shuangkuaisisi_moon_bigtts",
            "あけみ（朱美）" => "multi_female_gaolengyujie_moon_bigtts",
            "ひろし（広志）/Roberto" => "multi_male_wanqudashu_moon_bigtts"
        ],
        'modelSpanish' => [
            "かずね（和音）/Javier or Álvaro" => "multi_male_jingqiangkanye_moon_bigtts",
            "はるこ（晴子）/Esmeralda" => "multi_female_shuangkuaisisi_moon_bigtts",
            "ひろし（広志）/Roberto" => "multi_male_wanqudashu_moon_bigtts"
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

    public function returnTimbreList()
    {
        return ['res' => $this->timbre, 'code' => 200, 'message' => 'Success'];
    }


}
