<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sqlscript".
 *
 * @property integer $id
 * @property string $topic
 * @property string $sql_script
 * @property string $user
 * @property string $d_update
 */
class Sqlscript extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sqlscript';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sql_script'], 'string'],
            [['d_update'], 'safe'],
            [['topic', 'user'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'topic' => 'ชื่อ Script',
            'sql_script' => 'Code Sql Script',
            'user' => 'User',
            'd_update' => 'วันที่สร้าง',
        ];
    }
}
