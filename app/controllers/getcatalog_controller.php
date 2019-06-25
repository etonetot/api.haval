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


class Getcatalog_Controller extends Controller
{
    function __construct($app, $arrParams)
    {
        parent::__construct($app, $arrParams);
        $this->model = $app->model;
    }

    function run()
    {
        $comTrans = $this->getParamInt('comTrans');  // 0- простые, 1- коммерч, 2 - ВСЕ
        $rus = $this->getParamInt('rus');
        $mode = $this->getParamStr('mode');

        $whereStatusRus = $rus ? " AND (status='rus' OR status='rusplan' OR status='rusold')" : "";

        $whereStatusMode = "";
        switch ($mode)
        {
            case "opinion":	$whereStatusMode = " AND opinion_count>0 "; break;
            case "video":   $whereStatusMode = " AND video_count>0 "; break;
            case "press":	$whereStatusMode = " AND press_count>0 "; break;
            case "doc":	$whereStatusMode = " AND doc_count>0 "; break;
            case "photos":  break;
            default: $mode=""; break;
        }

        $arrRet = array();
        $arrTitles = array();
        if ($comTrans==0 || $comTrans==2)
            $arrTitles = array_merge($arrTitles, array('cross'=>_T("Кроссоверы"), 'la'=>_T("Легковые"), 'pickup'=>_T("Пикапы"), 'minibus'=>_T("Микроавтобусы")));
        if ($comTrans==1 || $comTrans==2)
            $arrTitles = array_merge($arrTitles, array('truck'=>_T("Грузовики до 7т"), 'bigtruck'=>_T("Тяжелые грузовики"), 'bus'=>_T("Автобусы") ));

        $arrVendors = $this->model->getVendors();

        $letter = chr(ord("A")-1);
        foreach ($arrVendors as $Vendor)
        {
            $vendor = $Vendor['vendor'];

            $arrModelsAll = $this->model->getModels("vendor='$vendor' AND show_on=1 $whereStatusRus $whereStatusMode", "status, orderNo DESC");
            if (count($arrModelsAll)==0)
                continue;
            print $vendor;
            $arrModels = array();
            if ($comTrans==0 || $comTrans==2)
                $arrModels = array_merge($arrModels, array('cross'=>array(), 'la'=>array(), 'pickup'=>array(), 'minibus'=>array()) );
            if ($comTrans==1 || $comTrans==2)
                $arrModels = array_merge($arrModels, array('truck'=>array(), 'bigtruck'=>array(), 'bus'=>array() ));

            $vendorname = in_array($Vendor['vendor'], $this->model->arrBoldVendors) ? "<b>".$Vendor['vendorname']."</b>" : $Vendor['vendorname'];
            $vendor_link = $this->model->GetVendorLink($Vendor);
            $logo = $Vendor['urllogo'];

            foreach ($arrModelsAll as $Model)
            {
                $body = $Model['body'];
                if ($body=='jeep')
                    $body='cross';

                if (array_key_exists($body, $arrModels))
                    array_push($arrModels[$body], $Model);
            }

            $nNotEmpty = 0;
            foreach ($arrModels as $body => $arr)
                if (Count($arr))
                    $nNotEmpty++;

            if ($nNotEmpty==0)
                continue;

            $arrVendorsUsed[] = $Vendor;
            $l = substr( $Vendor['vendorname'], 0 ,1 );
            $locallink = "";
            if ($l!=$letter)
            {
                $letter = $l;
                $locallink = "Lett$letter";
            }
            $locallinkVendor = "Lett".$Vendor['vendorname'];

            $arrBodies = array();

            foreach ($arrModels as $body => $arr)
            {
                if (Count($arr)==0)
                    continue;
                $arrColumns = array();
                if ((Count($arr)>10 && $nNotEmpty<3) || Count($arr)>30 )
                {
                    $half = IntVal(Count($arr)/2);
                    $arrColumns[] = $this->PrintListHelper($arr, 0, $half, $mode);
                    $arrColumns[] = $this->PrintListHelper($arr, $half, Count($arr), $mode);
                }
                else
                    $arrColumns[] = $this->PrintListHelper($arr, 0, Count($arr), $mode);

                $arrBodies[] = array("title" => $arrTitles[$body], "columns" => $arrColumns );
            }

            $arrRet[] = array(  "vendorlink"=>$vendor_link,
                "logo"=>"/photo/-logos/$logo",
                "vendorname" => $vendorname,
                "locallink" => $locallink,
                "locallinkVendor" => $locallinkVendor,
                "bodies" => $arrBodies,
            );

            if ($i++==20)
                break;
        }

        $arrLetters = $this->PrintLetterNavigator($arrVendorsUsed, $comTrans==1 ? 0 : 1 );


        print json_encode (array("vendors"=>$arrRet, "letters"=>$arrLetters), JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }



    function PrintListHelper($arr, $start, $end, $mode)
    {
        $arrRet = array();
        for($i=$start ; $i<$end ; $i++ )
        {
            $Model = $arr[$i];
            $arrRet[] = array(
                    "modelname" => $Model['modelname'],
                    "modellink" => $this->model->GetModelLink( $Model, $mode ),
                        );
        }
        return $arrRet;
    }


    function PrintLetterNavigator($arrVendors, $bAddVendorLinks)
    {
        $arrLetters = array();
        $letter = chr(ord("A")-1);
        for ($i=0 ; $i<Count($arrVendors); $i++ )
        {
            $Vendor = $arrVendors[$i];
            $l = substr( $Vendor['vendorname'], 0 ,1 );
            if ($l==$letter)
                continue;
            while ($l!=$letter && ( ord($letter)<(ord("Z")+1) ) )
                $letter = chr(ord($letter)+1);
            $arrLetterVendors = array();

            if ($bAddVendorLinks)
            {
                if ($letter=='C')
                {
                    $arrLetterVendors[] = ["name"=>"hangan", "class"=>"link2", "link"=>"Changan" ];
                    $arrLetterVendors[] = ["name"=>"hery", "class"=>"link2", "link"=>"Chery" ];
                }
                if ($letter=='H')
                    $arrLetterVendors[] = ["name"=>"aval", "class"=>"link1", "link"=>"Haval" ];
                if ($letter=='D')
                {
                    $arrLetterVendors[] = ["name"=>"ongFeng", "class"=>"link2", "link"=>"DongFeng" ];
                    $arrLetterVendors[] = ["name"=>"W", "class"=>"link2", "link"=>"Derways" ];
                }
                if ($letter=='L')
                    $arrLetterVendors[] = ["name"=>"ifan", "class"=>"link1", "link"=>"Lifan" ];
                if ($letter=='G')
                    $arrLetterVendors[] = ["name"=>"eely", "class"=>"link1", "link"=>"Geely" ];
                if ($letter=='Z')
                    $arrLetterVendors[] = ["name"=>"otye", "class"=>"link1", "link"=>"Zotye" ];
                if ($letter=='F')
                    $arrLetterVendors[] = ["name"=>"aw", "class"=>"link1", "link"=>"Faw" ];
            }

            $arrLetters[] = array("name"=>$letter, "vendors"=>$arrLetterVendors);
        }
        return $arrLetters;
    }

















}
