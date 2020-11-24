<?php

namespace Leavebn\Duanship;

/**
 * 用于短视频去水印的库
 * @version 1.0 109.11.24
 * @author leavebn
 */
class DuanAPI 
{
    private $secretId;

    private $secretKey;

    private $preParse;

    const API = "https://duanship.wangtui88.com/release/parse";

    public function __construct($config)
    {
        $this->secretId = isset($config['secretId']) ? $config['secretId'] : null;
        $this->secretKey = isset($config['secretKey']) ? $config['secretKey'] : null;
        $this->preParse = isset($config['preParse']) ? $config['preParse'] : 0;// 是否需要更多信息
        if (!$this->secretId || !$this->secretKey) {
            throw new \Exception("密钥不完整");
        }
        return $this;
    }

    /**
     * 创建用于认证的Header.Authorization
     * @return [type] [description]
     */
    private function authorization() : string
    {
        $dateTime = $this->getDateTime();
        $srcStr = "date: ".$dateTime."\n"."source: "."parseApi";
        $Authen = 'hmac id="'.$this->secretId.'", algorithm="hmac-sha1", headers="date source", signature="';
        $signStr = base64_encode(hash_hmac('sha1', $srcStr, $this->secretKey, true));
        $Authen = $Authen.$signStr."\"";
        return $Authen;
    }

    private function getDateTime()
    {
        return gmdate("D, d M Y H:i:s T");
    }

    /**
     * 发起一个Post请求
     * @param  [type] $url     [description]
     * @param  [type] $params  [description]
     * @param  [type] $headers [description]
     * @return [type]          [description]
     */
    private function requestPost($url, $params, $headers)
    {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch, CURLOPT_POST, 1);

        $data = curl_exec($ch); 
        if (curl_errno($ch)) { 
            throw new \Exception(curl_error($ch));
        } else { 
            curl_close($ch); 
            return json_decode($data, true);
        } 
    }

    private function getHeaders() : array
    {
        $headers = [
            'Source: parseApi',
            'Date: '.$this->getDateTime(),
            'Authorization: '.$this->authorization(),
        ];
        return $headers;
    }

    /**
     * 解析一个短视频
     * @param  string $url [description]
     * @return [type]      [description]
     */
    public function parse(string $url)
    {
        $headers = $this->getHeaders();
        $resp = $this->requestPost(self::API, [
            'url'      => $url,
            'preParse' => $this->preParse
        ], $headers);
        // 可能是请求密钥出错
        if (isset($resp["message"])) {
            return [
                'code' => 150,
                'msg' => $resp["message"]
            ];
        }
        return $resp;
    }
}