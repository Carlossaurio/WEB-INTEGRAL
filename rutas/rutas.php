<?php
// No necesitamos hacer require aquí ya que ya se incluyó en index.php

// Obtener la ruta de la URL
$ruta = isset($_GET['ruta']) ? $_GET['ruta'] : '';

// Procesar la ruta
$controlador = new ControladorRutas();
$controlador->procesarRuta($ruta);
?>
