<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 17.03.2019
 * Time: 18:00
 */

namespace app\controllers;

require_once __DIR__."/../views/lang.php";


class Getsales_Controller extends Controller
{
    function run()
    {
        $modelid = $this->arrParams['_modelid'];
        //$country = $this->arrParams['country'];
        //$Model = $this->model->getModel($modelid, "variants");

        //if (!$country)
        $country = 'r';
        $sql = "SELECT year, mon, summ, sales, rank FROM models_sales WHERE modelid=? AND country=? AND summ=1 ORDER BY year ASC, mon ASC";
        $arrRus = $this->model->fetchAll($sql, array($modelid, $country));
        $country = 'c';
        $arrChina = $this->model->fetchAll($sql, array($modelid, $country));

        header('Access-Control-Allow-Origin:*');
        print json_encode(
            ["rus"=>$arrRus,"china"=>$arrChina],
            JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }




}

