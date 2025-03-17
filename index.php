<?php
// Conectar ao banco de dados
// Usa PDO para prevenir SQL Injection
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'aimagendamento';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar se há erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consultar todos os registros da tabela "clientes"
$sql_clientes = "SELECT * FROM clientes";
$result_clientes = $conn->query($sql_clientes);

// Consultar todos os registros da tabela "logs"
$sql_logs = "SELECT * FROM logs ORDER BY data_registro DESC";
$result_logs = $conn->query($sql_logs);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery (necessário para AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS (incluindo scripts de modais) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="style.css">

    <title>Clientes e Logs</title>


    <script>
        function filtrarTabela() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let linhas = document.querySelectorAll("#tabelaClientes tbody tr");

            linhas.forEach(linha => {
                let texto = linha.textContent.toLowerCase();
                linha.style.display = texto.includes(input) ? "" : "none";
            });
        }
    </script>

    <script>
        function filtrarTabela1() {
            let input = document.getElementById("searchInput1").value.toLowerCase();
            let linhas = document.querySelectorAll("#tabelaClientes1 tbody tr");

            linhas.forEach(linha => {
                let texto = linha.textContent.toLowerCase();
                linha.style.display = texto.includes(input) ? "" : "none";
            });
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".ver-cliente").click(function() {
                var clienteId = $(this).data("id");

                $.ajax({
                    url: "buscar_cliente.php",
                    type: "POST",
                    data: {
                        id: clienteId
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            $("#modal-id").text(data.id);
                            $("#modal-nome").text(data.nome);
                            $("#modal-email").text(data.email);
                            $("#modal-foto").attr("src", data.foto);

                            // Abre a modal corretamente no Bootstrap 5
                            var clienteModal = new bootstrap.Modal(document.getElementById("clienteModal"));
                            clienteModal.show();
                        } else {
                            alert("Erro ao carregar os dados.");
                        }
                    },
                    error: function() {
                        alert("Falha na requisição.");
                    }
                });
            });
        });
    </script>


</head>

<body>

    <?php include 'sidebar.php'; ?>


    <h1 class="clients">Controlo de Clientes</h1>
    <form method="POST" action="gera_logs.php" enctype="multipart/form-data">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="file" name="imagem" accept="image/*" required>
        <button type="submit">Registar</button>
    </form><br>


    <input type="text" id="searchInput" onkeyup="filtrarTabela()" placeholder="Pesquisar...">
    <table id="tabelaClientes" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Foto</th>
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
                    echo "<td><img src='" . $row['foto'] . "' width='50'></td>";
                    echo "<td>
            <button class='btn btn-info ver-cliente' data-id='" . $row['id'] . "'><i class='bi bi-eye'></i></button>
            <a href='?update_id=" . $row['id'] . "'><button class='btn btn-primary'><i class='bi bi-pencil-square'></i></button></a>
            <a href='gera_logs.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Você tem certeza que deseja excluir?\")'>
                <button class='btn btn-danger'><i class='bi bi-trash'></i></button>
            </a>
        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Nenhum cliente encontrado</td></tr>";
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
        <form method="POST" action="gera_logs.php" style="text-align: center; align-items: center; ">
            <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
            <label for="nome">Nome:</label>
            <input type="text" name="nome1" value="<?php echo $nome; ?>" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email1" value="<?php echo $email; ?>" required><br>
            <button type="submit">Atualizar</button>
        </form>
    <?php
    }
    ?>



    <h1>Logs</h1>
    <input type="text" id="searchInput1" onkeyup="filtrarTabela1()" placeholder="Pesquisar...">
    <table id="tabelaClientes1" border="1">
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
                    echo "<td>" . $row['data_registro'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum log encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Modal para exibir detalhes do cliente -->
    <div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clienteModalLabel">Detalhes do Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <span id="modal-id"></span></p>
                    <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
                    <p><strong>Email:</strong> <span id="modal-email"></span></p>
                    <p><strong>Foto:</strong></p>
                    <img id="modal-foto" src="" alt="Foto do Cliente" width="100%">
                </div>
            </div>
        </div>
    </div>


    <?php
    // Fechar a conexão
    $conn->close();
    ?>


</body>

</html>