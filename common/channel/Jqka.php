<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2019/3/5
 * Time: 下午9:51
 */

namespace common\channel;

use linslin\yii2\curl\Curl;

class Jqka
{
    const CATEGORY_DETAIL_URL = "http://q.10jqka.com.cn/%s/detail/field/%s/order/desc/page/%s/ajax/1/code/%s";

    public static function categoryDetail($type, $filed, $categorCode)
    {
        $result = [];
        $page   = 1;
        while (true) {
            $data = self::gnDetail($type, $filed, $categorCode, $page);
            if (empty($data)) {
                break;
            }

            foreach ($data as $item) {
                $result[] = $item;
            }
        }

        return $result;

    }

    /**
     * @param $type
     * @param $filed
     * @param $categorCode
     * @param $page
     *
     * @return array|null
     */
    private static function gnDetail($type, $filed, $categorCode, $page)
    {

//
//        String url = "http://q.10jqka.com.cn/thshy/detail/field/199112/order/desc/page/1/ajax/1/code/881114";
//
//        HttpUriRequest httpUriRequest = new HttpGet(url);
//        httpUriRequest.setHeader(new BasicHeader("Cookie", "searchGuide=sg; spversion=20130314; Hm_lvt_78c58f01938e4d85eaf619eae71b4ed1=1550326623,1551101553,1551793036; user=MDpYSFVXVFVTUzo6Tm9uZTo1MDA6Mzk1NDgxMDQ6NywxMTExMTExMTExMSw0MDs0NCwxMSw0MDs2LDEsNDA7NSwxLDQwOzEsMSw0MDsyLDEsNDA7MywxLDQwOzUsMSw0MDs4LDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAxLDQwOjI1Ojo6Mjk0MzE5MTQ6MTU1MTc5MzAzNzo6OjExOTM4MzAwMjA6NjA0ODAwOjA6MTBmZWNhZTgyZDA0ZmFjNGM2MmQ5MmY1MDExZGNiNGFlOmRlZmF1bHRfMjow; userid=29431914; u_name=XHUWTUSS; escapename=XHUWTUSS; ticket=9d6d59fa1d5d809fa35bd01919a88897; __utma=156575163.1430177172.1550591325.1551449015.1551793071.5; __utmc=156575163; __utmz=156575163.1551793071.5.5.utmcsr=10jqka.com.cn|utmccn=(referral)|utmcmd=referral|utmcct=/; Hm_lpvt_78c58f01938e4d85eaf619eae71b4ed1=1551793328; historystock=000727%7C*%7C000029%7C*%7C300424%7C*%7C000576%7C*%7C002190; v=Ag5_eQsx3gbX1Woio63tMUAdWe_Tj9IK5FKHSThXeBzM46BZoB8imbTj1ukL"));
//
//        CloseableHttpClient httpClient = HttpClients.createDefault();
//        CloseableHttpResponse response = httpClient.execute(httpUriRequest);
//        HttpEntity responseEntity = response.getEntity();
//        String res = EntityUtils.toString(responseEntity);
//        System.out.println(res);



        $url = sprintf(self::CATEGORY_DETAIL_URL, $type, $filed, $page, $categorCode);
        echo $url . PHP_EOL;

        $curl = new Curl();
        $curl->setOption(CURLOPT_TIMEOUT, 120);
        $curl->setOption(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOption(CURLOPT_COOKIE,"searchGuide=sg; spversion=20130314; Hm_lvt_78c58f01938e4d85eaf619eae71b4ed1=1550326623,1551101553,1551793036; user=MDpYSFVXVFVTUzo6Tm9uZTo1MDA6Mzk1NDgxMDQ6NywxMTExMTExMTExMSw0MDs0NCwxMSw0MDs2LDEsNDA7NSwxLDQwOzEsMSw0MDsyLDEsNDA7MywxLDQwOzUsMSw0MDs4LDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAxLDQwOjI1Ojo6Mjk0MzE5MTQ6MTU1MTc5MzAzNzo6OjExOTM4MzAwMjA6NjA0ODAwOjA6MTBmZWNhZTgyZDA0ZmFjNGM2MmQ5MmY1MDExZGNiNGFlOmRlZmF1bHRfMjow; userid=29431914; u_name=XHUWTUSS; escapename=XHUWTUSS; ticket=9d6d59fa1d5d809fa35bd01919a88897; __utma=156575163.1430177172.1550591325.1551449015.1551793071.5; __utmc=156575163; __utmz=156575163.1551793071.5.5.utmcsr=10jqka.com.cn|utmccn=(referral)|utmcmd=referral|utmcct=/; Hm_lpvt_78c58f01938e4d85eaf619eae71b4ed1=1551793328; historystock=000727%7C*%7C000029%7C*%7C300424%7C*%7C000576%7C*%7C002190; v=Ag5_eQsx3gbX1Woio63tMUAdWe_Tj9IK5FKHSThXeBzM46BZoB8imbTj1ukL");

        $orgRes = $curl->get($url);
        echo $url . PHP_EOL;
        echo $orgRes . PHP_EOL;

        if (empty($orgRes)) {
            return null;
        }

        $response = iconv('gbk', 'utf8', $orgRes);
        if (empty($response)) {
            return [];
        }

        echo $response . PHP_EOL;

        preg_match_all("/cn\/(\\d{6})\//", $response, $stock, PREG_PATTERN_ORDER);

        var_dump($response);

        if (!empty($stock)) {
            return $stock[1];
        }

        return [];
    }

}