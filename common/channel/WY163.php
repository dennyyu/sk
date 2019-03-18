<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2018/10/14
 * Time: 下午4:40
 */

namespace common\channel;

use common\helpers\Dh;
use common\helpers\ExcelHelper;
use common\helpers\StockHelper;
use Exception;
use linslin\yii2\curl\Curl;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

class WY163
{

    const DAILY_URL = "http://quotes.money.163.com/service/chddata.html?code=%s&start=%s&end=%s";

    private static function toWyStockCode($stockCode)
    {
        if ($stockCode == '1A0001') {
            return "0000001";
        }
        if (StringHelper::startsWith($stockCode, "60")) {
            return "0" . $stockCode;
        }

        return "1" . $stockCode;
    }

    public static function getDailyList($stockCode, $startDate, $endDate)
    {
        //日期	股票代码	名称	收盘价	最高价	最低价	开盘价	前收盘	涨跌额	涨跌幅	换手率	成交量	成交金额	tcap	mcap	成交笔数

        $wy_stock_code = self::toWyStockCode($stockCode);
        $url           = sprintf(self::DAILY_URL, $wy_stock_code, Dh::format($startDate, 'Ymd'), Dh::format($endDate, 'Ymd'));

        echo $url . PHP_EOL;
        $curl = new Curl();
        $curl->setOption(CURLOPT_TIMEOUT, 120);
        $response = $curl->get($url);
        $response = iconv('gbk', 'utf8', $response);
        $columns  = [
            "trade_date",
            "stock_code",
            "stock_name",
            "close",
            "high",
            "low",
            "open",
            "y_close",
            "chg",
            "percent",
            "turnrate",
            "voturnover",
            "vaturnover",
            "tcap",
            "mcap",

        ];
        $data     = ExcelHelper::toArray($response, $columns, 1);

        for ($index = 0; $index < count($data) - 1; $index++) {
            if (abs($data[$index]['percent']) > 11
                && $data[$index + 1]['tcap'] > 100
                && $data[$index]['tcap'] > 100) {
                $percent                 = ($data[$index]['tcap'] - $data[$index + 1]['tcap']) / $data[$index + 1]['tcap'];
                $data[$index]['percent'] = round($percent * 100, 2);
            }
        }

        echo '数量：' . count($data) . PHP_EOL;

        $tempData = $data;
        $data     = [];
        foreach ($tempData as $item) {

            if (count($item) < 15) {
                continue;
            }

            if ($item['close'] < 0.5) {
                continue;
            }
            if ($item['open'] < 0.5) {
                continue;
            }

            if ($item['low'] < 0.5) {
                continue;
            }

            if ($item['high'] < 0.5) {
                continue;
            }

            $data[] = $item;
        }

        echo '过滤后数量：' . count($data) . PHP_EOL;

        for ($index = 1; $index < count($data); $index++) {


            try {

                $previous = $data[$index - 1];
                $orgClose = $data[$index]['close'];
                $percent  = round($previous['percent'] / 100, 4);
                $newClose = $previous['close'] / (1 + $percent);

                $data[$index]['close'] = $newClose;

                $data[$index]['high']    = round($data[$index]['high'] * $data[$index]['close'] / $orgClose, 2);
                $data[$index]['open']    = round($data[$index]['open'] * $data[$index]['close'] / $orgClose, 2);
                $data[$index]['low']     = round($data[$index]['low'] * $data[$index]['close'] / $orgClose, 2);
                $data[$index - 1]['chg'] = round($data[$index - 1]['close'] - $data[$index]['close'], 2);
            } catch (Exception $e) {

                echo count($data) . PHP_EOL;
                echo $e->getTraceAsString() . PHP_EOL;
                echo '$index =' . $index . PHP_EOL;
                var_dump($data);

            }
        }
        for ($index = 0; $index < count($data); $index++) {
            $data[$index]['stock_code'] = $stockCode;
        }

        $result = self::ma($data);

        return $result;
    }

    /**
     * @param $stockCode
     * @param $startDate
     * @param $data
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public static function ma($data)
    {

        if (empty($data)) {
            return [];
        }
        $startDate = $data[0]['trade_date'];
        foreach ($data as $item) {
            $stockCode = $item['stock_code'];
            $startDate = $startDate > $item['trade_date'] ? $item['trade_date'] : $startDate;
        }

        usort($data, function ($a, $b) {
            return $a['trade_date'] < $b['trade_date'] ? -1 : 1;
        });

        $sql = <<<EOL
select 
*
from daily 
where stock_code = '$stockCode'
and trade_date<'$startDate'
order by trade_date desc
limit 30;
EOL;

        echo $sql . PHP_EOL;
        $dbDaily = Yii::$app->getDb()->createCommand($sql)->queryAll();

        $length = 0;

        $sort = 0;

        if (!empty($dbDaily)) {
            $length  = count($dbDaily);
            $dbDaily = array_reverse($dbDaily);

            $sort = ArrayHelper::getValue($dbDaily[count($dbDaily) - 1], 'sort', 0);
            echo 'sort:' . $sort . PHP_EOL;

            foreach ($data as $item) {
                $dbDaily[] = $item;
            }
            $data = $dbDaily;
        }

        $ma5  = 0;
        $ma10 = 0;
        $ma20 = 0;
        $ma30 = 0;

        for ($index = 0; $index < count($data); $index++) {
            $close = $data[$index]['close'];
            $ma5   = $ma5 + $close;
            $ma10  = $ma10 + $close;
            $ma20  = $ma20 + $close;
            $ma30  = $ma30 + $close;
            if ($index >= 5) {
                $ma5                 = $ma5 - $data[$index - 5]['close'];
                $data[$index]['ma5'] = round($ma5 / 5, 2);
            } else {
                $data[$index]['ma5'] = $ma5 / ($index + 1);
            }
            if ($index >= 10) {
                $ma10                 = $ma10 - $data[$index - 10]['close'];
                $data[$index]['ma10'] = $ma10 / 10;
            } else {
                $data[$index]['ma10'] = $ma10 / ($index + 1);
            }
            if ($index >= 20) {
                $ma20                 = $ma20 - $data[$index - 20]['close'];
                $data[$index]['ma20'] = $ma20 / 20;
            } else {
                $data[$index]['ma20'] = $ma20 / ($index + 1);
            }
            if ($index >= 30) {
                $ma30                 = $ma30 - $data[$index - 30]['close'];
                $data[$index]['ma30'] = $ma30 / 30;
            } else {
                $data[$index]['ma30'] = $ma30 / ($index + 1);
            }
            if ($index > 1) {
                $data[$index]['ma5p']  = round(100 * ($data[$index]['ma5'] - $data[$index - 1]['ma5']) / $data[$index - 1]['ma5'], 2);
                $data[$index]['ma10p'] = round(100 * ($data[$index]['ma10'] - $data[$index - 1]['ma10']) / $data[$index - 1]['ma10'], 2);
                $data[$index]['ma20p'] = round(100 * ($data[$index]['ma20'] - $data[$index - 1]['ma20']) / $data[$index - 1]['ma20'], 2);
                $data[$index]['ma30p'] = round(100 * ($data[$index]['ma30'] - $data[$index - 1]['ma30']) / $data[$index - 1]['ma30'], 2);
            } else {
                $data[$index]['ma5p']  = $data[$index]['percent'];
                $data[$index]['ma10p'] = $data[$index]['percent'];
                $data[$index]['ma20p'] = $data[$index]['percent'];
                $data[$index]['ma30p'] = $data[$index]['percent'];
            }

            if (!isset($data[$index]['sort'])) {
                $sort++;
                $data[$index]['sort'] = $sort;
            }
        }

        $result = array_slice($data, $length);

        return $result;
    }
}