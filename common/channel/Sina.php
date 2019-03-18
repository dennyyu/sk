<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2018/10/14
 * Time: 下午11:03
 */

namespace common\channel;

use linslin\yii2\curl\Curl;

class Sina
{

    public static $lhb_type = [
        "01" => "涨幅偏离值达7%的证券",
        "02" => "跌幅偏离值达7%的证券",
        "03" => "振幅值达15%的证券",
        "04" => "换手率达20%的证券",
        "05" => "连续三个交易日内，涨幅偏离值累计达20%的证券",
        "06" => "连续三个交易日内，跌幅偏离值累计达20%的证券 ",
        "28" => "单只标的证券的当日融资买入数量达到当日该证券总交易量的50%以上",
        "11" => "无价格涨跌幅限制的证券",
    ];

    //http://money.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=1000&sort=changepercent&asc=0&node=hs_a&symbol=&_s_r_a=page

    const DAILY_URL = "http://money.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=%s&num=%s&node=hs_a&_s_r_a=page&sort=symbol";

    const LHB_DETAIL_URL = "http://vip.stock.finance.sina.com.cn/q/api/jsonp.php/var%20details=/InvestConsultService.getLHBComBSData?";

    public static function getDailyList()
    {
        $length = 100;
        $result = [];
        for ($index = 0; $index < 35; $index++) {
            $data = self::get($index, $length);

            if (!empty($data)) {
                foreach ($data as $item) {
                    if ($item['volume'] < 0.5) {
                        continue;
                    }
                    $result[$item['stock_code']] = $item;
                }
            }
        }

        return array_values($result);
    }

    private static function get($page, $length)
    {


        $url = sprintf(self::DAILY_URL, $page, $length);
        echo $url . PHP_EOL;

        $curl = new Curl();
        $curl->setOption(CURLOPT_TIMEOUT, 120);
        $orgRes = $curl->get($url);

        if (empty($orgRes)) {
            return null;
        }

        $response = iconv('gbk', 'utf8', $orgRes);
        if (empty($response)) {
            return [];
        }
        $response = str_replace("open", '"open"', $response);
        $response = str_replace("symbol", '"symbol"', $response);
        $response = str_replace("code", '"stock_code"', $response);
        $response = str_replace("name", '"stock_name"', $response);
        $response = str_replace("trade", '"close"', $response);
        $response = str_replace("pricechange", '"chg"', $response);
        $response = str_replace("changepercent", '"pch"', $response);

        $response = str_replace("high", '"high"', $response);
        $response = str_replace("low", '"low"', $response);
        $response = str_replace("buy", '"buy"', $response);
        $response = str_replace("sell", '"sell"', $response);
        $response = str_replace("settlement", '"settlement"', $response);
        $response = str_replace("volume", '"volume"', $response);
        $response = str_replace("amount", '"amount"', $response);
        $response = str_replace("ticktime", '"ticktime"', $response);
        $response = str_replace("per", '"per"', $response);
        $response = str_replace("pb", '"pb"', $response);
        $response = str_replace("mktcap", '"mktcap"', $response);
        $response = str_replace("turnoverratio", '"turnrate"', $response);
        $response = str_replace("nmc", '"nmc"', $response);

        $response = str_replace("pch", 'percent', $response);

        $data = json_decode($response, true);

        return $data;

    }

    /** 获取每股龙虎榜详情
     *
     * @param $stock
     *
     * @return array|mixed
     */
    public static function getLhbDetail($stock)
    {
        $url = self::LHB_DETAIL_URL . sprintf("symbol=%s&tradedate=%s&type=%s", $stock["stock_code"], $stock["trade_date"], $stock["type"]);

        $curl   = new Curl();
        $orgRes = $curl->get($url);
        if (empty($orgRes)) {
            return [];
        }
        try {

            $response = iconv('gbk', 'utf8', $orgRes);
            if (empty($response)) {
                return [];
            }
        } catch (\Exception $exception) {

            echo $orgRes . PHP_EOL;;
            echo $exception->getMessage() . PHP_EOL;

            return [];
        }

        $response = str_replace("//<script>location.href='http://sina.com.cn'; </script>", "", $response);
        $response = str_replace("var details=((", "", $response);
        $response = str_replace("))", "", $response);

        $response = str_replace("SYMBOL", '"stock_code"', $response);
        $response = str_replace("type", '"category"', $response);
        $response = str_replace("comCode", '"comCode"', $response);
        $response = str_replace("comName", '"comName"', $response);
        $response = str_replace("buyAmount", '"bAmount"', $response);
        $response = str_replace("sellAmount", '"sAmount"', $response);
        $response = str_replace("netAmount", '"netAmount"', $response);
        $response = str_replace("buy", '"buy"', $response);
        $response = str_replace("sell", '"sell"', $response);
        $response = str_replace("bAmount", 'buyAmount', $response);
        $response = str_replace("sAmount", 'sellAmount', $response);

        $lhb_data = json_decode($response, true);

        return $lhb_data;

    }

}