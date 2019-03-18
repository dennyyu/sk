<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2019/2/16
 * Time: 下午11:43
 */

namespace common\channel;

use linslin\yii2\curl\Curl;
use Yii;

class Eastmoney
{

    public static $lhb_type = [
        "日涨幅偏离值达到7%的前五只证券"                 => "01",
        "有价格涨跌幅限制的日收盘价格涨幅偏离值达到7%的前三只证券"    => "01",
        "日跌幅偏离值达到7%的前五只证券"                 => "02",
        "有价格涨跌幅限制的日收盘价格跌幅偏离值达到7%的前三只证券"    => "02",
        "日振幅值达到15%的前五只证券"                  => "03",
        "有价格涨跌幅限制的日价格振幅达到15%的前三只证券"        => "03",
        "日换手率达到20%的前五只证券"                  => "04",
        "有价格涨跌幅限制的日换手率达到20%的前三只证券"         => "04",
        "连续三个交易日内，涨幅偏离值累计达到20%的证券"         => "05",
        "连续三个交易日内，跌幅偏离值累计达到20%的证券"         => "06",
        "单只标的证券的当日融资买入数量达到当日该证券总交易量的50％以上" => "28",
        "无价格涨跌幅限制的证券"                      => "11",
    ];

    public static function tradeDetail($date)
    {
        $url = "http://data.eastmoney.com/DataCenter_V3/stock2016/TradeDetail/pagesize=200,page=1,sortRule=-1,sortType=,startDate=$date,endDate=$date,gpfw=0,js=var%20data_tab_1.html?rt=25838770";

        $curl = new Curl();
        $curl->setHeaders(["charset=UTF-8"]);
        $response = $curl->get($url);

        $response = iconv("gbk", "utf-8", $response);

        $response = str_replace("var data_tab_1=", "", $response);
        $response = str_replace("\$SCode", "SCode", $response);
        $response = str_replace("\$SName", "SName", $response);

        $response_obj = json_decode($response);

        if (empty($response_obj->data)) {
            return [];
        }
        $result   = [];
        $lhb_type = Eastmoney::$lhb_type;
        $new_type = [];

        foreach ($response_obj->data as $item) {
            if (!isset($lhb_type[$item->Ctypedes])) {

                $new_type [$item->Ctypedes] = $item->Ctypedes;
                continue;
            }
            $result[] = [
                'stock_code' => $item->SCode,
                'stock_name' => $item->SName,
                'trade_date'      => $date,
                'type'       => $lhb_type[$item->Ctypedes],
            ];
        }


        return $result;
    }

}