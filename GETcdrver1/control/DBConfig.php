<?php
class DB {
    
    private $showDATA;

    public function __construct($host,$dbname,$username,$password) {
        $DBConnection = new PDO("pgsql:host=$host dbname=$dbname user=$username password=$password");
        $DBConnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //$DBConnection = null;
        if ( $DBConnection) {
        
        echo 'Connection attempt succeeded.';
        
        } else {
        
        echo 'Connection attempt failed.';
        
        }
        $this->showDATA = $DBConnection ;
            /*
            $DBConnection = new PDO('mysql:host='.$host.'; dbname='.$dbname.';',$username,$password);
            $DBConnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
           // $DBConnection->setAttribute(PDO::ATTR_EMLATE_PREPARES,false);
            $this->showDATA =$DBConnection;
            */
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
$CONTACT_AUTH_USER = 'cdr';
$CONTACT_AUTH_PASS = 'AIzaSyC2ZpuUWO0QjkJXYpIXmxROuIdWPhY9Ub0';
?> 