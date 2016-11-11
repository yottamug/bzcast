<?php

namespace app\models;

use Yii;
use yii\base\Model;

class FeedXML extends Model
{
	public $filename;
	public $mp3files;
	public $title;
	public $subtitle;
	public $summary;
	public $description;
	
    public function generateFeed()
    {
		// initialize object variables
		$feed_base_downloadurl	= Yii::$app->params['feed_base_downloadurl'];
		$feed_image_url			= Yii::$app->params['feed_image_url'];
		$feed_base_url			= Yii::$app->params['feed_base_url'];
		$feed_base_dir			= Yii::$app->params['feed_base_dir'];
		$feed_author			= Yii::$app->params['feed_author'];
		$feed_name				= Yii::$app->params['feed_name'];
		$feed_email				= Yii::$app->params['feed_email'];
		$feed_explicit			= Yii::$app->params['feed_explicit'];
		
		$feed_podcast_url		= Yii::$app->params['podcast_url'];
		
		// initialize variables
		$xmlns_atom 		= "http://www.w3.org/2005/Atom";
		$xmlns_itunes 		= "http://www.itunes.com/dtds/podcast-1.0.dtd";
		$xml_language		= "en";
		$version			= "2.0";
		$copyright			= '&#xA9;'.date("Y");
		
		$xml = new \DOMDocument( "1.0", "utf-8" );
		$xml->formatOutput = true;
		
		$xml_rss = $xml->createElement( "rss" );
		$xml_rss->setAttribute( "xmlns:atom", 		$xmlns_atom 	);
		$xml_rss->setAttribute( "xmlns:itunes", 	$xmlns_itunes 	);
		$xml_rss->setAttribute( "xml:lang", 		$xml_language 	);
		$xml_rss->setAttribute( "version",			$version		);
		
		$xml_channel = $xml->createElement( "channel" );
		
		$xml_link 				= $xml->createElement( "link", 				$feed_podcast_url );
		$xml_copyright 			= $xml->createElement( "copyright", 		$copyright );
		$xml_webMaster 			= $xml->createElement( "webMaster", 		$feed_author );
		$xml_managingEditor 	= $xml->createElement( "managingEditor",	$feed_author );
		$xml_itunes_explicit	= $xml->createElement( "itunes:explicit",	$feed_explicit );
		$xml_itunes_author 		= $xml->createElement( "itunes:author", 	$feed_name );
		
		$xml_itunes_summary		= $xml->createElement( "itunes:summary",	$this->summary );
		$xml_itunes_subtitle	= $xml->createElement( "itunes:subtitle",	$this->subtitle );		
		$xml_title 				= $xml->createElement( "title", 			$this->title );
		$xml_description 		= $xml->createElement( "description", 		$this->description );
		$xml_lastBuildDate 		= $xml->createElement( "lastBuildDate", 	date(\DateTime::RFC1123) );

		// image group
		$xml_image = $xml->createElement( "image" );
		$xml_image_url 			= $xml->createElement( "url",				$feed_image_url );
		$xml_image_title 		= $xml->createElement( "title",				$this->title );
		$xml_image_link 		= $xml->createElement( "link",				$feed_image_url );
		$xml_image->appendChild( $xml_image_url );
		$xml_image->appendChild( $xml_image_title );
		$xml_image->appendChild( $xml_image_link );
		
		// itunes owner group
		$xml_itunes_owner 		= $xml->createElement( "itunes:owner" );
		$xml_itunes_name		= $xml->createElement( "itunes:name",		$feed_name );
		$xml_itunes_email		= $xml->createElement( "itunes:email",		$feed_email );
		$xml_itunes_owner->appendChild( $xml_itunes_name );
		$xml_itunes_owner->appendChild( $xml_itunes_email );
		
		// itunes category group
		$xml_itunes_category	= $xml->createElement( "itunes:category");
		$xml_itunes_category->setAttribute( "text", 'Arts' );
		
		// itunes image group
		$xml_itunes_image		= $xml->createElement( "itunes:image" );
		$xml_itunes_image->setAttribute( "href", $feed_image_url );

		// atom link group
		$xml_atom_link			= $xml->createElement( "atom:link" );
		$xml_atom_link->setAttribute( "href", $feed_base_url.$this->filename.'.xml' );
		$xml_atom_link->setAttribute( "rel", "self" );
		$xml_atom_link->setAttribute( "type", "application/rss+xml" );
		
		
		$xml_channel->appendChild( $xml_link );
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
		$xml_channel->appendChild( $xml_title );
		$xml_channel->appendChild( $xml_itunes_author );
		$xml_channel->appendChild( $xml_description );
		$xml_channel->appendChild( $xml_lastBuildDate );
		
		foreach ($this->mp3files as $row)
		{
			$xml_item 				= $xml->createElement( "item" );
			
			$xml_item_enclosure = $xml->createElement( "enclosure" );
			$xml_item_enclosure->setAttribute( "url", 		$feed_base_downloadurl.$row->id."/".$row->id.".mp3") ;
			$xml_item_enclosure->setAttribute( "type", 		"audio/mpeg" );
			$xml_item_enclosure->setAttribute( "length", 	$row->size );
			
			$xml_item_title 			= $xml->createElement( "title", 			$row->title );
			$xml_item_guid 				= $xml->createElement( "guid", 				$feed_base_downloadurl.$row->id."/".$row->id.".mp3") ;
			$xml_item_link 				= $xml->createElement( "link", 				$feed_base_downloadurl.$row->id."/".$row->id.".mp3") ;	
			$xml_item_itunes_subtitle	= $xml->createElement( "itunes:subtitle", 	$row->author );
			$xml_item_description		= $xml->createElement( "description", 		$row->author );
			$xml_item_itunes_duration	= $xml->createElement( "itunes:duration", 	$row->duration );
			$xml_item_author			= $xml->createElement( "author", 			$feed_name );
			$xml_item_itunes_author		= $xml->createElement( "itunes:author", 	$feed_name );
			$xml_item_itunes_explicit	= $xml->createElement( "itunes:explicit", 	$feed_explicit );
			$xml_item_pubDate			= $xml->createElement( "pubDate", 			date('D, d M Y H:i:s +0800', strtotime($row->pubDate) ) );
							
			$xml_item->appendChild( $xml_item_title );
			$xml_item->appendChild( $xml_item_enclosure );
			$xml_item->appendChild( $xml_item_guid );
			$xml_item->appendChild( $xml_item_link );
			$xml_item->appendChild( $xml_item_itunes_subtitle );
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
		$xml->save($feed_base_dir.$this->filename.'.xml');
    }

}
