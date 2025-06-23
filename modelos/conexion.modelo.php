<?php
class Conexion {
    private static $instance = null;
    private $conexion;

    private function __construct() {
        $host = 'localhost';
        $db = 'api-rest';
        $user = 'root';
        $pass = '';

        try {
            $this->conexion = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $this->conexion->exec("set names utf8"); /* configuración de caracteres para php */
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Conexion();
        }
        return self::$instance;
    }

    public function getConexion() {
        return $this->conexion;
    }
}
?>
