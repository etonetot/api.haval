<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 19.04.2019
 * Time: 17:40
 */
namespace app\controllers;

require_once __DIR__."/../views/lang.php";

class Vendorgetcars_Controller extends Controller
{
    function run()
    {
        $vendor = $this->arrParams['_vendor'];
        //$Vendor = $this->model->getVendor($vendor);


        $arrModels = $this->model->getModels($vendor, '', "models.id, models.price1, models.status, models.photodir, models.individual_link as href, 
                                    models.modelname");
        foreach ($arrModels as $Model)
        {

            //$vars["Models"][] = $modelData;
        }



        header('Access-Control-Allow-Origin:*');
        print json_encode(
            $arrModels,
            JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }
}