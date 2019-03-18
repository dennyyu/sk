<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property int    $id
 * @property string $name
 * @property int    $parent
 * @property string $route
 * @property int    $order
 * @property string $data
 *
 * @property Menu   $parent0
 * @property Menu[] $menus
 */
class Menu extends ActiveRecord

{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent', 'order'], 'integer'],
            [['data'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['route'], 'string', 'max' => 256],
            [
                ['parent'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Menu::className(),
                'targetAttribute' => ['parent' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'     => 'ID',
            'name'   => 'Name',
            'parent' => 'Parent',
            'route'  => 'Route',
            'order'  => 'Order',
            'data'   => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(Menu::className(), ['id' => 'parent']);
    }

    /**
     * @return []
     */
    public static function getMenus()
    {
        $result = [];
        $menus  = self::findBySql('select * from menu ', null)->all();
        if (!empty($menus)) {
            foreach ($menus as $menu) {
                $item          = [];
                $item['label'] = $menu['name'];
                $item['url']   = $menu['route'];
                $item['id']    = $menu['id'];
                $parent        = self::getParentMenu($result, $menu['id']);
                if ($parent) {
                    $parent['items'][] = $item;
                } else {
                    $result[] = $item;
                }
            }
        }

        return $result;
    }

    private static function getParentMenu($menu, $parentId)
    {
        if (!$menu) {
            return null;
        }

        if (ArrayHelper::getValue($menu, 'id', null) == $parentId) {
            return $menu;
        }

        if (!empty($menu['itmes'])) {
            foreach ($menu['itmes'] as $itmes) {
                $itme = self::getParentMenu($itmes, $parentId);
                if (!$itme) {
                    return $itme;
                }
            }
        }

        return null;
    }

}
