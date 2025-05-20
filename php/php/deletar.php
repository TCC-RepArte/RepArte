<?php
include "conexao.php";
$loginx=$_POST["usuario"];
$senhax=$_POST["senha"];

    $comando= "delete from tab where login='$loginx' and senha='$senhax'";
    $resulta = mysqli_query($con,$comando);
    $quant=mysqli_affected_rows($con);  //pega a quantidade de registros deletados
       
          if (   $quant>0) {
       $dados=array("status"=>"ok");
    }
    else
      {   $dados=array("status"=>"erro");


}

$close = mysqli_close($con);
echo json_encode($dados);
?>