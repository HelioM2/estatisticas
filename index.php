<?php
// Conectar ao banco de dados
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'aimagendamento';

$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar se há erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consultar todos os registros da tabela "clientes"
$sql_clientes = "SELECT * FROM clientes";
$result_clientes = $conn->query($sql_clientes);

// Consultar todos os registros da tabela "logs"
$sql_logs = "SELECT * FROM logs ORDER BY data DESC";
$result_logs = $conn->query($sql_logs);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <title>Clientes e Logs</title>

    <style>
        /* style.css */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e2e2e2;
        }
    </style>
</head>

<body>
    <form method="POST" action="gera_logs.php">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Enviar</button>
    </form>


    <h1>Clientes</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_clientes->num_rows > 0) {
                while ($row = $result_clientes->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>
                    <a href='?update_id=" . $row['id'] . "'><button class='btn btn-primary'><i class='bi bi-pencil-square'></i></button></a>
                    <a href='?delete_id=" . $row['id'] . "' onclick='return confirm(\"Você tem certeza que deseja excluir?\")'><button class='btn btn-danger'><i class='bi bi-trash'></i></button></a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Nenhum cliente encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Se a variável "update_id" está definida, exibir o formulário de atualização
    if (isset($_GET['update_id'])) {
        $update_id = $_GET['update_id'];
        $stmt = $conn->prepare("SELECT nome, email FROM clientes WHERE id = ?");
        $stmt->bind_param('i', $update_id);
        $stmt->execute();
        $stmt->bind_result($nome, $email);
        $stmt->fetch();
        $stmt->close();
    ?>

        <h2>Atualizar Cliente</h2>
        <form method="POST" action="gera_logs.php" style="text-align: center; align-items: center; border: 1px solid #333;">
            <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?php echo $nome; ?>" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $email; ?>" required><br>
            <button type="submit">Atualizar</button>
        </form>
    <?php
    }
    ?>



    <h1>Logs</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ação</th>
                <th>Tabela</th>
                <th>Dados Antigos</th>
                <th>Dados Novos</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_logs->num_rows > 0) {
                while ($row = $result_logs->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['acao'] . "</td>";
                    echo "<td>" . $row['tabela'] . "</td>";
                    echo "<td>" . $row['dados_antigos'] . "</td>";
                    echo "<td>" . $row['dados_novos'] . "</td>";
                    echo "<td>" . $row['data'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum log encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Fechar a conexão
    $conn->close();
    ?>
</body>

</html>