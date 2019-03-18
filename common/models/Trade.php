<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "trade".
 *
 * @property int    $id
 * @property string $stock_code
 * @property string $buy_at
 * @property string $buy_amount
 * @property string $sell_at
 * @property string $sell_amount
 * @property string $net_amount
 */
class Trade extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trade';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_code', 'buy_amount', 'sell_amount', 'net_amount'], 'required'],
            [['buy_at', 'sell_at'], 'safe'],
            [['buy_amount', 'sell_amount', 'net_amount'], 'number'],
            [['stock_code'], 'string', 'max' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'stock_code'  => 'Stock Code',
            'buy_at'      => 'Buy At',
            'buy_amount'  => 'Buy Amount',
            'sell_at'     => 'Sell At',
            'sell_amount' => 'Sell Amount',
            'net_amount'  => 'Net Amount',
        ];
    }

    public static function batchInsert($data)
    {
        if (empty($data)) {
            return;
        }

        $columns   = Trade::getTableSchema()->columnNames;
        $cmdParams = [];
        foreach ($data as $item) {
            $cmdParams[] = array_intersect_key($item, array_flip($columns));
        }

        self::getDb()->createCommand()->batchInsert(self::tableName(), array_keys($cmdParams[0]), $cmdParams)
            ->execute();
    }

}
