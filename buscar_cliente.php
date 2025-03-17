<?php
// Conexão com o banco de dados
// Definir parâmetros de conexão
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'aimagendamento';
$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Verifica se o ID foi enviado
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Consulta ao banco de dados
    $sql = "SELECT * FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        echo json_encode($cliente);
    } else {
        echo json_encode(null);
    }
    
    $stmt->close();
}

$conn->close();
?>
