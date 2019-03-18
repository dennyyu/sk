<?php
namespace console\controllers;
use common\models\User;
use yii\console\Controller;

/**
 * Created by PhpStorm.
 * User: denny
 * Date: 2018/10/13
 * Time: 上午11:38
 */
class UserController extends Controller
{
    /**
     * @param $name
     * @param $password
     * @param $email
     */
    public function actionSignup($name, $password, $email)
    {
        $user           = new User();
        $user->username = $name;
        $user->setPassword($password);
        $user->email = $email;
        $result      = $user->save();

        if (!$result) {
            echo '注册用户失败';
        }

    }
}