# bzcast
Podcast Feed Generator Using Yii2 Framework

REQUIREMENTS
------------
1) [Yii 2](http://www.yiiframework.com/)

2) [getID3()](http://getid3.sourceforge.net/)



### config
Edit the file `config/web.php` and enable urlManager

```php
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
				'download/<id:\d+>/<title>' => 'podcast/download',
            ],
        ],
```

update default feed
/var/www/html/bzcast/yii podcast/update 

update database record
/var/www/html/bzcast/yii podcast/updatedb 

update category feed (only read from database)
/var/www/html/bzcast/yii podcast/feed feedname_defined_in_db
