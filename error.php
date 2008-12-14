<?php
/**
 * uslinc 的错误处理类
 *
 */
class Error extends  Exception 
{
    /**
     * 构造函数，继承了Exception
     *
     * @param string $msg 错误信息
     * @param int    $code 错误代号
     */
    function __construct($msg, $code=0)
    {
        parent::__construct($msg, $code);
    }
    
    
    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}