<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "daily".
 *
 * @property int    $id
 * @property string $stock_code
 * @property string $trade_date
 * @property string $open
 * @property string $high
 * @property string $close
 * @property string $low
 * @property string $chg
 * @property string $percent
 * @property string $ma5
 * @property string $ma10
 * @property string $ma20
 * @property string $ma30
 * @property string $turnrate
 * @property string $dif
 * @property string $dea
 * @property string $macd
 */
class Daily extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'daily';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_code'], 'required'],
            [['trade_date'], 'safe'],
            [
                [
                    'open',
                    'high',
                    'close',
                    'low',
                    'chg',
                    'percent',
                    'ma5',
                    'ma10',
                    'ma20',
                    'ma30',
                    'turnrate',
                    'dif',
                    'dea',
                    'macd',
                ],
                'number',
            ],
            [['stock_code'], 'string', 'max' => 6],
            [['stock_code', 'trade_date'], 'unique', 'targetAttribute' => ['stock_code', 'trade_date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'stock_code' => 'Stock Code',
            'trade_date'   => 'Trade At',
            'open'       => 'Open',
            'high'       => 'High',
            'close'      => 'Close',
            'low'        => 'Low',
            'chg'        => 'Chg',
            'percent'    => 'Percent',
            'ma5'        => 'Ma5',
            'ma10'       => 'Ma10',
            'ma20'       => 'Ma20',
            'ma30'       => 'Ma30',
            'turnrate'   => 'Turnrate',
            'dif'        => 'Dif',
            'dea'        => 'Dea',
            'macd'       => 'Macd',
        ];
    }

    public static function batchInsert($data)
    {
        if (empty($data)) {
            return;
        }

        $columns   = Daily::getTableSchema()->columnNames;
        $cmdParams = [];
        foreach ($data as $item) {
            $cmdParams[] = array_intersect_key($item, array_flip($columns));
        }

        self::getDb()->createCommand()->batchInsert(self::tableName(), array_keys($cmdParams[0]), $cmdParams)
            ->execute();
    }

    public static function truncate(){
        self::getDb()->createCommand('truncate table daily')->execute();
    }

}
