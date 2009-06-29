<?php	
	$dbConn=new COM ("ADODB.Connection") or die("创建COM失败");
    //$ADO="Provider=sqloledb;Data Source=localhost;Initial Catalog=myTest;User Id=sa;Password=sa;";
    $ADO="DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=" . realpath("data.mdb"); 
    $dbConn->open($ADO);
	$rs=new COM("ADODB.RecordSet") or die("创建RS失败");
    $sql="SELECT * FROM user";
    $rs->open($sql,$dbConn,1,1);    
    while(!$rs->eof){
       
	    echo $rs->fields["username"]->value;
        echo"<BR>";
        $rs->movenext();
    }
    $rs->Close;
    $rs=null;
    $dbConn->Close;
    $dbConn=null;	
?>
