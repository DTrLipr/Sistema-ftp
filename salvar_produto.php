<?php
// ATIVA A EXIBIÇÃO DE TODOS OS ERROS. ESSENCIAL PARA DESCOBRIR O PROBLEMA.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =======================================================================
// 1. CONFIGURAÇÕES DO BANCO DE DADOS - PREENCHA AQUI
// =======================================================================
$servidor = "localhost";
$usuario_db = "kareng52_wp686"; // <- PREENCHA
$senha_db = "Sistema@01";   // <- PREENCHA
$banco = "kareng52_wp686";       // <- PREENCHA

// =======================================================================
// 2. CONEXÃO COM O BANCO DE DADOS
// =======================================================================
// Cria a conexão usando um método mais moderno
$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);

// Verifica se a conexão falhou
if ($conexao->connect_error) {
    // Se falhar, o script para aqui e mostra o erro exato.
    die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
}
echo "Conexão com o banco de dados bem-sucedida!<br>";


// =======================================================================
// 3. VERIFICA SE O FORMULÁRIO FOI ENVIADO
// =======================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Pega os dados de texto do formulário
    $nome_produto = $_POST['nome_produto'];
    $ref_produto = $_POST['ref_produto'];
    $caminho_final_da_imagem = null; // Inicia como nulo

    // =======================================================================
    // 4. PROCESSAMENTO DO UPLOAD DA IMAGEM
    // =======================================================================
    // Verifica se um arquivo foi enviado e se não houve erro no upload
    if (isset($_FILES['imagem_produto']) && $_FILES['imagem_produto']['error'] == 0) {
        
        $pasta_uploads = "uploads/";
        // Cria um nome de arquivo único para evitar que uma imagem substitua outra
        $nome_unico_arquivo = uniqid() . '_' . basename($_FILES["imagem_produto"]["name"]);
        $caminho_final_da_imagem = $pasta_uploads . $nome_unico_arquivo;

        // Verifica se o arquivo é mesmo uma imagem
        $tipo_arquivo = strtolower(pathinfo($caminho_final_da_imagem, PATHINFO_EXTENSION));
        if ($tipo_arquivo != "jpg" && $tipo_arquivo != "png" && $tipo_arquivo != "jpeg" && $tipo_arquivo != "gif") {
            die("ERRO: Apenas arquivos JPG, JPEG, PNG & GIF são permitidos.");
        }

        // Tenta mover o arquivo para a pasta 'uploads'
        if (move_uploaded_file($_FILES["imagem_produto"]["tmp_name"], $caminho_final_da_imagem)) {
            echo "A imagem ". htmlspecialchars($nome_unico_arquivo). " foi enviada com sucesso.<br>";
        } else {
            die("ERRO: Houve um problema ao mover o arquivo da imagem para o servidor.");
        }
    } else {
        echo "Nenhuma imagem foi enviada ou houve um erro no upload.<br>";
    }

    // =======================================================================
    // 5. INSERÇÃO DOS DADOS NO BANCO (MÉTODO SEGURO)
    // =======================================================================
    // Prepara o comando SQL para evitar injeção de SQL (mais seguro)
    $sql = "INSERT INTO produtos (nome, referencia, caminho_imagem) VALUES (?, ?, ?)";
    
    // Prepara a declaração
    $stmt = $conexao->prepare($sql);
    
    if ($stmt === false) {
        die("ERRO: Não foi possível preparar a declaração SQL: " . $conexao->error);
    }
    
    // Associa as variáveis PHP aos parâmetros do SQL ("sss" significa 3 strings)
    $stmt->bind_param("sss", $nome_produto, $ref_produto, $caminho_final_da_imagem);

    // Executa o comando
    if ($stmt->execute()) {
        echo "Produto cadastrado com sucesso no banco de dados!";
    } else {
        echo "ERRO: Não foi possível cadastrar o produto: " . $stmt->error;
    }

    // Fecha a declaração e a conexão
    $stmt->close();
    $conex