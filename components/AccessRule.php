<?php

namespace app\components;
use dektrium\user\models\User;

class AccessRule extends \yii\filters\AccessRule{
    protected function matchRole($user){

        if(empty($this->roles)){
            return true;
        }
        foreach ($this->roles as $role){

            if($role == '?' && $user->getIsGuest()){
                    return true;
            }
            elseif($role == User::ROLE_USER && !$user->getIsGuest()){
                    return true;
            }
            elseif(!$user->getIsGuest() && $role == $user->identity->role){
                return true;
            }
        }
        return false;
    }
}

