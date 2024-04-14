<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    include_once("sql.php");
    
    require_once("vendor/autoload.php"); 
    
    $conn = createDataBaseConnection();
    $data = json_decode(file_get_contents('php://input'));
    $id = $data->id;
    $id_user = $data->id_user;
    $token = $data->token;
    
    if (!$token) {
        echo json_encode(array("mensaje" => "Token de autorización no proporcionado"));
        exit();
    }
    try {
        $key = 'your_secret_key';
        $decoded = Firebase\JWT\JWT::decode($token, new Firebase\JWT\Key($key, 'HS256'));   
        $id_user = $decoded->id;
    } 
    catch (Exception $e) {
        echo json_encode(array("mensaje" => "Token de autorización inválido"));
        exit();
    }

    
    $sql = "INSERT INTO Likes (id_user, id_post) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_user, $id);
    
    if ($stmt->execute()) {
        echo json_encode(array("mensaje" => "¡Like subido correctamente!"));
    } 
    else {
        echo json_encode(array("mensaje" => "Error al subir like: " . $stmt->error));
    }
    
    $stmt->close();
    $conn->close();
?>
