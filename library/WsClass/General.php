<?php

class WsClass_General
{
    public function __construct() {
//        mysql_connect("localhost","root","");
//        mysql_select_db("bank");

    }

    /**
     * test function for soap server
     *
     * @return string
     */
    public function no_argument()
    {
        return 'demo';
//        $SERVER = "localhost";
//        $USERNAME = "root";
//        $PASSWORD = "";
//        $DBNAME = "bank";
//        $conn = mysql_connect($SERVER, $USERNAME, $PASSWORD);
//        $data = mysql_query("update account set balance=balance+'$balance' where account_id='$account'");
//
//        return mysql_affected_rows($conn);
//        ;
    }
    /**
     * test function for soap server
     *     
     * @param int|string $argument1
     * @param int|string $argument2
     * @return int
     */
    public function has_argument($argument1,$argument2)
    {
        return $argument1+$argument2;      
        
//        $result=mysql_query("select * from account where account_id='$data'");
//        if($row=mysql_fetch_assoc($result)){
//            return $row['account_id'];
//        }
//        return '';

    }
}

