<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "dosagedb".
 *
 * @property integer $id
 * @property string $code
 * @property string $name_eng
 * @property string $name_chh
 * @property string $name_chs
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }


}
