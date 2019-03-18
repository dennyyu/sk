<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2018/10/14
 * Time: 下午4:55
 */

namespace console\controllers;

use common\channel\Sina;
use common\channel\WY163;
use common\helpers\Dh;
use common\helpers\SqlHelper;
use common\models\Daily;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class WyController extends Controller
{

    public static function actionDailyAll($append = true)
    {
        if (!$append) {
            Daily::truncate();
        }
        $sql = <<<EOL
select a.`stock_code`
from stock as a
left join daily as b 
on a.`stock_code` = b.`stock_code`
where b.`stock_code` is null;
EOL;

        $allStock = Yii::$app->getDb()->createCommand($sql)->queryAll();

        $today = Dh::date();
        foreach ($allStock as $stock) {
            try {
                self::actionStock($stock['stock_code'], '1990-01-01', $today);
            } catch (Exception $ex) {
                Yii::error($ex);
            }
        }
    }

    public static function actionStock($stockCode, $startDate, $endDate)
    {
        $dailyList = WY163::getDailyList($stockCode, $startDate, $endDate);


        Daily::deleteAll('stock_code=:stock_code and trade_date>=:startDate and trade_date<=:endDate', [
            ':stock_code' => $stockCode,
            ':startDate'  => $startDate,
            ':endDate'    => $endDate,
        ]);

        SqlHelper::batchInsert('daily', $dailyList);
    }

    public static function actionDownloadDaily()
    {

        $stock_date = null;

        $daily_max_date = Yii::$app->getDb()
            ->createCommand('select max(trade_date) as max_date from daily where trade_date<date(now())')
            ->queryAll();
        if (empty($daily_max_date)) {
            return;
        }

        $stock_date = $daily_max_date[0]['max_date'];

        $today = Dh::date();
        while (true) {
            $stock_date = Dh::addDays(1, $stock_date);
            if ($stock_date <= $today) {
                if (!Dh::isHoliday($stock_date)) {
                    echo date('w', strtotime($stock_date));
                    break;
                }
            } else {
                return;
            }
        }

        $dailyList = Sina::getDailyList();
        $sql       = <<<EOL
select a.* 
from daily as a
inner join (
select stock_code,max(trade_date) as trade_date
from daily
group by stock_code) as b 
on a.stock_code=b.stock_code and a.trade_date=b.trade_date;
EOL;

        $lastDailyList = Yii::$app->getDb()->createCommand($sql)->queryAll();

        $insertList = [];

        foreach ($lastDailyList as $dbItem) {
            $currentItem = null;
            foreach ($dailyList as $tempItem) {
                if ($tempItem['stock_code'] == $dbItem['stock_code']
                    && $tempItem['close'] != $dbItem['close']
                    && $tempItem['open'] != $dbItem['open']
                    && $tempItem['low'] != $dbItem['low']
                    && $tempItem['high'] != $dbItem['high']
                    && $tempItem['turnrate'] != $dbItem['turnrate']) {
                    $currentItem = $tempItem;
                    break;
                }
            }

            if (empty($currentItem)) {
                continue;
            }

            $dbItem             = array_merge($dbItem, array_intersect_key($currentItem, $dbItem));
            $dbItem['trade_date'] = $stock_date;

            unset($dbItem['id']);
            $stock_code = $dbItem['stock_code'];

            $sql  = <<<EOL
select `close`
from daily 
where stock_code = '$stock_code'
order by trade_date desc
limit 30
EOL;
            $data = Yii::$app->getDb()->createCommand($sql)->queryAll();
            $ma5  = 0;
            $ma10 = 0;
            $ma20 = 0;
            $ma30 = 0;
            if (!empty($data)) {
                for ($index = 0; $index < count($data); $index++) {
                    $ma5  += $index < 4 ? $data[$index]['close'] : 0;
                    $ma10 += $index < 9 ? $data[$index]['close'] : 0;
                    $ma20 += $index < 19 ? $data[$index]['close'] : 0;
                    $ma30 += $index < 29 ? $data[$index]['close'] : 0;
                }
                $dbItem['ma5']  = ($ma5 + $currentItem['close']) / min(count($data), 5);
                $dbItem['ma10'] = ($ma10 + $currentItem['close']) / min(count($data), 10);
                $dbItem['ma20'] = ($ma20 + $currentItem['close']) / min(count($data), 20);
                $dbItem['ma30'] = ($ma30 + $currentItem['close']) / min(count($data), 30);
            }

            $insertList[] = $dbItem;
        }

        Daily::batchInsert($insertList);

    }

    public static function actionMiss()
    {

        $allStock = [
            '000503',
            '000537',
            '000540',
            '000576',
            '000617',
            '000671',
            '000679',
            '000790',
            '000796',
            '000801',
            '000839',
            '000851',
            '000856',
            '000897',
            '000908',
            '000961',
            '000969',
            '002011',
            '002089',
            '002107',
            '002141',
            '002158',
            '002189',
            '002348',
            '002445',
            '002509',
            '002607',
            '002611',
            '002677',
            '002708',
            '002721',
            '002839',
            '002878',
            '002935',
            '002943',
            '002946',
            '300007',
            '300100',
            '300139',
            '300148',
            '300197',
            '300210',
            '300266',
            '300292',
            '300351',
            '300589',
            '300675',
            '300675',
            '300675',
            '300675',
            '600327',
            '600478',
            '600525',
            '600532',
            '600536',
            '600643',
            '600687',
            '600855',
            '600929',
            '600986',
            '601128',
            '601388',
            '603369',
            '603700',
        ];

        $today = Dh::date();
        foreach ($allStock as $stock) {
            try {
                self::actionStock($stock, '1990-01-01', $today);
            } catch (Exception $ex) {
                Yii::error($ex);
            }
        }

    }

    public function actionAppend()
    {

        $sql = <<<EOL
select a.`stock_code`,max(trade_date) as max_trade
from daily as a
group by a.stock_code
EOL;

        $allStock = Yii::$app->getDb()->createCommand($sql)->queryAll();

        $endDate = Dh::date();

        while (Dh::isHoliday($endDate)) {
            $endDate = Dh::addDays(-1, $endDate);
        }

        foreach ($allStock as $stock) {
            try {
                if ($stock['max_trade'] < $endDate) {
                    self::actionStock($stock['stock_code'], Dh::addDays(1, ArrayHelper::getValue($stock, 'max_trade', '1990-01-01')), Dh::date());
                }
            } catch (Exception $ex) {
                Yii::error($ex);
            }
        }

    }

}