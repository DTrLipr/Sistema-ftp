<?php
// ESSENCIAL: Inicia a sessão para que o sistema de login funcione. DEVE ser a primeira linha.
session_start();

// --- CABEÇALHOS PARA IMPEDIR O CACHE DO NAVEGADOR ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// --- CONFIGURAÇÕES GERAIS ---
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");

// --- CONFIGURAÇÕES DO BANCO DE DADOS ---
$servidor = "localhost";
$usuario_db = "kareng52_wp77"; // SEU USUÁRIO DO BANCO
$senha_db = "Sistema@02";   // ✅ COLOQUE A SENHA CORRETA AQUI
$banco = "kareng52_wp77";       // SEU NOME DO BANCO

// --- CONEXÃO ---
$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);
if ($conexao->connect_error) {
    die(json_encode(["sucesso" => false, "mensagem" => "Falha na conexão: " . $conexao->connect_error]));
}

// --- FUNÇÃO DE VERIFICAÇÃO DE PERMISSÃO ---
// Esta função irá proteger as rotas de produtos
function verificarLogin() {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401); // Código para "Não Autorizado"
        die(json_encode(["sucesso" => false, "mensagem" => "Acesso negado. Por favor, faça login."]));
    }
}


// --- ROTAS DA API ---
$metodo = $_SERVER['REQUEST_METHOD'];
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// ✅ NOVA ROTA: REGISTRAR UM NOVO USUÁRIO (INSCREVER-SE)
if ($metodo == 'POST' && $acao == 'registrar') {
    $nome_completo = $_POST['nome_completo'];
    $data_nascimento = $_POST['data_nascimento'];
    $nome_usuario = $_POST['nome_usuario'];
    $cargo_empresa = $_POST['cargo_empresa'];
    $senha = $_POST['senha'];

    // CRIPTOGRAFA A SENHA - ESSENCIAL PARA SEGURANÇA
    $hash_senha = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome_completo, data_nascimento, nome_usuario, cargo_empresa, senha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssss", $nome_completo, $data_nascimento, $nome_usuario, $cargo_empresa, $hash_senha);
    
    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true, "mensagem" => "Usuário registrado com sucesso!"]);
    } else {
        // die(json_encode(["sucesso" => false, "mensagem" => $stmt->error]));
        echo json_encode(["sucesso" => false, "mensagem" => "Erro: Este nome de usuário já está em uso."]);
    }
    $stmt->close();
} 
// ✅ NOVA ROTA: AUTENTICAR UM USUÁRIO (LOGIN)
elseif ($metodo == 'POST' && $acao == 'login') {
    $nome_usuario = $_POST['nome_usuario'];
    $senha = $_POST['senha'];

    $sql = "SELECT id, nome_completo, nome_usuario, cargo_empresa, senha FROM usuarios WHERE nome_usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $nome_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        // Verifica se a senha enviada corresponde à senha criptografada no banco
        if (password_verify($senha, $usuario['senha'])) {
            // Salva os dados do usuário na sessão PHP
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nome_completo' => $usuario['nome_completo'],
                'nome_usuario' => $usuario['nome_usuario'],
                'cargo_empresa' => $usuario['cargo_empresa']
            ];
            echo json_encode(["sucesso" => true, "usuario" => $_SESSION['usuario']]);
        } else {
            echo json_encode(["sucesso" => false, "mensagem" => "Nome de usuário ou senha inválidos."]);
        }
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Nome de usuário ou senha inválidos."]);
    }
    $stmt->close();
}
// ✅ NOVA ROTA: VERIFICAR SE EXISTE UMA SESSÃO ATIVA
elseif ($metodo == 'GET' && $acao == 'verificar_sessao') {
    if (isset($_SESSION['usuario'])) {
        echo json_encode(["logado" => true, "usuario" => $_SESSION['usuario']]);
    } else {
        echo json_encode(["logado" => false]);
    }
}
// ✅ NOVA ROTA: DESTRUIR A SESSÃO (LOGOUT)
elseif ($metodo == 'GET' && $acao == 'logout') {
    session_destroy();
    echo json_encode(["sucesso" => true, "mensagem" => "Logout realizado."]);
}

// --- ROTAS DE PRODUTOS (AGORA PROTEGIDAS) ---

// ROTA PARA LISTAR PRODUTOS
elseif ($metodo == 'GET' && $acao == 'listar') {
    verificarLogin(); // Protege a rota
    $sql = "SELECT id, nome, referencia, caracteristica, imagem, materias_primas FROM produtos ORDER BY id DESC";
    // ... (o resto do seu código de listar continua igual)
    $resultado = $conexao->query($sql);
    $produtos = [];
    while ($linha = $resultado->fetch_assoc()) {
        $linha['materias_primas'] = explode(',', $linha['materias_primas']);
        $produtos[] = $linha;
    }
    echo json_encode($produtos);
} 
// ROTA PARA ADICIONAR PRODUTO
elseif ($metodo == 'POST' && $acao == 'adicionar') {
    verificarLogin(); // Protege a rota
    // ... (o resto do seu código de adicionar continua igual)
    $nome = $_POST['name'];
    $ref = $_POST['ref'];
    $char = $_POST['char'];
    $materias_primas = $_POST['rawMaterials'];
    $caminho_imagem = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $pasta_uploads = "uploads/";
        if (!is_dir($pasta_uploads)) { mkdir($pasta_uploads, 0777, true); }
        $nome_arquivo = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $caminho_imagem = $pasta_uploads . $nome_arquivo;
        move_uploaded_file($_FILES["image"]["tmp_name"], $caminho_imagem);
    }

    $sql = "INSERT INTO produtos (nome, referencia, caracteristica, imagem, materias_primas) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssss", $nome, $ref, $char, $caminho_imagem, $materias_primas);
    
    if ($stmt->execute()) {
        $id_inserido = $stmt->insert_id;
        echo json_encode([
            "sucesso" => true, 
            "mensagem" => "Produto adicionado!",
            "produto" => ["id" => $id_inserido, "nome" => $nome, "referencia" => $ref, "caracteristica" => $char, "imagem" => $caminho_imagem, "materias_primas" => explode(',', $materias_primas)]
        ]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao salvar: " . $stmt->error]);
    }
    $stmt->close();
} 
// ROTA PARA EDITAR PRODUTO
elseif ($metodo == 'POST' && $acao == 'editar') {
    verificarLogin(); // Protege a rota
    // ... (o resto do seu código de editar continua igual)
    $id = $_POST['id'];
    $nome = $_POST['name'];
    $ref = $_POST['ref'];
    $char = $_POST['char'];
    $materias_primas = $_POST['rawMaterials'];
    
    $sql_imagem_atual = "SELECT imagem FROM produtos WHERE id = ?";
    $stmt_img = $conexao->prepare($sql_imagem_atual);
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $resultado_img = $stmt_img->get_result();
    $produto_atual = $resultado_img->fetch_assoc();
    $caminho_imagem = $produto_atual['imagem'];
    $stmt_img->close();

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $pasta_uploads = "uploads/";
        if (!is_dir($pasta_uploads)) { mkdir($pasta_uploads, 0777, true); }
        $nome_arquivo = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $caminho_imagem = $pasta_uploads . $nome_arquivo;
        move_uploaded_file($_FILES["image"]["tmp_name"], $caminho_imagem);
    }

    $sql = "UPDATE produtos SET nome = ?, referencia = ?, caracteristica = ?, materias_primas = ?, imagem = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $ref, $char, $materias_primas, $caminho_imagem, $id);

    if ($stmt->execute()) {
        echo json_encode([
            "sucesso" => true, 
            "mensagem" => "Produto atualizado!",
            "produto" => ["id" => intval($id), "nome" => $nome, "referencia" => $ref, "caracteristica" => $char, "imagem" => $caminho_imagem, "materias_primas" => explode(',', $materias_primas)]
        ]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar: " . $stmt->error]);
    }
    $stmt->close();
} 
// ROTA PARA DELETAR PRODUTO
elseif ($metodo == 'POST' && $acao == 'deletar') {
    verificarLogin(); // Protege a rota
    // ... (o resto do seu código de deletar continua igual)
    $id = $_POST['id'];
    $sql = "DELETE FROM produtos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true, "mensagem" => "Produto deletado com sucesso."]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao deletar: " . $stmt->error]);
    }
    $stmt->close();
}

$conexao->close();
?>