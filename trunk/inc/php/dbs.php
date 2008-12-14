<?php

class DBS extends Object 
{ 
    static private $host='localhost';
    static private $username='tfbjcc_foto';
    static private $passwd='#S{BPC!X8deW';
    static private $dbname='tfbjcc_foto';
    static private $port='3306';
    
    static protected  $mysqli=null;
    
    /**
     * 私有的构造函数，singleton的保证。
     */
    private function __construct(){}
    
    /**
     * 创建一个mysqli的实例
     */
    static function create()
    {
        self::$mysqli =new mysqli(self::$host, self::$username, self::$passwd, self::$dbname/*, self::$port*/);
        if(!self::$mysqli->ping()){
            $this->error(self::$mysqli->error);
        }
    }
    
    static function init()
    {
        if(self::$mysqli==null){
            self::create();
            return  self::$mysqli;
        }else{
            return self::$mysqli;
        }
    }
    
    static function end()
    {
        self::$mysqli->close();
    }
    
}