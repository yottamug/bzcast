<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Mp3file;
use app\models\BzId3;
use app\models\Category;
use app\models\FeedXML;

class PodcastController extends Controller
{
	
		
	public function actionFeed($category_id='')
	{	
		$feedXML = new FeedXML;
		if ($category_id)
		{
			if ( is_numeric($category_id) )	$category = Category::findone($category_id);
			else 							$category = Category::find()->where(['feed' => $category_id])->one();
			
			if (! $category ) 
			{
				echo "====================================================\n";
				echo "Invalid Category, Please Check Again!\n";
				echo "Program Terminated!\n";
				echo "====================================================\n\n";
				exit;
			}
			
			$condition = json_decode($category['condition'],true);
			$mp3files = Mp3file::find()->andFilterWhere($condition)->orderBy(['pubDate' => SORT_DESC])->all();
			
			$feedXML->filename 		= $category['feed'];
			$feedXML->mp3files 		= $mp3files;
			$feedXML->title			= $category['title'];
			$feedXML->subtitle		= "Podcast Archive";
			$feedXML->summary		= "Podcast Archive";
			$feedXML->description	= "Podcast Archive";
			
			
			// PodcastController::feedCreate($mp3files, $category['feed'], $category['title']);
		}
		else
		{
			$mp3files = Mp3file::find()->limit(30)->orderBy(['pubDate' => SORT_DESC])->all();
			
			$feedXML->filename 		= Yii::$app->params['podcast_feedname'];
			$feedXML->mp3files 		= $mp3files;
			$feedXML->title			= Yii::$app->params['podcast_title'];
			$feedXML->subtitle		= Yii::$app->params['podcast_subtitle'];
			$feedXML->summary		= Yii::$app->params['podcast_summary'];
			$feedXML->description	= Yii::$app->params['podcast_description'];			
			
			// PodcastController::feedCreate($mp3files, "bzfeed");			
		}
		$feedXML->generateFeed();
	}
	
	public function actionUpdate()
	{
		list ($status, $message) = PodcastController::actionUpdatedb();
		echo $message;
		if ($status)
		{
			echo "Feed XML is creating/updating...";
			PodcastController::actionFeed();
			echo "Feed XML has been created/updated!";
		}
	}
	
	public function actionUpdatedb()
	{
		$podcastdir = Yii::$app->params['bzcast_podcastdir'];
		$files = \yii\helpers\FileHelper::findFiles($podcastdir,[
			'only'		=> ['*.mp3'],
			'except'	=> ['.@__thumb']					
			]);

		$new_counter = 0;
		foreach ($files as $file)
		{
			if ( Mp3file::isNew($file) )
			{
				$info = BzId3::GetMp3Info($file);
				
				$new_counter++;
				$mp3file = new Mp3file;
				$mp3file->title		= $info['title'];
				$mp3file->filename	= $info['filename'];
				$mp3file->category  = $info['category'];
				$mp3file->author    = $info['author'];
				$mp3file->pubDate   = $info['pubDate'];
				$mp3file->duration  = $info['duration'];
				$mp3file->size      = $info['size'];
				$mp3file->bitrate   = $info['bitrate'];
				$mp3file->frequency = $info['frequency'];
				$mp3file->save();
			}
		}
		$message  = "Total Files: ".count($files)."\nNew Files: ".$new_counter."\n";
		return array($new_counter, $message);
	}
	
}
