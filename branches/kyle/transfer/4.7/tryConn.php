<?php	
	$dbConn=new COM ("ADODB.Connection") or die("����COMʧ��");
    //$ADO="Provider=sqloledb;Data Source=localhost;Initial Catalog=myTest;User Id=sa;Password=sa;";
    $ADO="DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=" . realpath("data.mdb"); 
    $dbConn->open($ADO);
	$rs=new COM("ADODB.RecordSet") or die("����RSʧ��");
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
