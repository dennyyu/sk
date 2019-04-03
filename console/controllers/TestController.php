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
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionXml()
    {
        $stock = Eastmoney::tradeDetail('2019-01-07');

        Sina::getLhbDetail($stock[0]);
        echo count($stock);

    }

    public function actionWebdriver()
    {
        $driver = null;
        try {
            $host = 'http://127.0.0.1:9515'; // this is the default
            $capabilities = DesiredCapabilities::chrome();
            $driver = RemoteWebDriver::create($host, $capabilities, 5000);
            $driver->get('http://www.iwencai.com/stockpick/search?typed=0&preParams=&ts=1&f=1&qs=result_original&selfsectsn=&querytype=stock&searchfilter=&tid=stockpick&w=600080+2019-04-02+%E6%B6%A8%E5%81%9C%E5%8E%9F%E5%9B%A0');


            $element = $driver->findElement(WebDriverBy::className('dp_articleswithfeedback_block_con'));
            $element = $element->findElement(WebDriverBy::className('summ'));

            echo $element->getText();
        } catch (\Exception $ex) {

        } finally {
            if ($driver) {
                $driver->close();
            }
        }


    }

}