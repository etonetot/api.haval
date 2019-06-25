<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 20.03.2019
 * Time: 21:35
 */

namespace app\controllers;

require_once __DIR__."/../views/lang.php";


class Getvideo_Controller extends Controller
{
    function run()
    {
        $modelid = $this->arrParams['_modelid'];
        //$Model = $this->model->getModel($modelid, "variants");

        //if (!$Model)
        //           print "Model not found";


        $arrVideo = $this->model->getVideo($modelid);
        for ($i=0; $i<count($arrVideo) ; $i++)
        {
            $item = $arrVideo[$i];
            $arr = explode("/", $item['link']);
            $link = $arr[count($arr)-1];
            if (!$link)
                $link = $arr[count($arr)-2];
            $arrVideo[$i]['link'] = $link;
        }

        header('Access-Control-Allow-Origin:*');
        print json_encode(
            $arrVideo,
            JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }





}

