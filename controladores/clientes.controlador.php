<?php
require_once __DIR__ . '/../modelos/clientes.modelo.php';

class ControladorClientes {
    
    public function create($datos) {
        // Validar nombre
        if (isset($datos["nombre"]) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $datos["nombre"])) {
            $json = [
                "status" => 404,
                "detalle" => "error en el campo del nombre permitido solo letras en el nombre"
            ];
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            return;
        }

        // Validar apellido
        if (isset($datos["apellido"]) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $datos["apellido"])) {
            $json = [
                "status" => 404,
                "detalle" => "error en el campo del apellido permitido solo letras"
            ];
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            return;
        }

        // Validar email
        if (isset($datos["email"]) && !preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $datos["email"])) {
            $json = [
                "status" => 404,
                "detalle" => "error en el campo email"
            ];
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            return;
        }

        // Validar email repetido
        $clientes = ClientesModelo::index("clientes");
        foreach ($clientes as $key => $value) {
            if ($value["email"] == $datos["email"]) {
                $json = [
                    "status" => 404,
                    "detalle" => "el email ya existe"
                ];
                echo json_encode($json, JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        // Generar credenciales del cliente
        $id_cliente = str_replace("$", "c", crypt($datos["nombre"] . $datos["apellido"] . $datos["email"], '$2a$07$afartwetsdAD52356FEDGsfhsd$'));
        $llave_secreta = str_replace("$", "a", crypt($datos["email"] . $datos["apellido"] . $datos["nombre"], '$2a$07$afartwetsdAD52356FEDGsfhsd$'));

        $datos = [
            "nombre" => $datos["nombre"],
            "apellido" => $datos["apellido"],
            "email" => $datos["email"],
            "id_cliente" => $id_cliente,
            "llave_secreta" => $llave_secreta,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s')
        ];

        $create = ClientesModelo::create("clientes", $datos);

        if ($create === "ok") {
            $json = [
                "status" => 201,
                "detalle" => "Credenciales generadas exitosamente",
                "id_cliente" => $id_cliente,
                "llave_secreta" => $llave_secreta
            ];
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            return;
        }

        // En caso de error en la creación
        $json = [
            "status" => 500,
            "detalle" => "Error al crear el cliente"
        ];
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}
