<?php
header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") { http_response_code(405); echo json_encode(["exito"=>false,"mensaje"=>"Método no permitido."]); exit; }
$id=(int)($_POST["id"]??0);
$estado=trim($_POST["estado"]??"");
$permitidos=["Pendiente","Entregado","Devuelto"];
if($id<=0 || !in_array($estado,$permitidos,true)){ echo json_encode(["exito"=>false,"mensaje"=>"Estado o reserva inválidos."]); exit; }

$q=$mysqli->prepare("SELECT vehiculo_id, estado FROM reservas WHERE id=?");
$q->bind_param("i",$id); $q->execute(); $r=$q->get_result()->fetch_assoc(); $q->close();
if(!$r){ echo json_encode(["exito"=>false,"mensaje"=>"La reserva no existe."]); exit; }
$actual=strtolower($r["estado"]); $nuevo=strtolower($estado);
$transiciones=["pendiente"=>["entregado"],"entregado"=>["devuelto"],"devuelto"=>[]];
if(!in_array($nuevo,$transiciones[$actual]??[],true)){ echo json_encode(["exito"=>false,"mensaje"=>"No se puede cambiar de ".$r["estado"]." a $estado."]); exit; }

$mysqli->begin_transaction();
try {
  $u=$mysqli->prepare("UPDATE reservas SET estado=? WHERE id=?"); $u->bind_param("si",$estado,$id); $u->execute(); $u->close();
  $vehiculoEstado=$estado === "Entregado" ? "Entregado" : ($estado === "Devuelto" ? "Disponible" : "Reservado");
  $v=$mysqli->prepare("UPDATE vehiculos SET estado=? WHERE id=?"); $v->bind_param("si",$vehiculoEstado,$r["vehiculo_id"]); $v->execute(); $v->close();
  $mysqli->commit();
  echo json_encode(["exito"=>true,"mensaje"=>"Reserva actualizada a $estado. El vehículo ahora está $vehiculoEstado."]);
} catch(Throwable $e) { $mysqli->rollback(); http_response_code(500); echo json_encode(["exito"=>false,"mensaje"=>"No se pudo actualizar la reserva."]); }
$mysqli->close();
?>