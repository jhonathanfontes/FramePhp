<?php

/**
 * Rotas para o Controller de Exemplo
 * Demonstra como usar as novas funcionalidades do FramePhp
 */

// Rotas principais
$router->get('/exemplo', 'ExampleController@index');
$router->get('/exemplo/criar', 'ExampleController@criar');
$router->post('/exemplo/salvar', 'ExampleController@salvar');
$router->get('/exemplo/editar/{id}', 'ExampleController@editar');
$router->post('/exemplo/atualizar/{id}', 'ExampleController@atualizar');
$router->get('/exemplo/excluir/{id}', 'ExampleController@excluir');

// Rotas de API
$router->get('/api/exemplo', 'ExampleController@apiListar');
$router->post('/api/exemplo', 'ExampleController@apiCriar');

// Rotas administrativas (apenas para admins)
$router->get('/exemplo/cache-stats', 'ExampleController@cacheStats');
$router->get('/exemplo/limpar-cache', 'ExampleController@limparCache');

// Grupo de rotas com middleware de autenticação
$router->group(['middleware' => 'auth'], function($router) {
    $router->get('/exemplo/perfil', 'ExampleController@perfil');
    $router->post('/exemplo/perfil', 'ExampleController@atualizarPerfil');
});

// Grupo de rotas com middleware de permissões
$router->group(['middleware' => 'permissions'], function($router) {
    $router->get('/exemplo/admin', 'ExampleController@admin');
    $router->post('/exemplo/admin', 'ExampleController@adminAction');
});
