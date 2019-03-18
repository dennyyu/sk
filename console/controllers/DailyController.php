<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2018/10/14
 * Time: 下午4:55
 */

namespace console\controllers;

use common\channel\Sina;
use common\helpers\Dh;
use common\models\Daily;
use common\models\Stock;
use Yii;
use yii\console\Controller;

class DailyController extends Controller
{

    public function actionSina()
    {
        $stock_date = Dh::date();

        while (Dh::isHoliday($stock_date)) {
            $stock_date = Dh::addDays(-1, $stock_date);
        }

        $dailyList = Sina::getDailyList();

        $columns = [
            'stock_code',
            'stock_name',
            'close',
            'chg',
            'percent',
            'buy',
            'sell',
            'settlement',
            'open',
            'high',
            'low',
            'volume',
            'amount',
            'ticktime',
            'per',
            'pb',
            'mktcap',
            'nmc',
            'turnrate',
            'trade_date',
        ];

        if (!empty($dailyList)) {
            for ($index = 0; $index < count($dailyList); $index++) {
                $dailyList[$index]['trade_date'] = $stock_date;
                unset($dailyList[$index]['symbol']);
            }
            $delete_sql = "delete from sina_daily where trade_date='$stock_date'";
            echo $delete_sql . PHP_EOL;

            Yii::$app->getDb()->createCommand($delete_sql)->execute();
            Yii::$app->getDb()->createCommand()->batchInsert('sina_daily', $columns, $dailyList)->execute();
        }
    }

}