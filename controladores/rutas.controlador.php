<?php
class ControladorRutas {
    public function inicio() {
        include __DIR__ . "/../rutas/rutas.php";
    }

    public function procesarRuta($ruta) {
        switch ($ruta) {
            case '' :
            case 'cursos' :
                require_once 'controladores/cursos.controlador.php';
                $controlador = new CursosControlador();
                
                // Determinar el método HTTP
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET' :
                        $id = isset($_GET['id']) ? $_GET['id'] : null;
                        if ($id) {
                            $controlador->show($id);
                        } else {
                            $controlador->index();
                        }
                        break;
                    case 'POST' :
                        $controlador->create();
                        break;
                    case 'DELETE' :
                        $id = isset($_GET['id']) ? $_GET['id'] : null;
                        if ($id) {
                            $controlador->delete($id);
                        } else {
                            http_response_code(400);
                            echo json_encode(['error' => 'ID del curso es requerido']);
                        }
                        break;
                    case 'PUT' :
                        $id = isset($_GET['id']) ? $_GET['id'] : null;
                        if ($id) {
                            $controlador->update($id);
                        } else {
                            http_response_code(400);
                            echo json_encode(['error' => 'ID del curso es requerido']);
                        }
                        break;
                    default :
                        break;
                }
                break;

            case 'clientes' :
                require_once 'controladores/clientes.controlador.php';
                $controlador = new ControladorClientes();
                
                // Determinar el método HTTP
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST' :
                        $datos = [
                            'nombre' => $_POST['nombre'] ?? '',
                            'apellido' => $_POST['apellido'] ?? '',
                            'email' => $_POST['email'] ?? ''
                        ];
                        $controlador->create($datos);
                        break;
                    default :
                        http_response_code(405);
                        echo json_encode(['error' => 'Método no permitido']);
                        break;
                }
                break;

            case 'registro':
                require_once 'controladores/clientes.controlador.php';
                $controlador = new ControladorClientes();
                
                // Determinar el método HTTP
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $datos = [
                            'nombre' => $_POST['nombre'] ?? '',
                            'apellido' => $_POST['apellido'] ?? '',
                            'email' => $_POST['email'] ?? ''
                        ];
                        $controlador->create($datos);
                        break;
                    default:
                        http_response_code(405);
                        echo json_encode(['error' => 'Método no permitido']);
                        break;
                }
                break;
            
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Ruta no encontrada']);
                break;
        }
    }
}
?>
