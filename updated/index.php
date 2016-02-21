<?php
use Slim\Views\PhpRenderer;

require __DIR__ . '/utility.php';

$app = new \Slim\App();

$container = $app->getContainer();
$container['renderer'] = new PhpRenderer('./templates/');
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use($container, $dati) {
        $container->renderer->render($response, 'shared/404.php', array ('dati' => $dati));
        return $response;
    };
};

$app->get('/', 
        function ($request, $response, $args) use($dati) {
            $response = $this->renderer->render($response, 'index.php', array ('dati' => $dati));
            return $response;
        })->setName('index');

$app->map([ 'GET', 'POST'], '/contattaci', 
        function ($request, $response, $args) use($dati) {
            $response = $this->renderer->render($response, 'email.php', array ('dati' => $dati));
            if (fatto()) {
                $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
            }
        });

$app->map([ 'GET', 'POST'], '/templates[/{name+}]', 
        function ($request, $response, $args) use($dati) {
            $response = $this->renderer->render($response, 'shared/404.php', array ('dati' => $dati));
            return $response;
        });

$app->get('/guida/:id', 
        function ($request, $response, $args) use($dati) {
            $response = $this->renderer->render($response, 'index.php', array ('dati' => $dati, 'guida' => $args['id']));
            return $response;
        });

if (!$dati['debug']) {
    $app->map([ 'GET', 'POST'], '/login', 
            function ($request, $response, $args) use($dati) {
                $response = $this->renderer->render($response, 'login/index.php', array ('dati' => $dati));
                if (isUserAutenticate()) {
                    $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                }
                return $response;
            });
    
    $app->get('/logout', 
            function ($request, $response, $args) use($dati) {
                $response = $this->renderer->render($response, 'login/logout.php', array ('dati' => $dati));
                $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                return $response;
            });
    
    $app->map([ 'GET', 'POST'], '/verifica/:id', 
            function ($request, $response, $args) use($dati) {
                $dati['database']->update('persone', array ('dati' => $dati, 'verificata' => 1), 
                        array ('dati' => $dati, 'verificata' => $args['id']));
                $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                return $response;
            });
    
    if (isUserAutenticate()) {
        $app->get('/profilo', 
                function ($request, $response, $args) use($dati) {
                    $response = $this->renderer->render($response, 'user.php', array ('dati' => $dati));
                    return $response;
                });
        
        $app->get('/check', 
                function ($request, $response, $args) use($dati) {
                    $results = $dati['database']->select('persone', array ('nome', 'email', 'verificata'), array ('id' => $dati['user']));
                    if ($results != null) {
                        foreach ($results as $result) {
                            if ($result['verificata'] != 1) {
                                send(decode($result['email']), $dati['sito'], 'Verifica email', 
                                        '<p>&Egrave; necessario eseguire la verifica dell\'email inserita nel tuo account per il sito dell\'autogestione, al fine di assicurarci che siamo in grado di contattarti in caso di necessit&agrave;.</p>
                    <p>Clicca sul link seguente o copialo nella barra del browser per completare l\'operazione.</p>
                    <p><center><a href=\'http://itiseuganeo.altervista.org/verifica/' . $result['verificata'] .
                                                 '\'>http://itiseuganeo.altervista.org/verifica/' . $result['verificata'] .
                                                 '<a></center></p>', $result['nome']);
                            }
                        }
                    }
                    finito('reinvia');
                    $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                })->setName('check');
        
        $app->map([ 'GET', 'POST'], '/modifica', 
                function ($request, $response, $args) use($dati) {
                    $response = $this->renderer->render($response, 'login/modifica.php', array ('dati' => $dati));
                    $fatto = fatto();
                    if ($dati['first'] && $fatto) {
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('check'));
                    }
                    elseif ($fatto) {
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                    }
                    return $response;
                });
        
        if (isAdminUserAutenticate()) {
            $app->get('/autogestioni', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/autogestioni.php', array ('dati' => $dati));
                    })->setName('autogestioni');
            
            $app->map([ 'GET', 'POST'], '/autogestione', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/autogestioni.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('autogestioni'));
                        }
                        return $response;
                    });
        }
    }
    else {
        $app->map([ 'GET', 'POST'], '/recupero', 
                function ($request, $response, $args) use($dati) {
                    $response = $this->renderer->render($response, 'login/index.php', array ('dati' => $dati, 'recupero' => true));
                    if (fatto()) {
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                    }
                    return $response;
                });
        
        $app->map([ 'GET', 'POST'], '/recupero/:id', 
                function ($request, $response, $args) use($dati) {
                    $response = $this->renderer->render($response, 'login/modifica.php', array ('dati' => $dati, 'rand' => $args['id']));
                    if (fatto()) {
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                    }
                    return $response;
                });
    }
    if (isUserAutenticate() && !$dati['first'] && $dati['autogestione'] != null) {
        $app->map([ 'GET', 'POST'], '/impostazioni', 
                function ($request, $response, $args) use($dati) {
                    $response = $this->renderer->render($response, 'login/modifica.php', array ('dati' => $dati, 'info' => true));
                    if (fatto()) {
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                    }
                    return $response;
                });
        
        $app->map([ 'GET', 'POST'], '/email', 
                function ($request, $response, $args) use($dati) {
                    $response = $this->renderer->render($response, 'login/modifica.php', array ('dati' => $dati, 'email' => true));
                    if (fatto()) {
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('check'));
                    }
                    return $response;
                });
        
        if ($dati['sezioni']['forum']) {
            /* Forum */
            $app->get('/forum', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/index.php', array ('dati' => $dati));
                        return $response;
                    });
            
            $app->get('/posts', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/posts.php', array ('dati' => $dati, 'user' => $dati['user']));
                    })->setName('post');
            
            $app->get('/posts/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/posts.php', 
                                array ('dati' => $dati, 'articolo' => $args['id'], 'user' => $dati['user']));
                        return $response;
                    });
            
            $app->get('/tipo/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/tipi.php', array ('dati' => $dati, 'id' => $args['id']));
                        return $response;
                    });
            
            $app->get('/categoria/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/categorie.php', array ('dati' => $dati, 'id' => $args['id']));
                        return $response;
                    });
            
            $app->map([ 'GET', 'POST'], '/articolo/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/articoli.php', array ('dati' => $dati, 'id' => $args['id']));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('articoli'));
                        }
                        return $response;
                    });
            
            $app->get('/articoli', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/articoli.php', array ('dati' => $dati));
                    })->setName('articoli');
            
            $app->get('/categorie', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/categorie.php', array ('dati' => $dati));
                    })->setName('categorie');
            
            $app->get('/tipi', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'forum/tipi.php', array ('dati' => $dati));
                    })->setName('tipi');
            
            $app->group('/nuovo', 
                    function () use($dati) {
                        $this->map([ 'GET', 'POST'], '/articolo', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'forum/articoli.php', 
                                            array ('dati' => $dati, 'new' => true));
                                    if (fatto()) {
                                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('articoli'));
                                    }
                                    return $response;
                                });
                        
                        $this->map([ 'GET', 'POST'], '/articolo/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'forum/articoli.php', 
                                            array ('dati' => $dati, 'new' => true, 'categoria' => $args['id']));
                                    if (fatto()) {
                                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('articoli'));
                                    }
                                    return $response;
                                });
                        
                        if (isAdminUserAutenticate()) {
                            $this->map([ 'GET', 'POST'], '/categoria', 
                                    function ($request, $response, $args) use($dati) {
                                        $response = $this->renderer->render($response, 'forum/categorie.php', 
                                                array ('dati' => $dati, 'new' => true));
                                        if (fatto()) {
                                            $response = $response->withStatus(301)->withHeader('Location', 
                                                    $this->router->pathFor('categorie'));
                                        }
                                        return $response;
                                    });
                            
                            $this->map([ 'GET', 'POST'], '/categoria/:id', 
                                    function ($request, $response, $args) use($dati) {
                                        $response = $this->renderer->render($response, 'forum/categorie.php', 
                                                array ('dati' => $dati, 'new' => true, 'tipo' => $args['id']));
                                        if (fatto()) {
                                            $response = $response->withStatus(301)->withHeader('Location', 
                                                    $this->router->pathFor('categorie'));
                                        }
                                        return $response;
                                    });
                            
                            $this->map([ 'GET', 'POST'], '/tipo', 
                                    function ($request, $response, $args) use($dati) {
                                        $response = $this->renderer->render($response, 'forum/tipi.php', 
                                                array ('dati' => $dati, 'new' => true));
                                        if (fatto()) {
                                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('tipi'));
                                        }
                                        return $response;
                                    });
                        }
                    });
            
            $app->group('/modifica', 
                    function () use($dati) {
                        $this->map([ 'GET', 'POST'], '/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'forum/posts.php', 
                                            array ('dati' => $dati, 'edit' => $args['id']));
                                    if (fatto()) {
                                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('articoli'));
                                    }
                                    return $response;
                                });
                        
                        $this->map([ 'GET', 'POST'], '/articolo/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'forum/articoli.php', 
                                            array ('dati' => $dati, 'edit' => $args['id']));
                                    if (fatto()) {
                                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('articoli'));
                                    }
                                    return $response;
                                });
                        
                        if (isAdminUserAutenticate()) {
                            $this->map([ 'GET', 'POST'], '/categoria/:id', 
                                    function ($request, $response, $args) use($dati) {
                                        $response = $this->renderer->render($response, 'forum/categorie.php', 
                                                array ('dati' => $dati, 'edit' => $args['id']));
                                        if (fatto()) {
                                            $response = $response->withStatus(301)->withHeader('Location', 
                                                    $this->router->pathFor('categorie'));
                                        }
                                        return $response;
                                    });
                            
                            $this->map([ 'GET', 'POST'], '/tipo/:id', 
                                    function ($request, $response, $args) use($dati) {
                                        $response = $this->renderer->render($response, 'forum/tipi.php', 
                                                array ('dati' => $dati, 'edit' => $args['id']));
                                        if (fatto()) {
                                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('tipi'));
                                        }
                                        return $response;
                                    });
                        }
                    });
        }
        
        if ($dati['sezioni']['corsi']) {
            /* Corsi */
            $app->get('/corsi', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'corsi.php', array ('dati' => $dati));
                    })->setName('corsi');
            
            $app->get('/corsi/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'corsi.php', array ('dati' => $dati, 'id' => $args['id']));
                        if (!isset($_GET['ajax'])) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('corsi'));
                        }
                        return $response;
                    });
            
            $app->get('/corso/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'corsi.php', array ('dati' => $dati, 'view' => $args['id']));
                        return $response;
                    });
            
            $app->get('/presenze', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'corsi.php', array ('dati' => $dati, 'presenze' => true));
                        return $response;
                    });
            
            /* Squadre */
            $app->get('/squadra/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'squadra.php', array ('dati' => $dati, 'id' => $args['id']));
                        return $response;
                    });
            
            $app->get('/giocatori/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'squadra.php', array ('dati' => $dati, 'gioca' => $args['id']));
                        return $response;
                    });
            
            $app->map([ 'GET', 'POST'], '/squadra', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'squadra.php', array ('dati' => $dati, 'new' => 1));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('corsi'));
                        }
                        return $response;
                    });
            
            $app->map([ 'GET', 'POST'], '/edit/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'squadra.php', array ('dati' => $dati, 'edit' => $args['id']));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('corsi'));
                        }
                        return $response;
                    });
            
            $app->get('/delete/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'squadra.php', 
                                array ('dati' => $dati, 'id' => $args['id'], 'delete' => ''));
                        return $response;
                    });
            
            $app->get('/delete/yes/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'squadra.php', 
                                array ('dati' => $dati, 'id' => $args['id'], 'delete' => 'yes'));
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('corsi'));
                        return $response;
                    });
            
            $app->get('/aggiungi/:id/:add', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'squadra.php', 
                                array ('dati' => $dati, 'gioca' => $args['id'], 'add' => $args['add']));
                        $response = $response->withStatus(301)->withHeader('Location', 
                                $this->router->pathFor('index') . 'giocatori/' . squadra($dati['database'], $dati['user']));
                        return $response;
                    });
            
            $app->get('/rimuovi/:id/:remove', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'squadra.php', 
                                array ('dati' => $dati, 'gioca' => $args['id'], 'remove' => $remove));
                        $response = $response->withStatus(301)->withHeader('Location', 
                                $this->router->pathFor('index') . 'giocatori/' . squadra($dati['database'], $dati['user']));
                        return $response;
                    });
            
            /* Proposte */
            $app->get('/proposte', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'proposte.php', array ('dati' => $dati));
                    })->setName('proposte');
            
            $app->get('/proposte/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'proposte.php', array ('dati' => $dati, 'id' => $args['id']));
                        if (!isset($_GET['ajax'])) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('proposte'));
                        }
                        return $response;
                    });
            
            $app->map([ 'GET', 'POST'], '/proposta', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'proposte.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('proposte'));
                        }
                        return $response;
                    });
        }
        
        if ($dati['sezioni']['citazioni']) {
            /* Citazioni */
            $app->get('/citazioni', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'citazioni.php', array ('dati' => $dati, 'citazione' => true));
                    })->setName('citazioni');
            
            $app->get('/citazioni/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'citazioni.php', 
                                array ('dati' => $dati, 'citazione' => true, 'id' => $args['id']));
                        if (!isset($_GET['ajax'])) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('citazioni'));
                        }
                        return $response;
                    });
            
            $app->get('/citazione/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'citazioni.php', 
                                array ('dati' => $dati, 'citazione' => true, 'view' => $args['id']));
                        return $response;
                    });
            
            $app->map([ 'GET', 'POST'], '/citazione', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'citazioni.php', 
                                array ('dati' => $dati, 'citazione' => true, 'new' => true));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('citazioni'));
                        }
                        return $response;
                    });
        }
        
        if ($dati['sezioni']['aule']) {
            /* Aule studio */
            $app->get('/aule', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'aule.php', array ('dati' => $dati, 'aula' => true));
                    })->setName('aule');
            
            $app->get('/aule/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'aule.php', 
                                array ('dati' => $dati, 'aula' => true, 'id' => $args['id']));
                        if (!isset($_GET['ajax'])) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('aule'));
                        }
                        return $response;
                    });
            
            $app->get('/aula/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'aule.php', 
                                array ('dati' => $dati, 'aula' => true, 'view' => $args['id']));
                        return $response;
                    });
            
            $app->map([ 'GET', 'POST'], '/aula', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'aule.php', array ('dati' => $dati, 'aula' => true, 'new' => true));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('aule'));
                        }
                        return $response;
                    });
        }
        
        /* Profili altrui e barcode */
        $app->get('/profilo/:id', 
                function ($request, $response, $args) use($dati) {
                    $response = $this->renderer->render($response, 'user.php', array ('dati' => $dati, 'id' => $args['id']));
                    return $response;
                });
        
        $app->get('/utenti', 
                function ($request, $response, $args) use($dati) {
                    $response = $this->renderer->render($response, 'admin/utenti.php', array ('dati' => $dati));
                })->setName('utenti');
        
        $app->get('/barcode', 
                function ($request, $response, $args) use($dati) {
                    $response = $response->withHeader('Content-type', 'image/gif');
                    $response = $this->renderer->render($response, 'barcode/gif.php', array ('dati' => $dati, 'id' => $dati['user']));
                    return $response;
                });
        
        $app->get('/barcode/:id', 
                function ($request, $response, $args) use($dati) {
                    $response = $response->withHeader('Content-type', 'image/gif');
                    $response = $this->renderer->render($response, 'barcode/gif.php', array ('dati' => $dati, 'id' => $args['id']));
                    return $response;
                });
        
        $app->get('/barcode/:id/huge', 
                function ($request, $response, $args) use($dati) {
                    $response = $response->withHeader('Content-type', 'image/gif');
                    $response = $this->renderer->render($response, 'barcode/gif.php', 
                            array ('dati' => $dati, 'id' => $args['id'], 'huge' => true));
                    return $response;
                });
        
        if (isAdminUserAutenticate()) {
            if ($dati['sezioni']['forum']) {
                /* Forum */
                $app->get('/post', 
                        function ($request, $response, $args) use($dati) {
                            $response = $this->renderer->render($response, 'forum/posts.php', array ('dati' => $dati));
                            return $response;
                        });
                
                $app->get('/posts/:id/tutti', 
                        function ($request, $response, $args) use($dati) {
                            $response = $this->renderer->render($response, 'forum/posts.php', 
                                    array ('dati' => $dati, 'articolo' => $args['id']));
                            return $response;
                        });
            }
            
            /* Amministrazione */
            $app->get('/admin', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/index.php', array ('dati' => $dati));
                        return $response;
                    });
            
            $app->map([ 'GET', 'POST'], '/aggiorna', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/aggiorna.php', array ('dati' => $dati));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('index'));
                        }
                        return $response;
                    });
            
            $app->get('/utenti/reset/:id', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/utenti.php', array ('dati' => $dati, 'reset' => $args['id']));
                        if (!isset($_GET['ajax'])) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('utenti'));
                        }
                        return $response;
                    });
            
            $app->get('/accessi', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/accessi.php', array ('dati' => $dati));
                    })->setName('accessi');
            
            $app->get('/reset/accessi', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/accessi.php', array ('dati' => $dati, 'reset' => true));
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('accessi'));
                        return $response;
                    });
            
            $app->get('/sessioni', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/sessioni.php', array ('dati' => $dati));
                    })->setName('sessions');
            
            $app->get('/reset/sessioni', 
                    function ($request, $response, $args) use($dati) {
                        $this->renderer->render($response, 'admin/sessioni.php', array ('dati' => $dati, 'reset' => true));
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('sessions'));
                        return $response;
                    });
            
            $app->get('/scuole', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/scuole.php', array ('dati' => $dati));
                    })->setName('scuole');
            
            $app->get('/notizie', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/notizie.php', array ('dati' => $dati));
                    })->setName('notizie');
            
            $app->map([ 'GET', 'POST'], '/notizia', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/notizie.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('notizie'));
                        }
                        return $response;
                    });
            
            $app->get('/liberi', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/liberi.php', array ('dati' => $dati));
                        return $response;
                    });
            
            /* Opzioni autogestione */
            $app->get('/newsletter', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/autogestioni.php', 
                                array ('dati' => $dati, 'newsletter' => true));
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('autogestioni'));
                        return $response;
                    });
            
            $app->get('/random', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/autogestioni.php', array ('dati' => $dati, 'random' => true));
                        $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('autogestioni'));
                        return $response;
                    });
            
            /* Nuovo corso */
            $app->map([ 'GET', 'POST'], '/corso', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'corsi.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $response = $response->withStatus(301)->withHeader('Location', $this->router->pathFor('corsi'));
                        }
                        return $response;
                    });
            
            /* PDF */
            $app->group('/mostra', 
                    function () use($dati) {
                        $this->get('/credenziali', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $response->withHeader('Content-type', 'application/pdf');
                                    $response = $this->renderer->render($response, 'pdf/users.php', array ('dati' => $dati));
                                    return $response;
                                });
                        
                        $this->get('/credenziali-totali', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $response->withHeader('Content-type', 'application/pdf');
                                    $response = $this->renderer->render($response, 'pdf/usersall.php', array ('dati' => $dati));
                                    return $response;
                                });
                        
                        $this->get('/barcodes', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $response->withHeader('Content-type', 'application/pdf');
                                    $response = $this->renderer->render($response, 'admin/barcodes.php', array ('dati' => $dati));
                                    return $response;
                                });
                        
                        $this->get('/corsi', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $response->withHeader('Content-type', 'application/pdf');
                                    $response = $this->renderer->render($response, 'pdf/courses.php', array ('dati' => $dati));
                                    return $response;
                                });
                        
                        $this->get('/classi', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $response->withHeader('Content-type', 'application/pdf');
                                    $response = $this->renderer->render($response, 'pdf/classes.php', array ('dati' => $dati));
                                    return $response;
                                });
                        
                        $this->get('/random', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $response->withHeader('Content-type', 'application/pdf');
                                    $response = $this->renderer->render($response, 'pdf/random.php', array ('dati' => $dati));
                                    return $response;
                                });
                        
                        $this->get('/squadre', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $response->withHeader('Content-type', 'application/pdf');
                                    $response = $this->renderer->render($response, 'pdf/squadre.php', array ('dati' => $dati));
                                    return $response;
                                });
                    });
            
            $app->get('/download/barcodes', 
                    function ($request, $response, $args) use($dati) {
                        $response = $this->renderer->render($response, 'admin/barcodes.php', array ('dati' => $dati, 'download' => true));
                        $file = 'barcode.txt';
                        $res = $app->response();
                        $res['Content-Description'] = 'File Transfer';
                        $res['Content-Type'] = 'application/octet-stream';
                        $res['Content-Disposition'] = 'attachment; filename="' . basename($file) . '"';
                        $res['Content-Transfer-Encoding'] = 'binary';
                        $res['Expires'] = '0';
                        $res['Cache-Control'] = 'must-revalidate';
                        $res['Pragma'] = 'public';
                        $res['Content-Length'] = filesize($file);
                        readfile($file);
                        unlink($file);
                        return $response;
                    });
            
            /* Blocca/abilita */
            $app->group('/cambia', 
                    function () use($dati) {
                        $this->get('/aula/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'aule.php', 
                                            array ('dati' => $dati, 'stato' => $args['id']));
                                    $response = $response->withStatus(301)->withHeader('Location', 
                                            $this->router->pathFor('aule') . '#blocked');
                                    return $response;
                                });
                        
                        $this->get('/proposta/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'proposte.php', 
                                            array ('dati' => $dati, 'stato' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('proposte') . '#blocked');
                                    }
                                    return $response;
                                });
                        
                        $this->get('/corso/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'corsi.php', 
                                            array ('dati' => $dati, 'stato' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('corsi') . '#blocked');
                                    }
                                    return $response;
                                });
                        
                        $this->get('/citazione/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'citazioni.php', 
                                            array ('dati' => $dati, 'stato' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('citazioni') . '#blocked');
                                    }
                                    return $response;
                                });
                        
                        $this->get('/tipo/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'forum/tipi.php', 
                                            array ('dati' => $dati, 'stato' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('tipi') . '#blocked');
                                    }
                                    return $response;
                                });
                        
                        $this->get('/categoria/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'forum/categorie.php', 
                                            array ('dati' => $dati, 'stato' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('categorie') . '#blocked');
                                    }
                                    return $response;
                                });
                        
                        $this->get('/articolo/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'forum/articoli.php', 
                                            array ('dati' => $dati, 'stato' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('articoli') . '#blocked');
                                    }
                                    return $response;
                                });
                        
                        $this->get('/post/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'forum/posts.php', 
                                            array ('dati' => $dati, 'stato' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('post') . '#blocked');
                                    }
                                    return $response;
                                });
                    });
            
            /* Rifiuta */
            $app->group('/rifiuta', 
                    function () use($dati) {
                        $this->get('/aula/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'aule.php', 
                                            array ('dati' => $dati, 'rifiuta' => $args['id']));
                                    $response = $response->withStatus(301)->withHeader('Location', 
                                            $this->router->pathFor('aule') . '#blocked');
                                    return $response;
                                });
                        
                        $this->get('/proposta/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'proposte.php', 
                                            array ('dati' => $dati, 'rifiuta' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('proposte') . '#blocked');
                                    }
                                    return $response;
                                });
                        
                        $this->get('/citazione/:id', 
                                function ($request, $response, $args) use($dati) {
                                    $response = $this->renderer->render($response, 'citazioni.php', 
                                            array ('dati' => $dati, 'rifiuta' => $args['id']));
                                    if (!isset($_GET['ajax'])) {
                                        $response = $response->withStatus(301)->withHeader('Location', 
                                                $this->router->pathFor('citazioni') . '#blocked');
                                    }
                                    return $response;
                                });
                    });
        }
    }
}
$app->run();
?>