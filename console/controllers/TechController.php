<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2019/3/1
 * Time: 下午11:55
 */

namespace console\controllers;

use common\helpers\SqlHelper;
use yii\console\Controller;

class TechController extends Controller
{

    public function actionHuiTiao()
    {
        $sql     = <<<EOL
select 
*
from daily 
where trade_date>date_sub(now(),interval 30 day)
order by stock_code,trade_date DESC;
EOL;
        $dbDaily = SqlHelper::queryAll($sql);

        $result = [];

        foreach ($dbDaily as $daily) {
            $result[$daily['stock_code']][] = $daily;
        }

        foreach ($result as $stockDaily) {
            for ($index = 0; $index < count($stockDaily); $index++) {


            }

        }

    }

    public function actionPreg()
    {
        $str = <<<EOL
        <td><a class="j_addStock" title="加自选" href="javascript:void(0);"><img src="http://i.thsi.cn/images/q/plus_logo.png" alt=""></a></td>
            </tr>
                        <tr>
                <td>17</td>
                <td><a href="http://stockpage.10jqka.com.cn/600499/" target="_blank">600499</a></td>
                <td><a href="http://stockpage.10jqka.com.cn/600498" target="_blank">科达洁能</a></td>
                <td class="c-rise">5.80</td>
                <td class="c-rise">5.46</td>
                <td class="c-rise">0.30</td>
                <td class="">0.00</td>
                <td>2.02</td>
                <td class="c-rise">1.43</td>
                <td class="c-rise">6.73</td>
                <td>1.61亿</td>
                <td>14.11亿</td>
                <td>81.86亿</td>
                <td>21.71</td>
EOL;

        $match_array = preg_match_all("/cn\/(\\d{6})\//",$str,$out,PREG_PATTERN_ORDER);
        var_dump($out);





    }
}