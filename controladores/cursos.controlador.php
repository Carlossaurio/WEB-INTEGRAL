<?php
require_once __DIR__ . '/../modelos/cursos.modelo.php';
require_once __DIR__ . '/../modelos/clientes.modelo.php';

class CursosControlador {
    
    public function index() {
        // Verificar autenticación
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Obtener clientes para validar credenciales
        $clientesModelo = new ClientesModelo();
        $clientes = $clientesModelo->obtenerClientes();
        $clienteAutenticado = false;

        foreach ($clientes as $cliente) {
            if (
                $_SERVER['PHP_AUTH_USER'] === $cliente['id_cliente'] &&
                $_SERVER['PHP_AUTH_PW'] === $cliente['llave_secreta']
            ) {
                $clienteAutenticado = true;
                break;
            }
        }

        if (!$clienteAutenticado) {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciales inválidas']);
            return;
        }

        // Paginación
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $cantidad = isset($_GET['cantidad']) ? (int)$_GET['cantidad'] : 10;
        $desde = ($pagina - 1) * $cantidad;

        // Obtener cursos paginados con información del creador
        $cursos = ModeloCursos::index('cursos', 'clientes', $cantidad, $desde);
        
        if ($cursos === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener los cursos']);
        } else {
            echo json_encode([
                'status' => 200,
                'pagina' => $pagina,
                'cursos' => $cursos
            ]);
        }
    }

    public function create() {
        // Verificar autenticación
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Obtener clientes para validar credenciales
        $clientesModelo = new ClientesModelo();
        $clientes = $clientesModelo->obtenerClientes();
        $clienteAutenticado = false;
        $id_creador = null;

        foreach ($clientes as $cliente) {
            if (
                $_SERVER['PHP_AUTH_USER'] === $cliente['id_cliente'] &&
                $_SERVER['PHP_AUTH_PW'] === $cliente['llave_secreta']
            ) {
                $clienteAutenticado = true;
                $id_creador = $cliente['id'];
                break;
            }
        }

        if (!$clienteAutenticado) {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciales inválidas']);
            return;
        }

        // Verificar que se han enviado los datos necesarios
        if (!isset($_POST['titulo'], $_POST['descripcion'], $_POST['instructor'], $_POST['imagen'], $_POST['precio'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos requeridos']);
            return;
        }

        // Obtener datos del curso
        $datos = [
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'instructor' => $_POST['instructor'],
            'imagen' => $_POST['imagen'],
            'precio' => $_POST['precio'],
            'id_creador' => $id_creador,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Crear curso
        $resultado = ModeloCursos::create('cursos', $datos);
        
        if ($resultado === 'ok') {
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => 'Curso creado exitosamente',
                'data' => $datos
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear el curso']);
        }
    }

    public function show($id) {
        $curso = ModeloCursos::show('cursos', 'clientes', $id);
        
        if ($curso) {
            echo json_encode(['curso' => $curso]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Curso no encontrado']);
        }
    }

    public function update($id) {
        // Get the raw PUT data
        $rawData = file_get_contents('php://input');
        
        // Decode the JSON data
        $datos = json_decode($rawData, true);
        
        if (!$datos || !isset($datos['titulo'], $datos['descripcion'], $datos['instructor'], $datos['imagen'], $datos['precio'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos requeridos']);
            return;
        }

        $datos = [
            'id' => $id,
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'instructor' => $datos['instructor'],
            'imagen' => $datos['imagen'],
            'precio' => $datos['precio'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $resultado = ModeloCursos::update('cursos', $datos);
        
        if ($resultado === 'ok') {
            echo json_encode([
                'status' => 200,
                'message' => 'Curso actualizado correctamente',
                'data' => $datos
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al actualizar el curso']);
        }
    }

    public function delete($id) {
        $resultado = ModeloCursos::delete('cursos', $id);
        
        if ($resultado === 'ok') {
            echo json_encode([
                'status' => 200,
                'message' => 'Curso eliminado correctamente'
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar el curso']);
        }
    }
}
