<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\Mp3file;


class PodcastController extends Controller
{
    public function actionDownload($id)
    {
		$mp3file = Mp3file::findOne($id);		
		// Yii::$app->response->setDownloadHeaders($mp3file->filename);	
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
		if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") == false) {
			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
		} else {
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public");
		}
		header("Expires: Sat, 26 Jul 1979 05:00:00 GMT");
		header("Content-Encoding: UTF-8");
		header("Content-Type: audio/mpeg; charset=utf-8");
		header("Content-Disposition: attachment; filename=\"".$mp3file->id.".mp3\"");
		header("Cache-Control: max-age=0");		
		
		Yii::$app->response->stream = fopen($mp3file->filename, 'r');
		
		return yii::$app->response;
    }
}
