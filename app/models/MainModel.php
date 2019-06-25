<?php
/**
 * Created by PhpStorm.
 * User: Adv
 * Date: 24.05.2018
 * Time: 16:36
 */

namespace app\models;

use \PDO;


class MainModel extends Model
{
    public $arrBoldVendors = array("ch", "ge", "lif", "hav", "fa", "dfm", "der", "dfc", "cng", "fot", "zot",);

    function fetchAll($sql, $arrArgs = 0)
    {
        $stmt = $this->db->prepare($sql);
        if ($arrArgs==0)
            $stmt->execute();
        else
            $stmt->execute($arrArgs);
        return $stmt->fetchAll();
    }

    function fetchRow($sql, $arrArgs = 0)
    {
        $stmt = $this->db->prepare($sql);
        if ($arrArgs==0)
            $stmt->execute();
        else
            $stmt->execute($arrArgs);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getVendors()
    {
        return $this->fetchAll("SELECT * FROM vendors ORDER BY vendorname");
    }

    public function getVendor($vendor)
    {
        return $this->fetchRow("SELECT * FROM vendors WHERE vendor=?", array($vendor));
    }

    public function getModel($modelid)
    {
        return $this->fetchRow("SELECT * FROM models WHERE id=?", array($modelid));
    }

    public function getClone($id)
    {
        return $this->fetchRow("SELECT * FROM clones WHERE id=?", array($id));
    }

    public function getCrash($id)
    {
        return $this->fetchRow("SELECT * FROM models_crash WHERE modelid=?", array($id));
    }

    public function getVideo($id)
    {
        return $this->fetchAll("SELECT * FROM videos WHERE modelid=?", array($id));
    }
/*
    public function getNews($modelid)
    {
        $sql =  "SELECT n.id, n.posted, n.subject, n.review, n.news_image
        FROM news n, news2models nd 
        WHERE  nd.modelid=? AND nd.newsid=n.id AND n.show_on=1";
        return $this->fetchAll($sql, array($modelid));
    }

    public function getNewsItem($newsid)
    {
        $sql = "SELECT * FROM news WHERE id=?";
        return $this->fetchRow($sql, array($newsid));
    }
*/
    public function getProps()
    {
        return $this->fetchAll("SELECT * from props2 ORDER BY id");
    }

    public function getPropValues($modelid, $modelid_import=0)
    {
        $sql = $modelid ? "SELECT * FROM propvalue2 WHERE modelid=?" : "SELECT * FROM propvalue2 WHERE modelid_import=?";
        return $this->fetchAll( $sql, array($modelid ? $modelid : $modelid_import) );
    }

    public function getDocChapters()
    {
        return $this->fetchAll("SELECT id, chaptername, parentid, level, orderno FROM doc_chapter ORDER BY orderno");
    }

    public function getDocs($modelid)
    {
        $sql =  "SELECT DISTINCT d.id, d.docname, d.doctype, d.url, d.chapter1, d.chapter2, d.chapter3
        FROM doc_item d, doc_doc2model 
        WHERE  doc_doc2model.modelid=? AND doc_doc2model.docid=d.id AND d.show_on=1 AND d.is_checking=0 ";
        return $this->fetchAll($sql, array($modelid));
    }

    public function getDoc($docid)
    {
        $sql = "SELECT * FROM doc_item WHERE id=?";
        return $this->fetchRow($sql, array($docid));
    }



    public function getModels($vendor, $type='', $fields='')
    {
        $vendor = substr($vendor, 0, 3);
        $szVendor = $vendor? " AND models.vendor='$vendor' " : "";

        if (!in_array($type, array('jeep', 'la', 'truck', 'bus', 'cross', 'ev')) )
            $type = '';

        $pc=$la=$tr=$cr=$bs=$mb=$jp=$bt=0;

        if ($type=='la')
            $la=1;
        if ($type=='jeep')
            $pc=$jp=1;
        if ($type=='bus')
            $bs=$mb=1;
        if ($type=='truck')
            $tr=$bt=1;
        if ($type=='cross')
            $cr=1;
        if ($type=='ev')
            $ev=1;

        if ($pc==0 && $la==0 && $tr==0 && $cr==0 && $bs==0 && $mb==0 && $jp==0 && $bt==0)
            $pc=$la=$tr=$cr=$bs=$mb=$jp=$bt=1;

        $szWhereBodyType = "";
        if ($pc)
            $szWhereBodyType = $this->AddOrClause($szWhereBodyType, "body='pickup'");
        if ($jp)
            $szWhereBodyType = $this->AddOrClause($szWhereBodyType, "body='jeep'");
        if ($la)
            $szWhereBodyType = $this->AddOrClause($szWhereBodyType, "body='la'");
        if ($bs)
            $szWhereBodyType = $this->AddOrClause($szWhereBodyType, "body='bus'");
        if ($mb)
            $szWhereBodyType = $this->AddOrClause($szWhereBodyType, "body='minibus'");
        if ($tr)
            $szWhereBodyType = $this->AddOrClause($szWhereBodyType, "body='truck'");
        if ($bt)
            $szWhereBodyType = $this->AddOrClause($szWhereBodyType, "body='bigtruck'");
        if ($cr)
            $szWhereBodyType = $this->AddOrClause($szWhereBodyType, "body='cross'");
        if ($szWhereBodyType)
            $szWhereBodyType = " AND ($szWhereBodyType) ";

        $szEngine = $ev ? " AND engine='E' " : "";

        if (!$fields)
            $fields = "models.*";

        $sql = "SELECT $fields FROM models, vendors  
                WHERE show_on>0 AND vendors.vendor=models.vendor $szWhereBodyType $szVendor  $szEngine 
                ORDER BY status4order ASC, body4order ASC, startprod DESC, vendors.vendorOrderNo DESC, models.orderNo  DESC";
        $rs = $this->db->query($sql);
        $arrModels = $rs->fetchAll();
        return $arrModels;
    }

    function findCar($vendor, $vendorname, $carname, $year, $oldPrefix )
    {
        $path = "/".$vendorname."/".$carname."/";
        if ($year)
            $path = $path.$year."/";
        if ($oldPrefix)
            $path = "/".$oldPrefix.$path;

        $Model = $this->fetchRow("SELECT id FROM models WHERE vendor=? AND individual_link=?", array($vendor, $path));

        return $Model ? $Model['id'] : 0;
    }



    function getYuanRate($eng=0)
    {
        return $eng ? "0.15" : "9.0";
        Global $cache2;
        if (!$cache2)
            $cache2 = new cache2();
        $r = $cache2->get("_cbrrate");
        return $r;
    }

    public function getVendorLink($Vendor, $type="")
    {
        $ENG = $this->app->config->lang;
        $vendor_link = $Vendor['individual_link'] ? $ENG.$Vendor['individual_link'] : $ENG."/modellist.php?vendor=".$Vendor['vendor'];

        if ($type=="sales")
            $vendor_link .= $Vendor['individual_link'] ? "?view=sales" : "&view=sales";

        return $vendor_link;
    }

    public function getModelLink($Model, $typelink="", $bFullLink=0)
    {
        $ENG = $this->app->config->lang;
        $url = "";

        if ($typelink=="doc")
            $url = "/docview.php?modelid=".$Model['id'];
        else
        if ($typelink=="parts")
            $url = "/parts/?modelid=".$Model['id'];
        else
        if ($typelink=="forum")
            $url = $Model['individual_forum'] ? $Model['individual_forum'] : "/bb/viewforum.php?f=" . $Model['bbforumid'];
        else
        if ($Model['individual_link'])
        {
            if ($typelink)
                $url = $typelink ? $ENG.$Model['individual_link']."?view=$typelink" : $ENG.$Model['individual_link'];
        }
        else
        {
            switch($typelink)
            {
                case "video":   $url = $ENG."/video.php?modelid=".$Model['id']; break;
                case "dealers": $url = "/dealers.php?modelid=".$Model['id'];    break;
                case "props":   $url = $ENG."/props.php?modelid=".$Model['id']; break;
                case "opinion": $url = "/opinion.php?modelid=".$Model['id'];    break;
                case "press":   $url = "/press.php?modelid=".$Model['id'];  break;
                case "photos":  $url = $ENG."/photos.php?modelid=".$Model['id']; break;
                case "news":    $url = $ENG."/model-news.php?modelid=".$Model['id']; break;
                case "crash":   $url = $ENG."/cncap/index.php?modelid=".$Model['id']; break;
                case "sales":   $url = $ENG."/model-sales.php?modelid=".$Model['id']; break;
                default:        $url = $ENG."/model-main.php?modelid=".$Model['id']; break;
            }
        }

        if ( !$bFullLink )
           return $url;
        else
            return "<a hreef='" . $url . "'>" . $Model['modelname'] . "</a>";
    }

    public function getModelName($Model)
    {
        return $this->app->config->lang ? $Model['modelname_eng'] : $Model['modelname'];
    }



    function AddOrClause($str, $clause)
    {
        return $str ? $str." OR ".$clause : $clause;
    }

    function removeBOM($str="") {
        if(substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
            $str = substr($str, 3);
        }
        return $str;
    }


}
