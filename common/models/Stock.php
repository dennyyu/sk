<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "stock".
 *
 * @property int $id
 * @property string $stock_code
 * @property string $stokc_name
 */
class Stock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_code', 'stokc_name'], 'required'],
            [['stock_code'], 'string', 'max' => 6],
            [['stokc_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stock_code' => 'Stock Code',
            'stokc_name' => 'Stokc Name',
        ];
    }


    public static function batchInsert($data)
    {
        if (empty($data)) {
            return;
        }

        $columns   = Stock::getTableSchema()->columnNames;
        $cmdParams = [];
        foreach ($data as $item) {
            $cmdParams[] = array_intersect_key($item, array_flip($columns));
        }

        self::getDb()->createCommand()->batchInsert(self::tableName(), array_keys($cmdParams[0]), $cmdParams)
            ->execute();
    }


}
