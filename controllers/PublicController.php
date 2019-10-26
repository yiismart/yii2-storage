<?php

namespace smart\storage\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Storage caching controller
 */
class PublicController extends Controller
{
    /**
     * Cache contents of stored file and write it to response
     * @param string $name stored file identifier
     * @param boolean $d force download
     * @return void
     */
    public function actionIndex($name, $d = 0)
    {
        $storage = Yii::$app->storage;

        $name = $storage->prefix . $storage->publicPath . '/' . $name;

        $contents = $storage->cache($name);

        if ($contents === false) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = @finfo_file($finfo, Yii::getAlias('@webroot') . $name);
        finfo_close($finfo);

        if ($d == 1) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . strlen($contents));
        } else {
            if ($mime !== false) {
                header('Content-Type: ' . $mime);
            }
        }

        echo $contents;

        die();
    }
}
