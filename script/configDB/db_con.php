<?php
class DB {
    
    private $showDATA;

    public function __construct($host,$dbname,$username,$password) {
        $DBConnection = new PDO("pgsql:host=$host dbname=$dbname user=$username password=$password");
        $DBConnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //$DBConnection = null;
        $this->showDATA = $DBConnection ;
            
    }
    public function query($show) {
        $p = $this->showDATA->prepare($show);
       // $p->execute();
        if( $p->execute()){
        //$DATA=$p->fetchAll();
        //$DATA=$p->fetchAll(PDO::FETCH_ASSOC);
        return $p;
        }
    }
}
$Ho = 'localhost';
$Da = 'fusionpbx';
$Us = 'fusionpbx';
$Pa = '86A2WSO04wmb4fsuMbzix3RE1Q';
$HTTP_HOST = "192.168.0.37";//$_SERVER[HTTP_HOST];



?>