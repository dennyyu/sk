<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2018/10/14
 * Time: 下午4:41
 */

namespace common\helpers;

class ExcelHelper
{
    public static function toArray($text, $columns, $startRow = 1)
    {
        if (!isset($text)) {
            return [];
        }

        $column_split = ",";
        $rows         = explode(PHP_EOL, $text);
        if (empty($rows)) {
            return [];
        }
        $result = [];

        for ($index = $startRow; $index < count($rows); $index++) {
            $row = $rows[$index];
            if (strlen($row) > 0) {
                $value = explode($column_split, $row);
                if (!empty($value)) {
                    $item               = array_combine($columns, array_slice($value, 0, count($columns)));
                    $item['stock_code'] = str_replace("'", "", $item['stock_code']);
                    $result[]           = $item;
                }
            }
        }

        return $result;
    }
}