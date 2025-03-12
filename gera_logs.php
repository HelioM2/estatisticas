<?php
// Definir parâmetros de conexão
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'aimagendamento';

// Conectar ao banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


// Função para registrar logs
function registrar_log($acao, $tabela, $dados_antigos, $dados_novos)
{
    global $conn;

    // Preparar a instrução SQL para inserir o log
    $stmt = $conn->prepare("INSERT INTO logs (acao, tabela, dados_antigos, dados_novos) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $acao, $tabela, $dados_antigos, $dados_novos);

    // Executar a instrução e verificar se a inserção foi bem-sucedida
    if ($stmt->execute()) {

        // echo "Log registrado com sucesso.<br>";
        header("location: index.php");
    } else {
        echo "Erro ao registrar log: " . $stmt->error . "<br>";
    }

    // Fechar a instrução
    $stmt->close();
}

/* Exemplo de inserção ou atualização na tabela "clientes"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifique se é uma inserção ou uma atualização
    if (isset($_POST['id']) && $_POST['id'] != '') {  // Atualização
        $id = $_POST['id'];
        $novo_nome = $_POST['novo_nome'];
        $novo_email = $_POST['novo_email'];

        // Obter dados antigos antes da atualização
        $stmt = $conn->prepare("SELECT nome, email FROM clientes WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($nome_antigo, $email_antigo);
        $stmt->fetch();
        $stmt->close();

        // Atualizar dados do cliente
        $stmt = $conn->prepare("UPDATE clientes SET nome = ?, email = ? WHERE id = ?");
        $stmt->bind_param('ssi', $novo_nome, $novo_email, $id);

        if ($stmt->execute()) {
            // Registrar o log de atualização
            $dados_antigos = "Nome: $nome_antigo, Email: $email_antigo";
            $dados_novos = "Nome: $novo_nome, Email: $novo_email";
            registrar_log('UPDATE', 'clientes', $dados_antigos, $dados_novos);
        } else {
            echo "Erro na atualização: " . $stmt->error . "<br>";
        }

        $stmt->close();
    } 
}*/


 // Inserção de novo cliente
 
    if (isset($_POST['nome'], $_POST['email'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];

        // Verificar se o email já existe na tabela "clientes"
        $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        // Se o email já existe, não insira novamente
        if ($stmt->num_rows > 0) {
            echo "Erro: O email já está cadastrado.";
        } else {
            // Inserir novo cliente
            $stmt = $conn->prepare("INSERT INTO clientes (nome, email) VALUES (?, ?)");
            $stmt->bind_param('ss', $nome, $email);

            if ($stmt->execute()) {
                // Registrar o log de inserção
                $dados_novos = "Nome: $nome, Email: $email";
                registrar_log('INSERT', 'clientes', null, $dados_novos);
            } else {
                echo "Erro na inserção: " . $stmt->error . "<br>";
            }
        }

        $stmt->close();
    }

   //Actualização na tabela "clientes"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = $_POST['update_id'];
    $novo_nome = $_POST['nome1'];
    $novo_email = $_POST['email1'];

    // Verifique se o ID foi realmente fornecido
    if (empty($id)) {
        echo "Erro: ID não fornecido.";
        exit;
    }

    // Obter dados antes da exclusão para registrar o log
    $stmt = $conn->prepare("SELECT nome, email FROM clientes WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($nome_antigo, $email_antigo);
    $stmt->fetch();
    $stmt->close();

    // Se não encontrar o cliente, interrompa a exclusão
    if (empty($nome_antigo) || empty($email_antigo)) {
        echo "Erro: Cliente não encontrado.";
        exit;
    }

    //Actualizar cliente
    $stmt = $conn->prepare("UPDATE clientes SET nome = ?, email = ? WHERE id = ?");
    $stmt->bind_param('ssi', $novo_nome, $novo_email, $id);

    if ($stmt->execute()) {
        // Registrar o log de atualização
        $dados_antigos = "Nome: $nome_antigo, Email: $email_antigo";
        $dados_novos = "Nome: $novo_nome, Email: $novo_email";
        registrar_log('UPDATE', 'clientes', $dados_antigos, $dados_novos);
        echo "Cliente actualizado com sucesso.";
    } else {
        echo "Erro na atualização: " . $stmt->error . "<br>";
    }

    $stmt->close();
}

 


// Exemplo de exclusão na tabela "clientes"
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Verifique se o ID foi realmente fornecido
    if (empty($id)) {
        echo "Erro: ID não fornecido.";
        exit;
    }

    // Obter dados antes da exclusão para registrar o log
    $stmt = $conn->prepare("SELECT nome, email FROM clientes WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($nome_antigo, $email_antigo);
    $stmt->fetch();
    $stmt->close();

    // Se não encontrar o cliente, interrompa a exclusão
    if (empty($nome_antigo) || empty($email_antigo)) {
        echo "Erro: Cliente não encontrado.";
        exit;
    }

    // Excluir cliente
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        // Registrar o log de exclusão
        $dados_antigos = "Nome: $nome_antigo, Email: $email_antigo";
        $dados_novos = null;
        registrar_log('DELETE', 'clientes', $dados_antigos, $dados_novos);
        echo "Cliente excluído com sucesso.";
    } else {
        echo "Erro na exclusão: " . $stmt->error . "<br>";
    }

    $stmt->close();
}








// Fechar a conexão
$conn->close();
