<?php
/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2019/2/16
 * Time: 下午11:06
 */

namespace common\helpers;

use DOMNode;

class XmlHelper
{
    public static function getNodeByAttribute(DOMNode $node, $conditions)
    {
        if (!isset($node)) {
            return null;
        }
        if (empty($conditions)) {
            return $node;
        }

        if (!empty($node->attributes)) {
            $find = true;
            foreach ($conditions as $attKey => $attValue) {
                foreach ($node->attributes as $index => $attr) {
                    if ($attr->name == $attKey && $attr->value == $attValue) {
                        continue;
                    }
                    $find = false;
                    break;
                }
            }
            if ($find) {
                return $node;
            }
        }

        if (!empty($node->childNodes)) {
            foreach ($node->childNodes as $childNode) {
                $findNode = self::getNodeByAttribute($childNode, $conditions);
                if (!empty($findNode)) {
                    return $findNode;
                }
            }
        }

        return null;

    }

}