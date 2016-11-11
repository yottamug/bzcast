<?php

namespace app\models;

use Yii;
use yii\base\Model;


require_once('/usr/share/php/getid3/getid3.php');


/**
 * LoginForm is the model behind the login form.
 */
class BzId3 extends Model
{
    public function GetMp3Info($filename)
    {
		$getID3 = new \getID3;
		$info = $getID3->analyze($filename);
		$data = array();
		$data['title'] = $info['tags']['id3v2']['title']['0'];
		$data['filename'] = $filename;
		$data['category'] = $info['tags']['id3v2']['album']['0'];
		$data['author'] = $info['tags']['id3v2']['artist']['0'];
		$data['pubDate'] = ( isset($info['tags']['id3v2']['title']['0']) ) ? BzId3::getPubDateFromTitle($data['title'], $filename) : BzId3::getPubDateFromFile($filename) ;		
		$data['duration'] = $info['playtime_string'];
		$data['size'] = $info['filesize'];
		$data['bitrate'] = $info['mpeg']['audio']['bitrate'];
		$data['frequency'] = $info['mpeg']['audio']['sample_rate'];
		return $data;
    }

	public function getPubDateFromTitle($title, $filename)
	{
		// echo $title;
		$pattern = '/(\w+)_([0-9]{8})(\w+){0,1}/';
		$ret = preg_match($pattern, $title, $matches);
		if ($ret) 
		{
			return $matches['2'];
		}
		return BzId3::getPubDateFromFile($filename);
	}
	
	public function getPubDateFromFile($filename)
	{
		return date("Ymd", filemtime($filename));
	}
}
