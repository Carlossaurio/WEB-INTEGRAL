<?php
require_once 'conexion.modelo.php';

class ClientesModelo {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::getInstance()->getConexion();
    }

    public function obtenerClientes() {
        try {
            $sql = "SELECT id, nombre, apellido, email, id_cliente, llave_secreta FROM clientes";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    static public function index($tabla) {
        try {
            $stmt = Conexion::getInstance()->getConexion()->prepare("SELECT * FROM $tabla");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }

    static public function create($tabla, $datos) {
        try {
            $stmt = Conexion::getInstance()->getConexion()->prepare("INSERT INTO $tabla(nombre, apellido, email, id_cliente, llave_secreta, created_at, updated_at) VALUES (:nombre, :apellido, :email, :id_cliente, :llave_secreta, :created_at, :updated_at)");
            
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":apellido", $datos["apellido"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_STR);
            $stmt->bindParam(":llave_secreta", $datos["llave_secreta"], PDO::PARAM_STR);
            $stmt->bindParam(":created_at", $datos["created_at"], PDO::PARAM_STR);
            $stmt->bindParam(":updated_at", $datos["updated_at"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                return "ok";
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
}
