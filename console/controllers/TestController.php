<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2019/2/16
 * Time: 下午11:25
 */

namespace console\controllers;

use common\channel\Eastmoney;
use common\channel\Sina;
use common\helpers\Dh;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionXml()
    {
        $stock = Eastmoney::tradeDetail('2019-01-07');

        Sina::getLhbDetail($stock[0]);
        echo count($stock);

    }

}