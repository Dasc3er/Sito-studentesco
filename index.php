<?php
require __DIR__ . '/utility.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->notFound(function () use($dati, $app) {
    $app->render('shared/404.php', array ('dati' => $dati));
});

$app->get('/', function () use($dati, $app) {
    $app->render('index.php', array ('dati' => $dati));
})->name('index');

$app->map('/contattaci', 
        function () use($dati, $app) {
            $app->render('email.php', array ('dati' => $dati));
            if (fatto()) {
                $app->redirect($app->urlFor('index'));
            }
        })->via('GET', 'POST');

$app->map('/templates(/:name+)', 
        function ($name) use($dati, $app) {
            $app->render('shared/404.php', array ('dati' => $dati));
        })->via('GET', 'POST');

$app->get('/guida/:id', 
        function ($id) use($dati, $app) {
            $app->render('index.php', array ('dati' => $dati, 'guida' => $id));
        });

$app->get('/logout', 
        function () use($dati, $app) {
            $app->render('login/logout.php', array ('dati' => $dati));
            $app->redirect($app->urlFor('index'));
        });

if (!$dati['debug'] || isAdminUserAutenticate()) {
    $app->map('/login', 
            function () use($dati, $app) {
                $app->render('login/index.php', array ('dati' => $dati));
                if (isUserAutenticate()) {
                    $app->redirect($app->urlFor('index'));
                }
            })->via('GET', 'POST');
    
    $app->map('/verifica/:id', 
            function ($id) use($dati, $app) {
                $dati['database']->update('persone', array ('verificata' => 1), array ('verificata' => $id));
                $app->redirect($app->urlFor('index'));
            })->via('GET', 'POST');
    
    if (isUserAutenticate()) {
        $app->get('/profilo', 
                function () use($dati, $app) {
                    $app->render('user.php', array ('dati' => $dati));
                });
        
        $app->get('/check', 
                function () use($dati, $app) {
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
                    $app->redirect($app->urlFor('index'));
                })->name('check');
        
        $app->map('/modifica', 
                function () use($dati, $app) {
                    $app->render('login/modifica.php', array ('dati' => $dati));
                    $fatto = fatto();
                    if ($dati['first'] && $fatto) {
                        $app->redirect($app->urlFor('check'));
                    }
                    elseif ($fatto) {
                        $app->redirect($app->urlFor('index'));
                    }
                })->via('GET', 'POST');
        
        if (isAdminUserAutenticate()) {
            $app->get('/autogestioni', 
                    function () use($dati, $app) {
                        $app->render('admin/autogestioni.php', array ('dati' => $dati));
                    })->name('autogestioni');
            
            $app->map('/autogestione', 
                    function () use($dati, $app) {
                        $app->render('admin/autogestioni.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $app->redirect($app->urlFor('autogestioni'));
                        }
                    })->via('GET', 'POST');
        }
    }
    else {
        $app->map('/recupero', 
                function () use($dati, $app) {
                    $app->render('login/index.php', array ('dati' => $dati, 'recupero' => true));
                    if (fatto()) {
                        $app->redirect($app->urlFor('index'));
                    }
                })->via('GET', 'POST');
        
        $app->map('/recupero/:id', 
                function ($id) use($dati, $app) {
                    $app->render('login/modifica.php', array ('dati' => $dati, 'rand' => $id));
                    if (fatto()) {
                        $app->redirect($app->urlFor('index'));
                    }
                })->via('GET', 'POST');
    }
    
    if (isUserAutenticate() && !$dati['first'] && $dati['autogestione'] != null) {
        $app->map('/impostazioni', 
                function () use($dati, $app) {
                    $app->render('login/modifica.php', array ('dati' => $dati, 'info' => true));
                    if (fatto()) {
                        $app->redirect($app->urlFor('index'));
                    }
                })->via('GET', 'POST');
        
        $app->map('/email', 
                function () use($dati, $app) {
                    $app->render('login/modifica.php', array ('dati' => $dati, 'email' => true));
                    if (fatto()) {
                        $app->redirect($app->urlFor('check'));
                    }
                })->via('GET', 'POST');
        
        if ($dati['sezioni']['forum']) {
            /* Forum */
            $app->get('/forum', 
                    function () use($dati, $app) {
                        $app->render('forum/index.php', array ('dati' => $dati));
                    });
            
            $app->get('/posts', 
                    function () use($dati, $app) {
                        $app->render('forum/posts.php', array ('dati' => $dati, 'user' => $dati['user']));
                    })->name('post');
            
            $app->get('/posts/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('forum/posts.php', array ('dati' => $dati, 'articolo' => $id, 'user' => $dati['user']));
                    });
            
            $app->get('/tipo/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('forum/tipi.php', array ('dati' => $dati, 'id' => $id));
                    });
            
            $app->get('/categoria/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('forum/categorie.php', array ('dati' => $dati, 'id' => $id));
                    });
            
            $app->map('/articolo/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('forum/articoli.php', array ('dati' => $dati, 'id' => $id));
                        if (fatto()) {
                            $app->redirect($app->urlFor('articoli'));
                        }
                    })->via('GET', 'POST');
            
            $app->get('/articoli', 
                    function () use($dati, $app) {
                        $app->render('forum/articoli.php', array ('dati' => $dati));
                    })->name('articoli');
            
            $app->get('/categorie', 
                    function () use($dati, $app) {
                        $app->render('forum/categorie.php', array ('dati' => $dati));
                    })->name('categorie');
            
            $app->get('/tipi', 
                    function () use($dati, $app) {
                        $app->render('forum/tipi.php', array ('dati' => $dati));
                    })->name('tipi');
            
            $app->group('/nuovo', 
                    function () use($dati, $app) {
                        $app->map('/articolo', 
                                function () use($dati, $app) {
                                    $app->render('forum/articoli.php', array ('dati' => $dati, 'new' => true));
                                    if (fatto()) {
                                        $app->redirect($app->urlFor('articoli'));
                                    }
                                })->via('GET', 'POST');
                        
                        $app->map('/articolo/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('forum/articoli.php', array ('dati' => $dati, 'new' => true, 'categoria' => $id));
                                    if (fatto()) {
                                        $app->redirect($app->urlFor('articoli'));
                                    }
                                })->via('GET', 'POST');
                        
                        if (isAdminUserAutenticate()) {
                            $app->map('/categoria', 
                                    function () use($dati, $app) {
                                        $app->render('forum/categorie.php', array ('dati' => $dati, 'new' => true));
                                        if (fatto()) {
                                            $app->redirect($app->urlFor('categorie'));
                                        }
                                    })->via('GET', 'POST');
                            
                            $app->map('/categoria/:id', 
                                    function ($id) use($dati, $app) {
                                        $app->render('forum/categorie.php', array ('dati' => $dati, 'new' => true, 'tipo' => $id));
                                        if (fatto()) {
                                            $app->redirect($app->urlFor('categorie'));
                                        }
                                    })->via('GET', 'POST');
                            
                            $app->map('/tipo', 
                                    function () use($dati, $app) {
                                        $app->render('forum/tipi.php', array ('dati' => $dati, 'new' => true));
                                        if (fatto()) {
                                            $app->redirect($app->urlFor('tipi'));
                                        }
                                    })->via('GET', 'POST');
                        }
                    });
            
            $app->group('/modifica', 
                    function () use($dati, $app) {
                        $app->map('/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('forum/posts.php', array ('dati' => $dati, 'edit' => $id));
                                    if (fatto()) {
                                        $app->redirect($app->urlFor('articoli'));
                                    }
                                })->via('GET', 'POST');
                        $app->map('/articolo/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('forum/articoli.php', array ('dati' => $dati, 'edit' => $id));
                                    if (fatto()) {
                                        $app->redirect($app->urlFor('articoli'));
                                    }
                                })->via('GET', 'POST');
                        
                        if (isAdminUserAutenticate()) {
                            $app->map('/categoria/:id', 
                                    function ($id) use($dati, $app) {
                                        $app->render('forum/categorie.php', array ('dati' => $dati, 'edit' => $id));
                                        if (fatto()) {
                                            $app->redirect($app->urlFor('categorie'));
                                        }
                                    })->via('GET', 'POST');
                            
                            $app->map('/tipo/:id', 
                                    function ($id) use($dati, $app) {
                                        $app->render('forum/tipi.php', array ('dati' => $dati, 'edit' => $id));
                                        if (fatto()) {
                                            $app->redirect($app->urlFor('tipi'));
                                        }
                                    })->via('GET', 'POST');
                        }
                    });
        }
        
        if ($dati['sezioni']['felpa'] && permesso("felpa")) {
            $app->get('/elimina/felpa/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('felpa.php', array ('dati' => $dati, 'elimina' => $id));
                        $app->redirect($app->urlFor('felpa'));
                    });
            
            $app->map('/modifica/felpa/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('felpa.php', array ('dati' => $dati, 'modifica' => $id));
                        if (fatto()) {
                            $app->redirect($app->urlFor('felpa'));
                        }
                    })->via('GET', 'POST');
            
            $app->map('/nuovo/felpa', 
                    function () use($dati, $app) {
                        $app->render('felpa.php', array ('dati' => $dati, 'nuovo' => true));
                        if (fatto()) {
                            $app->redirect($app->urlFor('felpa'));
                        }
                    })->via('GET', 'POST');
            
            $app->get('/felpa', 
                    function () use($dati, $app) {
                        $app->render('felpa.php', array ('dati' => $dati));
                    })->name('felpa');
        }
        
        if ($dati['sezioni']['corsi'] && permesso("autogestione")) {
            /* Corsi */
            $app->get('/corsi', 
                    function () use($dati, $app) {
                        $app->render('corsi.php', array ('dati' => $dati));
                    })->name('corsi');
            
            $app->get('/:autogestione/corsi', 
                    function ($autogestione) use($dati, $app) {
                        $app->render('corsi.php', array ('dati' => $dati, 'autogestione' => $autogestione));
                    });
            
            $app->get('/corsi/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('corsi.php', array ('dati' => $dati, 'id' => $id));
                        if (!isset($_GET['ajax'])) {
                            $app->redirect($app->urlFor('corsi'));
                        }
                    });
            
            $app->get('/corso/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('corsi.php', array ('dati' => $dati, 'view' => $id));
                    });
            
            $app->get('/presenze', 
                    function () use($dati, $app) {
                        $app->render('corsi.php', array ('dati' => $dati, 'presenze' => true));
                    });
            
            /* Squadre */
            $app->get('/squadra/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('squadra.php', array ('dati' => $dati, 'id' => $id));
                    });
            
            $app->get('/giocatori/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('squadra.php', array ('dati' => $dati, 'gioca' => $id));
                    });
            
            $app->map('/squadra', 
                    function () use($dati, $app) {
                        $app->render('squadra.php', array ('dati' => $dati, 'new' => 1));
                        if (fatto()) {
                            $app->redirect($app->urlFor('corsi'));
                        }
                    })->via('GET', 'POST');
            
            $app->map('/edit/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('squadra.php', array ('dati' => $dati, 'edit' => $id));
                        if (fatto()) {
                            $app->redirect($app->urlFor('corsi'));
                        }
                    })->via('GET', 'POST');
            
            $app->get('/delete/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('squadra.php', array ('dati' => $dati, 'id' => $id, 'delete' => ''));
                    });
            
            $app->get('/delete/yes/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('squadra.php', array ('dati' => $dati, 'id' => $id, 'delete' => 'yes'));
                        $app->redirect($app->urlFor('corsi'));
                    });
            
            $app->get('/aggiungi/:id/:add', 
                    function ($id, $add) use($dati, $app) {
                        $app->render('squadra.php', array ('dati' => $dati, 'gioca' => $id, 'add' => $add));
                        $app->redirect(
                                $app->urlFor('index') . 'giocatori/' . squadra($dati['database'], $dati['autogestione'], $dati['user']));
                    });
            
            $app->get('/rimuovi/:id/:remove', 
                    function ($id, $remove) use($dati, $app) {
                        $app->render('squadra.php', array ('dati' => $dati, 'gioca' => $id, 'remove' => $remove));
                        $app->redirect(
                                $app->urlFor('index') . 'giocatori/' . squadra($dati['database'], $dati['autogestione'], $dati['user']));
                    });
            
            /* Proposte */
            $app->get('/proposte', 
                    function () use($dati, $app) {
                        $app->render('proposte.php', array ('dati' => $dati));
                    })->name('proposte');
            
            $app->get('/:autogestione/proposte', 
                    function ($autogestione) use($dati, $app) {
                        $app->render('proposte.php', array ('dati' => $dati, 'autogestione' => $autogestione));
                    });
            $app->get('/proposte/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('proposte.php', array ('dati' => $dati, 'id' => $id));
                        if (!isset($_GET['ajax'])) {
                            $app->redirect($app->urlFor('proposte'));
                        }
                    });
            
            $app->map('/proposta', 
                    function () use($dati, $app) {
                        $app->render('proposte.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $app->redirect($app->urlFor('proposte'));
                        }
                    })->via('GET', 'POST');
        }
        
        if ($dati['sezioni']['citazioni'] && permesso("citazioni")) {
            /* Citazioni */
            $app->get('/citazioni', 
                    function () use($dati, $app) {
                        $app->render('citazioni.php', array ('dati' => $dati, 'citazione' => true));
                    })->name('citazioni');
            
            $app->get('/citazioni/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('citazioni.php', array ('dati' => $dati, 'id' => $id));
                        if (!isset($_GET['ajax'])) {
                            $app->redirect($app->urlFor('citazioni'));
                        }
                    });
            
            $app->get('/citazione/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('citazioni.php', array ('dati' => $dati, 'view' => $id));
                    });
            
            $app->map('/citazione', 
                    function () use($dati, $app) {
                        $app->render('citazioni.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $app->redirect($app->urlFor('citazioni'));
                        }
                    })->via('GET', 'POST');
        }
        
        if ($dati['sezioni']['aule']) {
            /* Aule studio */
            $app->get('/aule', 
                    function () use($dati, $app) {
                        $app->render('aule.php', array ('dati' => $dati, 'aula' => true));
                    })->name('aule');
            
            $app->get('/aule/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('aule.php', array ('dati' => $dati, 'id' => $id));
                        if (!isset($_GET['ajax'])) {
                            $app->redirect($app->urlFor('aule'));
                        }
                    });
            
            $app->get('/aula/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('aule.php', array ('dati' => $dati, 'view' => $id));
                    });
            
            $app->map('/aula', 
                    function () use($dati, $app) {
                        $app->render('aule.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $app->redirect($app->urlFor('aule'));
                        }
                    })->via('GET', 'POST');
        }
        
        /* Profili altrui e barcode */
        $app->get('/profilo/:id', 
                function ($id) use($dati, $app) {
                    $app->render('user.php', array ('dati' => $dati, 'id' => $id));
                });
        
        $app->get('/utenti', 
                function () use($dati, $app) {
                    $app->render('admin/utenti.php', array ('dati' => $dati));
                })->name('utenti');
        
        $app->get('/felpe', 
                function () use($dati, $app) {
                    $app->render('admin/felpe.php', array ('dati' => $dati));
                })->name('felpe');
        
        $app->get('/barcode', 
                function () use($dati, $app) {
                    $app->response->headers->set('Content-Type', 'image/gif');
                    $app->render('barcode/gif.php', array ('dati' => $dati, 'id' => $dati['user']));
                });
        
        $app->get('/barcode/:id', 
                function ($id) use($dati, $app) {
                    $app->response->headers->set('Content-Type', 'image/gif');
                    $app->render('barcode/gif.php', array ('dati' => $dati, 'id' => $id));
                });
        
        $app->get('/barcode/:id/huge', 
                function ($id) use($dati, $app) {
                    $app->response->headers->set('Content-Type', 'image/gif');
                    $app->render('barcode/gif.php', array ('dati' => $dati, 'id' => $id, 'huge' => true));
                });
        
        if (isAdminUserAutenticate()) {
            if ($dati['sezioni']['forum'] && permesso("forum")) {
                /* Forum */
                $app->get('/post', 
                        function () use($dati, $app) {
                            $app->render('forum/posts.php', array ('dati' => $dati));
                        });
                
                $app->get('/posts/:id/tutti', 
                        function ($id) use($dati, $app) {
                            $app->render('forum/posts.php', array ('dati' => $dati, 'articolo' => $id));
                        });
            }
            
            /* Amministrazione */
            $app->get('/admin', 
                    function () use($dati, $app) {
                        $app->render('admin/index.php', array ('dati' => $dati));
                    });
            
            $app->map('/aggiorna', 
                    function () use($dati, $app) {
                        $app->render('admin/aggiorna.php', array ('dati' => $dati));
                        if (fatto()) {
                            $app->redirect($app->urlFor('index'));
                        }
                    })->via('GET', 'POST');
            
            $app->get('/utenti/reset/:id', 
                    function ($id) use($dati, $app) {
                        $app->render('admin/utenti.php', array ('dati' => $dati, 'reset' => $id));
                        if (!isset($_GET['ajax'])) {
                            $app->redirect($app->urlFor('utenti'));
                        }
                    });
            
            $app->get('/accessi', 
                    function () use($dati, $app) {
                        $app->render('admin/accessi.php', array ('dati' => $dati));
                    })->name('accessi');
            
            $app->get('/reset/accessi', 
                    function () use($dati, $app) {
                        $app->render('admin/accessi.php', array ('dati' => $dati, 'reset' => true));
                        $app->redirect($app->urlFor('accessi'));
                    });
            
            $app->get('/sessioni', 
                    function () use($dati, $app) {
                        $app->render('admin/sessioni.php', array ('dati' => $dati));
                    })->name('sessions');
            
            $app->get('/reset/sessioni', 
                    function () use($dati, $app) {
                        $app->render('admin/sessioni.php', array ('dati' => $dati, 'reset' => true));
                        $app->redirect($app->urlFor('sessions'));
                    });
            
            $app->get('/scuole', 
                    function () use($dati, $app) {
                        $app->render('admin/scuole.php', array ('dati' => $dati));
                    })->name('scuole');
            
            $app->get('/notizie', 
                    function () use($dati, $app) {
                        $app->render('admin/notizie.php', array ('dati' => $dati));
                    })->name('notizie');
            
            $app->map('/notizia', 
                    function () use($dati, $app) {
                        $app->render('admin/notizie.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $app->redirect($app->urlFor('notizie'));
                        }
                    })->via('GET', 'POST');
            
            $app->get('/liberi', 
                    function () use($dati, $app) {
                        $app->render('admin/liberi.php', array ('dati' => $dati));
                    });
            
            /* Opzioni autogestione */
            $app->get('/newsletter', 
                    function () use($dati, $app) {
                        $app->render('admin/autogestioni.php', array ('dati' => $dati, 'newsletter' => true));
                        $app->redirect($app->urlFor('autogestioni'));
                    });
            
            $app->get('/random', 
                    function () use($dati, $app) {
                        $app->render('admin/autogestioni.php', array ('dati' => $dati, 'random' => true));
                        $app->redirect($app->urlFor('autogestioni'));
                    });
            
            /* Nuovo corso */
            $app->map('/corso', 
                    function () use($dati, $app) {
                        $app->render('corsi.php', array ('dati' => $dati, 'new' => true));
                        if (fatto()) {
                            $app->redirect($app->urlFor('corsi'));
                        }
                    })->via('GET', 'POST');
            
            /* PDF */
            $app->group('/mostra', 
                    function () use($dati, $app) {
                        $app->get('/credenziali', 
                                function () use($dati, $app) {
                                    $app->response->headers->set('Content-Type', 'application/pdf');
                                    $app->render('pdf/credenziali.php', array ('dati' => $dati));
                                });
                        
                        $app->get('/felpe', 
                                function () use($dati, $app) {
                                    $app->response->headers->set('Content-Type', 'application/pdf');
                                    $app->render('pdf/sommarioFelpe.php', array ('dati' => $dati));
                                });
                        
                        $app->get('/barcodes', 
                                function () use($dati, $app) {
                                    $app->response->headers->set('Content-Type', 'application/pdf');
                                    $app->render('pdf/barcodes.php', array ('dati' => $dati));
                                });
                        
                        $app->get('/corsi', 
                                function () use($dati, $app) {
                                    $app->response->headers->set('Content-Type', 'application/pdf');
                                    $app->render('pdf/sommarioCorsi.php', array ('dati' => $dati));
                                });
                        
                        $app->get('/classi', 
                                function () use($dati, $app) {
                                    $app->response->headers->set('Content-Type', 'application/pdf');
                                    $app->render('pdf/sommarioClassi.php', array ('dati' => $dati));
                                });
                        
                        $app->get('/random', 
                                function () use($dati, $app) {
                                    $app->response->headers->set('Content-Type', 'application/pdf');
                                    $app->render('pdf/random.php', array ('dati' => $dati));
                                });
                        
                        $app->get('/squadre', 
                                function () use($dati, $app) {
                                    $app->response->headers->set('Content-Type', 'application/pdf');
                                    $app->render('pdf/squadre.php', array ('dati' => $dati));
                                });
                    });
            
            $app->get('/download/barcodes', 
                    function () use($dati, $app) {
                        $app->render('pdf/barcodes.php', array ('dati' => $dati, 'download' => true));
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
                    });
            
            /* Blocca/abilita */
            $app->group('/cambia', 
                    function () use($dati, $app) {
                        $app->get('/aula/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('aule.php', array ('dati' => $dati, 'stato' => $id));
                                    $app->redirect($app->urlFor('aule') . '#blocked');
                                });
                        
                        $app->get('/proposta/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('proposte.php', array ('dati' => $dati, 'stato' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('proposte') . '#blocked');
                                    }
                                });
                        
                        $app->get('/corso/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('corsi.php', array ('dati' => $dati, 'stato' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('corsi') . '#blocked');
                                    }
                                });
                        
                        $app->get('/citazione/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('citazioni.php', array ('dati' => $dati, 'stato' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('citazioni') . '#blocked');
                                    }
                                });
                        
                        $app->get('/tipo/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('forum/tipi.php', array ('dati' => $dati, 'stato' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('tipi') . '#blocked');
                                    }
                                });
                        
                        $app->get('/categoria/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('forum/categorie.php', array ('dati' => $dati, 'stato' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('categorie') . '#blocked');
                                    }
                                });
                        
                        $app->get('/articolo/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('forum/articoli.php', array ('dati' => $dati, 'stato' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('articoli') . '#blocked');
                                    }
                                });
                        
                        $app->get('/post/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('forum/posts.php', array ('dati' => $dati, 'stato' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('post') . '#blocked');
                                    }
                                });
                    });
            
            /* Rifiuta */
            $app->group('/rifiuta', 
                    function () use($dati, $app) {
                        $app->get('/aula/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('aule.php', array ('dati' => $dati, 'rifiuta' => $id));
                                    $app->redirect($app->urlFor('aule') . '#blocked');
                                });
                        
                        $app->get('/proposta/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('proposte.php', array ('dati' => $dati, 'rifiuta' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('proposte') . '#blocked');
                                    }
                                });
                        
                        $app->get('/citazione/:id', 
                                function ($id) use($dati, $app) {
                                    $app->render('citazioni.php', array ('dati' => $dati, 'rifiuta' => $id));
                                    if (!isset($_GET['ajax'])) {
                                        $app->redirect($app->urlFor('citazioni') . '#blocked');
                                    }
                                });
                    });
        }
    }
}

$app->run();

?>