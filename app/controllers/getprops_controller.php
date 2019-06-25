<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 17.03.2019
 * Time: 18:00
 */

namespace app\controllers;

require_once __DIR__."/../views/lang.php";


class Getprops_Controller extends Controller
{
    function run()
    {
        $modelid = $this->arrParams['_modelid'];
        $Model = $this->model->getModel($modelid, "variants");
        if (!$Model)
        {
            print "Model not found";
        }
        $arrVariants = explode(",", $Model['variants']);
        $var_count = Count($arrVariants);

        $arrValues = $arrRet = array();
        $propidPrice = $propidActual = $row = $curgroup = $lastgroup = 0;

        $arrProps = $this->model->getProps();
        $rate = $this->model->getYuanRate($this->isENG());

        $arr = $this->model->getPropValues($modelid);
        for ($i=0 ; $i<count($arr) ; $i++)
        {
            $Line = $arr[$i];
            $variantid = IntVal($Line['variantid']);
            $propid = IntVal($Line['propid']);
            $arrValues[$propid][$variantid] = $this->isEng() ? $Line['propvalue_eng'] : $Line['propvalue'];
        }

        foreach( $arrProps as $Prop )
        {
            if ( $Prop['propcode'] == "price" )
                $propidPrice = $Prop['id'];
            if ( $Prop['propcode'] == "ACTUAL" )
                $propidActual = $Prop['id'];
        }

        foreach( $arrProps as $Prop )
        {
            if (!$Prop['show_on'])
                continue;
            $row++;
            $propid = $Prop['id'];
            $szClass = $Prop['isHilighted'] ? "hilight".$Prop['isHilighted'] : "";
            $mark = "";

            if ($Prop['isGroup'] > 0)
            {
                if ($curgroup)
                    $arrRet[] = array( text=>'', 'trname'=>'tr$curgroup', 'class'=>'scrollrow', 'tdclass'=>'group', 'colspan'=>$var_count);
            }

            if ($Prop['isGroup']==-1 || $Prop['isGroup']==1)
                $curgroup = 0;

            if ($Prop['isGroup'] > 1)
            {
                $curgroup = ++$lastgroup;                                                //&#9658;
                $mark = "<div class=opencloserow curgroup='$curgroup' onclick='ToggleGroup(this, $curgroup)'>&#9660;</div>";
            }

            if ( $Prop['isGroup']<=0 && (!array_key_exists($propid, $arrValues) || $this->CheckEmpty($arrValues[$propid], $arrVariants)) )
                continue;

            $trclass = $curgroup && $Prop['isGroup']==0 ? " name='trYear$curgroup' " : "";
            $ret .= "<tr $trclass>";
            $propname = $ENG ? $Prop['propname_eng'] : $Prop['propname'];
            //$ret .= "<th class='fixed-side $szClass'>".$mark.$propname."</th>";

            if ($Prop['isGroup']==1 || $Prop['isGroup']==3)
            {
                //$ret .= "<td class='$szClass' colspan='$var_count'></td></tr>\n";
                $arrRet[] = ["text"=>$propname, "group"=>1, "class"=>$szClass];
                continue;
            }
            $arrRow = ["text"=>$propname, "class"=>$szClass];

            for ($i=0 ; $i<count($arrVariants) ; $i++)
            {
                $variantid = $arrVariants[$i];
                $value = @$arrValues[$propid][$variantid];
                $value = str_replace("+", "&#10004;", @$arrValues[$propid][$variantid]);
                $value = str_replace("/", " / ", $value);

                if ($Prop['propcode']=="price" && $propidActual>0 )
                {
                    if ( @$arrValues[$propidActual][$variantid]==-1)
                        $value = "<span class=notActual>".$value."</span>";
                }

                if ($Prop['propcode']=="price_rub" && $propidPrice)
                {
                    $price = $arrValues[$propidPrice][$variantid];
                    if ($ENG)
                        $value = number_format($price*1000*$rate/1000, 2, '.', '.')."0";
                    else
                        $value = number_format($price*1000*$rate/1000, 0, '', '.').".000";
                }

                //$ret .= "<td".($szClass ? " class='$szClass'" : "").">".$value."</td>";
                $arrRow["text$i"] = $value;
            }
            //$ret .= "</tr>\n";
            $arrRet[] = $arrRow;
        }

        header('Access-Control-Allow-Origin:*');
        print json_encode(
            array("varcount"=>$var_count, "rows"=>$arrRet),
            JSON_PARTIAL_OUTPUT_ON_ERROR  );
    }


    function CheckEmpty($arr, $arrVariants)
    {
        //	return false;
        $empty = true;
        for ($i=0 ; $i<count($arrVariants) ; $i++)
        {
            $variantid = $arrVariants[$i];
            if (!array_key_exists($variantid, $arr))
                continue;
            if ( $arr[$variantid] && $arr[$variantid]!='-' && $arr[$variantid]!='-/-' && $arr[$variantid]!='/'
                && $arr[$variantid]!='---/' && $arr[$variantid]!='--/--/--' && $arr[$variantid]!='---')
                $empty = false;
        }
        return $empty;
    }


}

