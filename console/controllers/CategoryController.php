<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2019/3/5
 * Time: 下午9:46
 */

namespace console\controllers;

use common\channel\Jqka;
use common\helpers\SqlHelper;
use yii\console\Controller;

class CategoryController extends Controller
{
    public function actionGn()
    {
        $category_sql = <<<EOL
        
        select distinct `code`, `name`,`type`
from category;
        
EOL;

        $category_type = [
            'thshy' => '199112',
            'gn'    => '264648',
        ];

        $category_array = SqlHelper::queryAll($category_sql);

        $data = Jqka::categoryDetail('thshy',$category_type['thshy'],'881114');

        var_dump($data);

        return;

        foreach ($category_array as $category) {
            Jqka::categoryDetail($category);

        }

    }

}