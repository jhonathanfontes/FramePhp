<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Não Autorizado - FramePHP</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="container">
        <div class="error-page">
            <h1>403 - Acesso Não Autorizado</h1>
            <p>Você não tem permissão para acessar esta página.</p>
            <a href="<?= base_url() ?>" class="btn btn-primary">Voltar para a página inicial</a>
        </div>
    </div>
</body>
</html>