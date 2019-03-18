<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2018/10/16
 * Time: 下午10:57
 */

namespace console\controllers;

use common\analysis\Analysor;
use common\models\Daily;
use common\models\Trade;
use yii\console\Controller;

class MaController extends Controller
{
    public function actionCloseVsMa30($stockCode)
    {
        $data  = Daily::getDb()
            ->createCommand('select * from daily where stock_code = :stock_code;', [':stock_code' => $stockCode])
            ->queryAll();
        $trade = Analysor::closeVsma30($data);

        Trade::deleteAll(['stock_code' => $stockCode]);
        Trade::batchInsert($trade);
    }
}