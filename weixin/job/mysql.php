<?php
	$mysql = new PDO("mysql:host=;dbname=test","",""); 
	$mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$mysql->exec('set names utf8');

    $mysql_errno = 0;
    $mysql_errmsg = "OK";
    $_TX_OWNER_ = "";

    function mysqlErrno() {
        global $mysql_errno;
        $errno = $mysql_errno;
        $mysql_errno = 0;
        return $errno;
    }

    function mysqlErrMsg() {
        global $mysql_errmsg;
        return $mysql_errmsg;
    }
    
    function setMysqlError($errno=0, $errmsg="OK") {
        global $mysql_errno;
        global $mysql_errmsg;

        $mysql_errno = $errno;
        $mysql_errmsg = $errmsg;
    }
    function GetMyFuncID() 
    {
        $TraceInfo = debug_backtrace();
        $Count = sizeof($TraceInfo);
        if ($Count <= 2) {
            return("MAIN");
        }else {
            return($TraceInfo[2]["file"].".".$TraceInfo[2]["function"]);
        }
    }

    function BEGIN() 
    {
        global $mysql;
        global $_TX_OWNER_;     
        if ($_TX_OWNER_ === "") {
            $_TX_OWNER_ = GetMyFuncID();
            $mysql->beginTransaction();
        }
        return ;
    }
    
    function COMMIT() 
    {
        global $mysql;
        global $_TX_OWNER_;
        if ($_TX_OWNER_ === GetMyFuncID()) {
            $mysql->commit();
            $_TX_OWNER_ = "";
        }
        return ;
    }
    
    function ROLLBACK() 
    {
        global $mysql;
        global $_TX_OWNER_;
        if ($_TX_OWNER_ === GetMyFuncID()) {
            $mysql->rollback();
            $_TX_OWNER_ = "";
        }
        return ;
    }

    function InsertTest($Line, $Name, $PhoneNum, $CertNum, $ProjectName)
    {
        global $mysql;

        $SQL = "INSERT INTO test(Line,Name,PhoneNum,CertNum,ProjectName) 
        VALUES(:Line,:Name,:PhoneNum,:CertNum,:ProjectName)";

        try{
            $STMT = $mysql->prepare($SQL);
            $Params = array();
            $Params['Line'] = $Line;
            $Params['Name'] = $Name;
            $Params['PhoneNum'] = $PhoneNum;
            $Params['CertNum'] = $CertNum;
            $Params['ProjectName'] = $ProjectName;
            $STMT->execute($Params);
            return 1;
        }
        catch(PDOException $ex){
            echo $ex->getMessage();die;
        }
    }

    function setAccessToken($Name, $Token, $UpdateTS,$ExpireTS)
    {   
        global $mysql;
        $SQL = "UPDATE wxcfg set Token = :Token, UpdateTS = :UpdateTS, ExpireTS = :ExpireTS WHERE Name=:Name";

        try{
            $STMT = $mysql->prepare($SQL);
            $Params = array();
            $Params['Name'] = $Name;
            $Params['Token'] = $Token;
            $Params['UpdateTS'] = $UpdateTS;
            $Params['ExpireTS'] = $ExpireTS;
            $STMT->execute($Params);
            return 1;
        }
        catch(PDOException $ex){
            echo $ex->getMessage();die;
        }
    }

    function InsertTemplate($TemplateID, $Content, $Status)
    {
        global $mysql;
        $SQL = "INSERT INTO template(TemplateID,Content,Status) 
        VALUES(:TemplateID,:Content,:Status)";

        try{
            $STMT = $mysql->prepare($SQL);
            $Params = array();
            $Params['TemplateID'] = $TemplateID;
            $Params['Content'] = $Content;
            $Params['Status'] = $Status;
            $STMT->execute($Params);
            return 1;
        }
        catch(PDOException $ex){
            echo $ex->getMessage();die;
        }
    }

    function MYgetTemplate($Limit)
    {
        global $mysql;
        $SQL = "SELECT * FROM template WHERE status = 0 LIMIT $Limit";
        $Res = array();
        try{
            $STMT = $mysql->prepare($SQL);
            $STMT->execute();
            while (($Row = $STMT->fetch(PDO::FETCH_ASSOC)) !== false) {
                $Res[] = $Row;
            }
            return $Res;
        }
        catch(PDOException $ex){
            echo $ex->getMessage();die;
        }
    }
?>
