<?php

include "conexao.php";
  $loginx=$_POST["usuario"];
$senhax=$_POST["senha"];
$fotox=$_POST["foto"];
//  $fotox="imagem.jpg";
    $comando= "update tabf set senha='$senhax',foto='$fotox' where login='$loginx'";
    $resulta = mysqli_query($con,$comando);
    $quant=mysqli_affected_rows($con);  //pega a quantidade de registros alterados
       
          if (   $quant>0) {
       $dados=array("status"=>"ok");
    }
    else
      {   $dados=array("status"=>"erro");

}

$close = mysqli_close($con);
echo json_encode($dados);
?>