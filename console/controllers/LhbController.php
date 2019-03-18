<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2019/2/17
 * Time: 上午1:11
 */

namespace console\controllers;

use common\channel\Eastmoney;
use common\channel\Sina;
use common\helpers\Dh;
use Yii;
use yii\console\Controller;

class LhbController extends Controller
{
    public function actionDownload($startDate = null, $endDate = null)
    {

        echo '开始下载龙虎榜' . PHP_EOL;

        $endDate = Dh::date($endDate);

        if (empty($startDate)) {

            $max_date = Yii::$app->getDb()->createCommand('select max(trade_date) as trade_date from lhb;')->queryAll();
            if (!empty($max_date)) {
                $startDate = $max_date[0]['trade_date'];
            }
        }

        for ($date = Dh::date($startDate); $date <= $endDate; $date = Dh::addDays(1, $date)) {
            self::download($date);
        }

        echo '结束下载龙虎榜' . PHP_EOL;
    }

    private function download($date)
    {

        echo '开始下载' . $date;

        $stockList = Eastmoney::tradeDetail($date);

        echo '.需下载个数' . count($stockList) . PHP_EOL;

        if (empty($stockList)) {
            return;
        }

        $sqlRow = [];

        foreach ($stockList as $stock) {
            $lhb_data = Sina::getLhbDetail($stock);
            if (empty($lhb_data)) {
                echo $stock['stock_code'] . $stock['trade_date'] . '未从sina获取到龙湖明细' . PHP_EOL;
                continue;
            }

            $columns = [
                'stock_code',
                'type',
                'comName',
                'buyAmount',
                'sellAmount',
                'trade_date',
                'stock_name',
                'direction',
            ];

            $lhb_data = array_merge($lhb_data['buy'], $lhb_data['sell']);

            foreach ($lhb_data as $row) {
                $row['buyAmount']  = $row['buyAmount'] * 10000;
                $row['sellAmount'] = $row['sellAmount'] * 10000;
                $row['trade_date'] = $stock['trade_date'];
                $row['stock_name'] = $stock['stock_name'];
                $row['direction']  = $row['buyAmount'] > $row['sellAmount'] ? 'buy' : 'sell';

                $row_key = $row['stock_code'] . $row['trade_date'] . $row['comCode'] . $row['comName'];
                unset($row['comCode']);
                unset($row['netAmount']);

                if (isset($sqlRow['$row_key'])) {
                    $sqlRow[$row_key]['buyAmount']  = $row['sellAmount'] > $sqlRow[$row_key]['buyAmount']
                        ? $row['sellAmount'] : $sqlRow['$row_key']['buyAmount'];
                    $sqlRow[$row_key]['sellAmount'] = $row['sellAmount'] > $sqlRow[$row_key]['sellAmount']
                        ? $row['sellAmount'] : $sqlRow['$row_key']['sellAmount'];
                } else {
                    $sqlRow [$row_key] = $row;
                }

            }
        }

        Yii::$app->getDb()->createCommand("delete from lhb where trade_date='$date'")->execute();

        Yii::$app->getDb()->createCommand()->batchInsert('lhb', $columns, $sqlRow)
            ->execute();

    }

}