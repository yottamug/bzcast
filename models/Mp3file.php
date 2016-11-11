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
class Mp3file extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mp3file';
    }

	public function isNew($filename)
	{
		$mp3file = Mp3file::findOne([
		    'filename' => $filename,
		]);		
		
		if ($mp3file) return false;
		else return true;
	}
	

	public function clean()
	{
		// remove record if the file no longer exists
		$mp3files = Mp3file::find()->all();
		foreach ($mp3files as $row)
		{
			if ( ! file_exists($row->filename) )
			{
				if (($model = Mp3file::findOne($row->id)) !== null) {
					$model->delete();
				}
			}
		}
	}

	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['category'], 'safe'],
            [['filename', 'author', 'pubDate','duration'], 'string', 'max' => 255],
        ];
    }

}
