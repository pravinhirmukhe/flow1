<?php
class Object
{
    protected  $object_error_on =1;  #错误报告为打开状态。0,为关闭状态
    /**
     * 抛出一个错误，供上层的错误处理部分处理
     * @param string $msg 错误信息
     */
    function error($msg='', $code=0)
    {
        if($this->object_error_on==1){
            $msge =$_SERVER['PHP_SELF']."  : ".$msg;
            throw  new Error($msge, $code);
        }else {
            return ;
        }
    }
}

