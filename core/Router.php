<?php

namespace core;
require_once __DIR__ . "/config.php";

use app\models\MainModel;


class Router
{
    public $config = 0;
    public $model = 0;

    var $arrRoutes = [
        "getcatalog" => [ "name"=>"getcatalog" ],
        "getdoc" => [ "name"=>"getdoc" ],
        "getrate" => [ "name"=>"getrate" ],
        "getnewsitem" => [ "name"=>"getnewsitem" ],
        "haval"=>["vendor"=>"hav", "oldPrefix"=>"great-wall"],
    ];

    var $carControllers = [
        "getcardata"=>0,
        "getprops"=>0,
        "getphotos"=>0,
        "getvideo"=>0,
        "getdocs"=>0,
        "getnews"=>0,
        "getsales"=>0,
        "photo"=>"car_photo",
        "crash"=>"car_crash"
    ];
    var $carDefController = "";

    var $vendorControllers = [
            "getcars"=>"vendorgetcars",
            "getnews"=>"vendorgetnews"
    ];
    var $vendorDefController = "";


    function run()
    {
        if (!$this->config)
            $this->config = new Config();
        if (!$this->model)
            $this->model = new MainModel($this);

        $url = parse_url( $_SERVER['REQUEST_URI'] );
        $arrParams = array();
        parse_str($url['query'], $arrParams);
        $routes = explode('/', $url['path']);

        $controller_name = "";
        $route1 = 1;
        $routeLast = count($routes)-1;

        if ($routes[$route1] == 'eng')
        {
            $route1 = 2;
            $this->config->lang = "/eng";
        }

        if ( empty($routes[$route1]) )
        {
            $controller_name = '';
        }
        else if (array_key_exists($routes[$route1], $this->arrRoutes))
        {
            $rt = $this->arrRoutes[ $routes[$route1] ];

            if ($rt['vendor'])
            {
                $arrParams['_vendor'] = $rt['vendor'];
                if (empty($routes[$route1+1]))
                    $controller_name = $this->vendorDefController;
                else if (array_key_exists($routes[$route1+1], $this->vendorControllers))
                    $controller_name = $this->vendorControllers[ $routes[$route1+1] ];
                else
                {
                    $carname = $routes[$route1+1];
                    $nextToken = empty($routes[$route1+2]) ? "" : $routes[$route1+2];
                    $year = 0;
                    if (intval($nextToken)>0)
                            $year = intval($nextToken);
                    $modelid = $this->model->findCar($rt['vendor'], $routes[$route1], $carname, $year, $rt['oldPrefix'] );
                    if ($modelid)
                    {
                        $arrParams['_modelid'] = $modelid;
                        if ($year)
                            $nextToken = empty($routes[$route1+3]) ? "" : $routes[$route1+3];
                        if (!$nextToken)
                            $controller_name = $this->carDefController;
                        else if (array_key_exists($nextToken, $this->carControllers))
                            $controller_name = $this->carControllers[ $nextToken ] ?: $nextToken;

                    }

                }


            }
            else if ($rt['name'])
            {
                $controller_name = $rt['name'];
                if ($rt['nextparam'])
                    $arrParams[ $rt['nextparam'] ] = $routes[$route1+1];
            }
        }

        if (!$controller_name)
        {
            $this->ErrorPage404();
        }

        $controller_name .= '_controller';
        //print "<br>path=".$url['path'];
        //print "<br>query=".$url['query'];
        //print "<br>routes[route1]=".$routes[$route1];

        $n = "\\app\controllers\\".$controller_name;
        $controller = new $n($this, $arrParams);
        $controller->run();
    }

    function ErrorPage404()
    {
        //print ;
        exit("<h1>404 error</h1>Page not found");
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:'.$host.'404');
    }
}


