<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 16.03.2019
 * Time: 23:58
 */

namespace app\controllers;

require_once __DIR__."/../views/lang.php";



class Getphotos_Controller extends Controller
{
    var $strInterior = "Интерьер";
    var $strInteriorPhoto = "Фото интерьера";
    var $strInfochart = "Инфографика";
    var $strChassis = "Подвеска и двигатель";
    var $strGirlsAnd = "Девушки и";
    var $strGirls = "Девушки";
    var $strPhotografs = "Фотографии";
    var $strPhoto = "Фото";
    var $strAllPhotosOf = "Все фотографии";
    var $strMorePhotos = "Еще фотографии";
    var $strMoreGirlsPhotos = "А еще у нас есть фото девушек с";
    var $titlefile = "title.htm";
    var $strProdYear = " г/в";

    var $photodir = "";
    var $rootpath = "";
    var $szAltText = "";

    var $arrInfoChart = array(
        "luggage"=>array("Размеры багажника"),
        "luggage1"=>array(""),
        "luggage2"=>array(""),
        "angles"=>array("Углы въезда и съезда"),
        "body"=>array("Геометрические параметры кузова"),
        "salon"=>array("Размеры салона"),
    );

    function __construct($app, $arrParams)
    {
        parent::__construct($app, $arrParams);
        $this->model = $app->model;

        if ($this->isEng())
            $this->SwitchToEng();
    }

    function run()
    {
        $modelid = $this->getParamInt('_modelid');  // 0- простые, 1- коммерч, 2 - ВСЕ

        $arrRet = array();
        $Model = $this->model->getModel($modelid, "photodir");

        $this->photodir = "/".$Model['photodir'];
        $this->rootpath = BASE_DIR."/".$Model['photodir'];
        $this->szAltText = $this->model->getModelName($Model);

        $files = $this->ScanFolder($this->rootpath."/full/");
        $arrRet[] = array(
                'title'=>$this->strAllPhotosOf." ".$this->szAltText,
                'folder'=>0,
                'path'=>$this->photodir."/full/",
                'files'=>$files);

        $this->GetFolderList($arrRet);

        header('Access-Control-Allow-Origin:*');
        print json_encode(
            array("title"=>$this->szAltText, photodir=>$Model['photodir'], "photos"=>$arrRet),
            JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }



    function SwitchToEng()
    {
        $this->strInterior = "Interior";
        $this->strInteriorPhoto = "Interior photos of";
        $this->strInfochart = "Infochart";
        $this->strChassis = "Chassis and engine";
        $this->strGirlsAnd = "Girls and";
        $this->strGirls = "Girls";
        $this->strPhotografs = "Photos of";
        $this->strPhoto = "Photo";
        $this->strPhotoPrev = "Prev photo";
        $this->strPhotoNext = "Next photo";
        $this->strAllPhotosOf = "All photos of";
        $this->strMorePhotos = "More photos";
        $this->strMoreGirlsPhotos = "More girls and ";
        $this->titlefile = "title_eng.htm";
        $this->strProdYear = "";
        $this->arrInfoChart["luggage"] = array("Trunk size");
        $this->arrInfoChart["angles"] = array("Angles");
        $this->arrInfoChart["body"] = array("Body size");
        $this->arrInfoChart["salon"] = array("Interior szie");
    }

    function GetFolderList(&$arrRet)
    {
        $ext = 1;
        $extdir = $this->rootpath."/folder".$ext;
        while (is_dir($extdir))
        {
            if (is_file($extdir."/interior"))
            {
                $interior = file_get_contents($extdir."/interior");
                $year = IntVal($interior) ? $interior.$this->strProdYear : "";
                $szCaption = "$this->strInteriorPhoto $this->szAltText $year";
            }
            else
            if (is_file($extdir."/old") && $this->szAltText)
            {
                $old = file_get_contents($extdir."/old");
                $year = $old ? $old.$this->strProdYear : "";
                $szCaption = "$this->szAltText $year";
            }
            else
            if (is_file($extdir."/infochart") && $this->szAltText)
                $szCaption = $this->strInfochart;
            else
            if (is_file($extdir."/girls") && $this->szAltText)
                $szCaption = "$this->strGirlsAnd $this->szAltText";
            else
            if (is_file($extdir."/chassis") && $this->szAltText)
                $szCaption = "$this->szAltText: $this->strChassis";
            else
            if (file_exists($extdir."/".$this->titlefile))
                $szCaption = file_get_contents($extdir."/".$this->titlefile);
            else
                $szCaption = "Другие фотографии";

            if ($szCaption)
            {
                $files = $this->ScanFolder($this->rootpath."/folder".$ext."/full/");
                $arrRet[] = array('title'=>$szCaption,
                                  'folder'=>$ext,
                                  'path'=>$this->photodir."/folder".$ext."/full/",
                                  'files'=>$files);
            }
            $ext++;
            $extdir = $this->rootpath."/folder".$ext;
        }
    }

    function ScanFolder($pattern)
    {
        $arrRet = array();
        if (!is_dir($pattern))
            return $arrRet;
        $dh = opendir($pattern);
        if ($dh)
        {
           while (($imageName = readdir($dh)) !== false)
           {
              if (is_file($pattern.$imageName))
                 $arrRet[] = $imageName;
           }
        }
        closedir($dh);
        sort($arrRet);
        return $arrRet;
    }


}
