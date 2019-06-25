<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 04.06.2019
 * Time: 19:55
 */

namespace app\controllers;

require_once __DIR__."/../views/lang.php";


class Getcardata_Controller extends Controller
{
    function run()
    {
        $modelid = $this->arrParams['_modelid'];
        $Model = $this->model->getModel($modelid);

        header('Access-Control-Allow-Origin:*');
        print json_encode(
            $Model,
            JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }





}

