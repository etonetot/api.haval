<?php
namespace app\models;

use \PDO;

include BASE_DIR."/dbconfig.php";


class Model
{
    public $db;
    public $app;
    public $controller;

    public function __construct($app, $controller=0)
    {
        $this->controller = $controller;
        $this->app = $app;

        $this->connectDB();
    }

    public function connectDB()
    {
        $charset = 'utf8';

        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->db = new PDO($dsn, DB_USER, DB_PASSWORD, $opt);

        if (!$this->db)
            die("Could not connect to database");

        //$this->db->exec('SET NAMES CP1251');
    }





}