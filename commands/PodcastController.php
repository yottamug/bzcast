<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Mp3file;
use app\models\BzId3;
use app\models\Category;
use app\models\FeedXML;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
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
		$podcastdir = Yii::$app->params['podcastdir'];
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
	
	
	
	public function feedCreate($mp3files, $feedname, $title='Podcast Library')
	{
		$feeddir = Yii::$app->params['feeddir'];
		$xml = new \DOMDocument( "1.0", "utf-8" );
		$xml->formatOutput = true;
		// Create some elements.
		$xml_rss = $xml->createElement( "rss" );

		// Set the attributes.
		$xml_rss->setAttribute( "xmlns:atom", "http://www.w3.org/2005/Atom" );
		$xml_rss->setAttribute( "xmlns:itunes", "http://www.itunes.com/dtds/podcast-1.0.dtd" );
		$xml_rss->setAttribute( "xml:lang", "en" );
		
		// $xml_rss->setAttribute( "xmlns:itunesu", "http://www.itunesu.com/feed" );
		$xml_rss->setAttribute( "version", "2.0" );
		
		$xml_channel = $xml->createElement( "channel" );
		
		$xml_link 				= $xml->createElement( "link", 				Yii::$app->params['podcast_link'] );
		// $xml_language 			= $xml->createElement( "language", 			'en' );
		$xml_copyright 			= $xml->createElement( "copyright", 		'&#xA9;'.date("Y") );
		$xml_webMaster 			= $xml->createElement( "webMaster", 		'podcast@bztk.com (Kenny)' );
		$xml_managingEditor 	= $xml->createElement( "managingEditor",	'podcast@bztk.com (Kenny)' );
		
		$xml_image_url 			= $xml->createElement( "url",				Yii::$app->params['podcast_image'] );
		$xml_image_title 		= $xml->createElement( "title",				$title );
		$xml_image_link 		= $xml->createElement( "link",				Yii::$app->params['podcast_link'] );
		$xml_image = $xml->createElement( "image" );
		$xml_image->appendChild( $xml_image_url );
		$xml_image->appendChild( $xml_image_title );
		$xml_image->appendChild( $xml_image_link );
		
		
		$xml_itunes_name		= $xml->createElement( "itunes:name",				'Kenny' );
		$xml_itunes_email		= $xml->createElement( "itunes:email",				'podcast@bztk.com' );
		$xml_itunes_owner = $xml->createElement( "itunes:owner" );
		$xml_itunes_owner->appendChild( $xml_itunes_name );
		$xml_itunes_owner->appendChild( $xml_itunes_email );
		
		$xml_itunes_explicit	= $xml->createElement( "itunes:explicit",				'no' );
		
		$xml_itunes_category	= $xml->createElement( "itunes:category");
		$xml_itunes_category->setAttribute( "text", 'Arts' );
		
		
		$xml_itunes_image		= $xml->createElement( "itunes:image" );
		$xml_itunes_image->setAttribute( "href", Yii::$app->params['podcast_image'] );

		$xml_itunes_summary	= $xml->createElement( "itunes:summary",				'A little description of your podcast.' );
		$xml_itunes_subtitle	= $xml->createElement( "itunes:subtitle",			'subtitle' );
					
		$xml_atom_link			= $xml->createElement( "atom:link" );
		$xml_atom_link->setAttribute( "href", Yii::$app->params['podcast_link']."feed/".$feedname.".xml" );
		$xml_atom_link->setAttribute( "rel", "self" );
		$xml_atom_link->setAttribute( "type", "application/rss+xml" );
		
		$xml_pubDate			= $xml->createElement( "pubDate", date(\DateTime::RFC1123) );

		$xml_title 				= $xml->createElement( "title", 			$title );
		$xml_itunes_author 		= $xml->createElement( "itunes:author", 	'Kenny' );
		$xml_description 		= $xml->createElement( "description", 		'' );
		$xml_lastBuildDate 		= $xml->createElement( "lastBuildDate", 	date(\DateTime::RFC1123) );
		
		
		$xml_channel->appendChild( $xml_link );
		// $xml_channel->appendChild( $xml_language );
		$xml_channel->appendChild( $xml_copyright );
		$xml_channel->appendChild( $xml_webMaster );
		$xml_channel->appendChild( $xml_managingEditor );
		$xml_channel->appendChild( $xml_image );
		$xml_channel->appendChild( $xml_itunes_owner );
		$xml_channel->appendChild( $xml_itunes_image );
		$xml_channel->appendChild( $xml_itunes_explicit );
		$xml_channel->appendChild( $xml_itunes_category );
		$xml_channel->appendChild( $xml_itunes_summary );
		$xml_channel->appendChild( $xml_itunes_subtitle );
		$xml_channel->appendChild( $xml_atom_link );
		// $xml_channel->appendChild( $xml_pubDate );
		$xml_channel->appendChild( $xml_title );
		$xml_channel->appendChild( $xml_itunes_author );
		$xml_channel->appendChild( $xml_description );
		$xml_channel->appendChild( $xml_lastBuildDate );
		
		foreach ($mp3files as $row)
		{
			$xml_item 				= $xml->createElement( "item" );
			$xml_item_title = $xml->createElement( "title", $row->title );
			$xml_item_enclosure = $xml->createElement( "enclosure" );
			$xml_item_enclosure->setAttribute( "url", Yii::$app->params['podcast_link']."web/index.php/download/".$row->id."/".$row->id.".mp3") ;
			$xml_item_enclosure->setAttribute( "type", "audio/mpeg" );
			$xml_item_enclosure->setAttribute( "length", $row->size );
			
			$xml_item_guid = $xml->createElement( "guid", Yii::$app->params['podcast_link']."web/index.php/download/".$row->id) ;
			$xml_item_link = $xml->createElement( "link", Yii::$app->params['podcast_link']."web/index.php/download/".$row->id) ;	

			$xml_item_itunes_subtitle	= $xml->createElement( "itunes:subtitle", $row->author );
			$xml_item_itunes_summary	= $xml->createElement( "itunes:summary", '<![CDATA[]]>' );
			$xml_item_description	= $xml->createElement( "description", $row->author );
			$xml_item_itunes_duration	= $xml->createElement( "itunes:duration", $row->duration );
			$xml_item_author	= $xml->createElement( "author", 'Kenny' );
			$xml_item_itunes_author	= $xml->createElement( "itunes:author", 'Kenny' );
			$xml_item_itunes_explicit	= $xml->createElement( "itunes:explicit", 'no' );
			$xml_item_pubDate	= $xml->createElement( "pubDate", date('D, d M Y H:i:s +0800', strtotime($row->pubDate) ) );
							
			$xml_item->appendChild( $xml_item_title );
			$xml_item->appendChild( $xml_item_enclosure );
			$xml_item->appendChild( $xml_item_guid );
			$xml_item->appendChild( $xml_item_link );
			
			$xml_item->appendChild( $xml_item_itunes_subtitle );
			// $xml_item->appendChild( $xml_item_itunes_summary );
			$xml_item->appendChild( $xml_item_description );
			$xml_item->appendChild( $xml_item_itunes_duration );
			$xml_item->appendChild( $xml_item_author );
			$xml_item->appendChild( $xml_item_itunes_author );
			$xml_item->appendChild( $xml_item_itunes_explicit );
			$xml_item->appendChild( $xml_item_pubDate );
			
			$xml_channel->appendChild( $xml_item );
		}
		

		
		$xml_rss->appendChild( $xml_channel );
		$xml->appendChild( $xml_rss );
		
		// Parse the XML.
		$xml->save(Yii::$app->params['feeddir'].$feedname.'.xml');			

	}
	
	
}
