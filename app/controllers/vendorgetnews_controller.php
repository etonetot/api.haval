<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 20.03.2019
 * Time: 21:35
 */

namespace app\controllers;

require_once __DIR__."/../views/lang.php";


class Vendorgetnews_Controller extends Controller
{
    function run()
    {
        $vendor = $this->arrParams['_vendor'];
        $sql =  "SELECT n.id, n.posted, n.subject, n.review, n.news_image
            FROM news n  
            WHERE n.vendor=?  AND n.show_on=1
            ORDER BY n.posted DESC";

        $arrDocs = $this->model->fetchAll($sql, array($vendor));
        //$arrDocs = $this->model->getNews($modelid);

        header('Access-Control-Allow-Origin:*');
        print json_encode(
            $arrDocs,
            JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }





}

