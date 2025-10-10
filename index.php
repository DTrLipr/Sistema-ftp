<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Cadastro de Produtos</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 2em auto; }
        input, button { width: 100%; padding: 10px; margin-bottom: 10px; box-sizing: border-box; }
    </style>
</head>
<body>
    <h1>Cadastrar Novo Produto</h1>
    
    <form action="salvar_produto.php" method="post" enctype="multipart/form-data">
        
        <label for="nome">Nome do Produto:</label>
        <input type="text" id="nome" name="nome_produto" required>
        
        <label for="referencia">Referência:</label>
        <input type="text" id="referencia" name="ref_produto">
        
        <label for="imagem">Imagem do Produto:</label>
        <input type="file" id="imagem" name="imagem_produto" accept="image/*" required>
        
        <button type="submit">Salvar Produto</button>
    </form>

    <hr>
    <a href="listar_produtos.php">Ver produtos cadastrados</a>
</body>
</html>
