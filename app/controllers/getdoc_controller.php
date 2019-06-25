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


class Getdoc_Controller extends Controller
{
    function __construct($app, $arrParams)
    {
        parent::__construct($app, $arrParams);
        $this->model = $app->model;
    }

    function run()
    {
        $docid = $this->getParamInt('docid');
        $arr = $this->model->getDoc($docid);

        header('Access-Control-Allow-Origin:*');
        print json_encode ($arr, JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }



}
