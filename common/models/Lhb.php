<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lhb".
 *
 * @property int $id
 * @property string $stock_code
 * @property string $stock_name
 * @property string $trade_date
 * @property string $direction
 * @property string $type
 * @property string $comName
 * @property string $buyAmount
 * @property string $sellAmount
 */
class Lhb extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lhb';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_code', 'stock_name', 'trade_date', 'direction', 'type', 'comName'], 'required'],
            [['trade_date'], 'safe'],
            [['direction', 'buyAmount', 'sellAmount'], 'string'],
            [['stock_code'], 'string', 'max' => 6],
            [['stock_name'], 'string', 'max' => 20],
            [['type'], 'string', 'max' => 2],
            [['comName'], 'string', 'max' => 128],
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
            'stock_name' => 'Stock Name',
            'trade_date' => 'Trade Date',
            'direction' => 'Direction',
            'type' => 'Type',
            'comName' => 'Com Name',
            'buyAmount' => 'Buy Amount',
            'sellAmount' => 'Sell Amount',
        ];
    }
}
