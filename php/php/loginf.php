<?php
include "conexao.php";
$loginx=$_POST["usuario"];
$senhax=$_POST["senha"];

//$loginx="ana";
//$senhax="123";


$comando= "select * from tabf where login='$loginx' and senha='$senhax'";
$resulta = mysqli_query($con,$comando);
 $dados=array("status"=>"-");
while($r = mysqli_fetch_array($resulta)){
 $dados=array("status"=>"ok","login"=>$r[1],"senha"=>$r[2], "foto"=>$r[3]);
}
$close = mysqli_close($con);
echo json_encode($dados);
?>