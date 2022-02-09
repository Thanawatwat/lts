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
        $p->execute();
        if($show ){
        //$DATA=$p->fetchAll();
        $DATA=$p->fetchAll(PDO::FETCH_ASSOC);
        return $DATA;
        }
        
    
        
    }
}
$Ho = 'localhost';
$Da = 'fusionpbx';
$Us = 'fusionpbx';
$Pa = 'LYWK99PvC5Zb7Y9EakixxzGAQSA';
$HTTP_HOST = "cns6.cans.cc";//$_SERVER[HTTP_HOST];
?> 
