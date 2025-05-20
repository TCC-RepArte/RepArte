<?php
include "conexao.php";
  $comando= "select * from tabf";
$resulta = mysqli_query($con,$comando);
 $dados=array();
while($r = mysqli_fetch_array($resulta)){
 $dados[]=array("cod"=>$r[0],"login"=>$r[1],"senha"=>$r[2],"foto"=>$r[3]);
}
$close = mysqli_close($con);
echo json_encode($dados);
?>