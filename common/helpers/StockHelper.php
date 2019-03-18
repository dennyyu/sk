<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2018/10/14
 * Time: 下午2:32
 */

namespace common\helpers;

use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

class StockHelper
{
    const STOCK_START_DATE = '1990-01-01';

    public static function short($stockCode)
    {
        if (empty($stockCode)) {
            return "";
        }
        $stockCode = trim($stockCode);
        if (strlen($stockCode) > 6) {
            return substr($stockCode, strlen($stockCode) - 6, 6);
        }

        return $stockCode;
    }

    public static function long($stockCode)
    {
        if (empty($stockCode)) {
            return "";
        }
        $stockCode = trim($stockCode);
        if (strlen($stockCode) <= 6) {

            if (ArrayHelper::isIn($stockCode, ['000001'])) {
                return "sh" . $stockCode;
            }
            if (StringHelper::startsWith($stockCode, "60")) {
                return "sh" . $stockCode;
            } else {
                return 'sz' . $stockCode;
            }
        }

        return $stockCode;

    }

}