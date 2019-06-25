<?php

namespace app\controllers;


class Controller {
	
	public $model;
    public $view;
    public $app;
    public $arrParams;

	function __construct($app, $arrParams)
	{
	    $this->app = $app;
        $this->model = $app->model;
        $this->arrParams = $arrParams;
	}

    function getParamStr($name)
    {
        $str = array_key_exists($name, $this->arrParams) ? $this->arrParams[$name] : "";
        return preg_replace('/[^\wА-Яа-я]/i', '', $str);
    }

    function getParamInt($name)
    {
        return array_key_exists($name, $this->arrParams) ? IntVal($this->arrParams[$name]) : 0;
    }

    function getConfig()
    {
        return $this->app->config;
    }

    function isEng()
    {
        return $this->app->config->lang == "/eng";
    }

    function run()
	{
	    echo "Controller->run";
	}
}
