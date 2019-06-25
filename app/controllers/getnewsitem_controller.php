<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 24.05.2018
 * Time: 14:09
 */


namespace app\controllers;

use app\views\PageView;
require_once __DIR__."/../views/lang.php";


class Getnewsitem_Controller extends Controller
{
    function __construct($app, $arrParams)
    {
        parent::__construct($app, $arrParams);
        $this->model = $app->model;
    }

    function run()
    {
        $docid = $this->getParamInt('newsid');
        $sql = "SELECT * FROM news WHERE id=?";
        $arr = $this->model->fetchRow($sql, array($docid));

        //$arr = $this->model->getNewsItem($docid);

        header('Access-Control-Allow-Origin:*');
        print json_encode ($arr, JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }



}
