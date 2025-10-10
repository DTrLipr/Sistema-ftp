<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos Cadastrados</title>
    <style>
        /* Estilos básicos para a página ficar mais organizada */
        body { font-family: sans-serif; margin: 2em; }
        .produto { border: 1px solid #ccc; border-radius: 8px; padding: 16px; margin-bottom: 16px; width: 300px; }
        .produto img { max-width: 100%; height: auto; border-radius: 4px; }
        h1 { color: #333; }
        h2 { margin-top: 0; }
    </style>
</head>
<body>

    <h1>Nossos Produtos</h1>

    <?php
    // --- INÍCIO DO CÓDIGO PHP ---

    // COLOQUE SUAS INFORMAÇÕES DO BANCO DE DADOS AQUI (AS MESMAS DO OUTRO ARQUIVO)
    $servidor = "localhost";
    $usuario_db = "kareng52_wp686"; // <- PREENCHA
    $senha_db = "Sistema@01";   // <- PREENCHA
    $banco = "kareng52_wp686";       // <- PREENCHA

    // 1. CONECTAR AO BANCO DE DADOS
    $conexao = mysqli_connect($servidor, $usuario_db, $senha_db, $banco);

    // Checar conexão
    if (!$conexao) {
        die("Falha na conexão: " . mysqli_connect_error());
    }

    // 2. CRIAR O COMANDO SQL PARA BUSCAR OS DADOS
    // "SELECT id, nome, referencia, caminho_imagem FROM produtos" significa:
    // "Selecione as colunas id, nome, referencia e caminho_imagem da tabela produtos"
    $sql = "SELECT id, nome, referencia, caminho_imagem FROM produtos ORDER BY id DESC"; // "ORDER BY id DESC" mostra os mais recentes primeiro

    // 3. EXECUTAR O COMANDO NO BANCO DE DADOS
    $resultado = mysqli_query($conexao, $sql);

    // 4. VERIFICAR SE ENCONTROU ALGUM PRODUTO
    if (mysqli_num_rows($resultado) > 0) {
        // "mysqli_num_rows" conta quantas linhas (produtos) foram encontradas

        // 5. CRIAR UM LOOP PARA MOSTRAR CADA PRODUTO
        // O "while" vai se repetir para cada produto que o banco de dados retornou
        while($linha = mysqli_fetch_assoc($resultado)) {
            // A cada repetição, a variável $linha terá os dados de um produto
            // Agora, vamos usar HTML para exibir esses dados.
            // Note que o PHP é "fechado" (?>) para que possamos escrever HTML facilmente.
            ?>

            <div class="produto">
                <img src="<?php echo $linha['caminho_imagem']; ?>" alt="Imagem do <?php echo $linha['nome']; ?>">
                
                <h2><?php echo $linha['nome']; ?></h2>
                
                <p>Referência: <?php echo $linha['referencia']; ?></p>
            </div>

            <?php
            // O PHP é "aberto" de novo (<?php) para continuar o loop
        }
    } else {
        // Se não encontrar nenhum produto, mostra uma mensagem
        echo "Nenhum produto cadastrado ainda.";
    }

    // 6. FECHAR A CONEXÃO
    mysqli_close($conexao);

    // --- FIM DO CÓDIGO PHP ---
    ?>

</body>
</html>