<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 20.03.2019
 * Time: 21:35
 */

namespace app\controllers;

require_once __DIR__."/../views/lang.php";


class Getdocs_Controller extends Controller
{
    function run()
    {
        $modelid = $this->arrParams['_modelid'];

        $arrChapters = $this->model->getDocChapters();
        $arrDocs = $this->model->getDocs($modelid);

        header('Access-Control-Allow-Origin:*');
        print json_encode(
            array("chapters"=>$arrChapters, "rows"=>$arrDocs),
            JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }





}

