<?php
class Connect extends PDO{
    public function_construst (){
        parent::_construst("msql:localhost;dbname=msp","root","",
        array(PDO::MSQL_ATTR_INIT_COMMAND =>" SET NAPES utf8"));
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXECPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
}
$tbl_notificacoes = "notificacoes";
$their ='id';