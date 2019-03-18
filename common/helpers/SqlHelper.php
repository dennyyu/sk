<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2019/3/2
 * Time: 上午12:12
 */

namespace common\helpers;

use Yii;
use yii\helpers\ArrayHelper;

class SqlHelper
{
    public static function execute($sql)
    {
        return Yii::$app->getDb()->createCommand($sql)->execute();
    }

    public static function queryAll($sql)
    {
        return Yii::$app->getDb()->createCommand($sql)->queryAll();
    }

    public static function getTableSchema($tableNae)
    {
        $tableSchema = Yii::$app->getDb()
            ->getSchema()
            ->getTableSchema($tableNae);

        if ($tableSchema === null) {
            throw new InvalidConfigException('The table does not exist: ' . static::tableName());
        }

        return $tableSchema;
    }

    public static function batchInsert($tableName, $data)
    {
        if (empty($data)) {
            return;
        }

        $columns   = self::getTableSchema($tableName)->columnNames;
        $cmdParams = [];
        foreach ($data as $item) {
            $cmdParams[] = array_intersect_key($item, array_flip($columns));

        }

        self::getDb()->createCommand()->batchInsert($tableName, array_keys($cmdParams[0]), $cmdParams)
            ->execute();
    }

    public static function getDb()
    {
        return Yii::$app->getDb();
    }

}