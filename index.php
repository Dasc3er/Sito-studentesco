<?php
session_cache_limiter(false);
session_start();

require __DIR__ . '/utility.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
    'templates.path' => 'templates'
));

$app->notFound(function () use($options, $app) {
    $app->render('shared/404.php', array(
        'options' => $options
    ));
});

$app->get('/', function () use($options, $app) {
    $app->render('index.php', array(
        'options' => $options
    ));
})
    ->name('index');

$app->get('/contattaci', function () use($options, $app) {
    $app->render('email.php', array(
        'options' => $options
    ));
})
    ->name('contattaci');

$app->post('/contattaci', function () use($options, $app) {
    $app->render('email.php', array(
        'options' => $options
    ));
    if (fatto()) $app->redirect($app->urlFor('index'));
});

$app->get('/templates/:name+', function () use($options, $app) {
    $app->render('shared/404.php', array(
        'options' => $options
    ));
});

$app->get('/guida/:id', function ($id) use($options, $app) {
    $app->render('index.php', array(
        'options' => $options,
        'guida' => $id
    ));
});

if (! $options["debug"]) {
    $app->map('/login', function () use($options, $app) {
        $app->render('login/index.php', array(
            'options' => $options
        ));
        if (isUserAutenticate()) $app->redirect($app->urlFor('index'));
    })
        ->via('GET', 'POST');

    $app->get('/logout', function () use($options, $app) {
        $app->render('login/logout.php', array(
            'options' => $options
        ));
        $app->redirect($app->urlFor('index'));
    });

    $app->map('/verifica/:id', function ($id) use($options, $app) {
        $options["database"]->update('persone', array(
            'options' => $options,
            'verificata' => 1
        ), array(
            'options' => $options,
            'verificata' => $id
        ));
        $app->redirect($app->urlFor('index'));
    })
        ->via('GET', 'POST');

    if (isUserAutenticate()) {
        $app->get('/profilo', function () use($options, $app) {
            $app->render('user.php', array(
                'options' => $options
            ));
        });

        $app->get('/profilo/:id', function ($id) use($options, $app) {
            $app->render('user.php', array(
                'options' => $options,
                'id' => $id
            ));
        });

        $app->get('/utenti', function () use($options, $app) {
            $app->render('admin/utenti.php', array(
                'options' => $options
            ));
        })
            ->name('utenti');

        $app->get('/check', function () use($options, $app) {
            $results = $options["database"]->select('persone', array(
                'nome',
                'email',
                'verificata'
            ), array(
                'id' => $options["user"]
            ));
            if ($results != null) {
                foreach ($results as $result) {
                    if ($result['verificata'] != 1) {
                        send(decode($result['email']), $options["sito"],'Verifica email', '<p>&Egrave; necessario eseguire la verifica dell\'email inserita nel tuo account per il sito dell\'autogestione, al fine di assicurarci che siamo in grado di contattarti in caso di necessit&agrave;.</p>
                    <p>Clicca sul link seguente o copialo nella barra del browser per completare l\'operazione.</p>
                    <p><center><a href=\'http://itiseuganeo.altervista.org/verifica/' . $result['verificata'] . '\'>http://itiseuganeo.altervista.org/verifica/' . $result['verificata'] . '<a></center></p>', $result['nome']);
                    }
                }
            }
            finito('reinvia');
            $app->redirect($app->urlFor('index'));
        })
            ->name('check');

        $app->map('/modifica', function () use($options, $app) {
            $app->render('login/modifica.php', array(
                'options' => $options
            ));
            $fatto = fatto();
            if ($options["first"] && $fatto) $app->redirect($app->urlFor('check'));
            else if ($fatto) $app->redirect($app->urlFor('index'));
        })
            ->via('GET', 'POST');

        $app->get('/aule', function () use($options, $app) {
            $app->render('aule.php', array(
                'options' => $options,
                'aula' => true
            ));
        })
            ->name('aule');

        $app->get('/aule/:id', function ($id) use($options, $app) {
            $app->render('aule.php', array(
                'options' => $options,
                'aula' => true,
                'id' => $id
            ));
            $app->redirect($app->urlFor('aule'));
        });

        $app->get('/aula/:id', function ($id) use($options, $app) {
            $app->render('aule.php', array(
                'options' => $options,
                'aula' => true,
                'view' => $id
            ));
        });

        $app->map('/aula', function () use($options, $app) {
            $app->render('aule.php', array(
                'options' => $options,
                'aula' => true,
                'new' => true
            ));
            if (fatto()) $app->redirect($app->urlFor('aule'));
        })
            ->via('GET', 'POST');
    }
    else {
        $app->map('/recupero', function () use($options, $app) {
            $app->render('login/index.php', array(
                'options' => $options,
                'recupero' => true
            ));
            if (fatto()) $app->redirect($app->urlFor('index'));
        })
            ->via('GET', 'POST');

        $app->map('/recupero/:id', function ($id) use($options, $app) {
            $app->render('login/modifica.php', array(
                'options' => $options,
                'rand' => $id
            ));
            if (fatto()) $app->redirect($app->urlFor('index'));
        })
            ->via('GET', 'POST');
    }

    if (isAdminUserAutenticate()) {
        $app->get('/autogestioni', function () use($options, $app) {
            $app->render('admin/autogestioni.php', array(
                'options' => $options
            ));
        })
            ->name('autogestioni');

        $app->map('/autogestione', function () use($options, $app) {
            $app->render('admin/autogestioni.php', array(
                'options' => $options,
                'new' => true
            ));
            if (fatto()) $app->redirect($app->urlFor('autogestioni'));
        })
            ->via('GET', 'POST');

        $app->get('/presente/:id/:corso', function ($id, $corso) use($options, $app) {
            $app->render('corsi.php', array(
                'options' => $options,
                'presente' => true,
                'persona' => $id,
                'corso' => $corso
            ));
            $app->redirect($app->urlFor('index') . '/corso/' . $corso);
        });

        $app->get('/admin', function () use($options, $app) {
            $app->render('admin/index.php', array(
                'options' => $options
            ));
        });

        $app->get('/credenziali', function () use($options, $app) {
            $app->response->headers->set('Content-Type', 'application/pdf');
            $app->render('pdf/users.php', array(
                'options' => $options
            ));
        });

        $app->get('/credenzialitot', function () use($options, $app) {
            $app->response->headers->set('Content-Type', 'application/pdf');
            $app->render('pdf/usersall.php', array(
                'options' => $options
            ));
        });

        $app->get('/barcodes', function () use($options, $app) {
            $app->response->headers->set('Content-Type', 'application/pdf');
            $app->render('admin/barcodes.php', array(
                'options' => $options
            ));
        });

        $app->get('/rand', function () use($options, $app) {
            $app->response->headers->set('Content-Type', 'application/pdf');
            $app->render('pdf/random.php', array(
                'options' => $options
            ));
        });

        $app->get('/squa', function () use($options, $app) {
            $app->response->headers->set('Content-Type', 'application/pdf');
            $app->render('pdf/squadre.php', array(
                'options' => $options
            ));
        });

        $app->get('/bar', function () use($options, $app) {
            $app->render('admin/barcodes.php', array(
                'options' => $options,
                "download" => true
            ));
            $file = "barcode.txt";
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
        });

        $app->get('/ra', function () use($options, $app) {
            $app->render('pdf/classes.php', array(
                'options' => $options,
                "download" => true
            ));
            $file = "random.txt";
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
        });

        $app->get('/accetta/:id', function ($id) use($options, $app) {
            $app->render('aule.php', array(
                'options' => $options,
                'stato' => $id
            ));
            $app->redirect($app->urlFor('aule') . '#blocked');
        });

        $app->get('/sospendi/:id', function ($id) use($options, $app) {
            $app->render('aule.php', array(
                'options' => $options,
                'cambia' => $id
            ));
            $app->redirect($app->urlFor('aule') . '#blocked');
        });

        $app->map('/aggiorna', function () use($options, $app) {
            $app->render('admin/aggiorna.php', array(
                'options' => $options
            ));
            if (fatto()) $app->redirect($app->urlFor('index'));
        })
            ->via('GET', 'POST');

        $app->get('/utenti/reset/:id', function ($id) use($options, $app) {
            $app->render('admin/utenti.php', array(
                'options' => $options,
                'reset' => $id
            ));
            $app->redirect($app->urlFor('utenti'));
        });

        $app->get('/accessi', function () use($options, $app) {
            $app->render('admin/accessi.php', array(
                'options' => $options
            ));
        })
            ->name('accessi');

        $app->get('/reset/accessi', function () use($options, $app) {
            $app->render('admin/accessi.php', array(
                'options' => $options,
                'reset' => true
            ));
            $app->redirect($app->urlFor('accessi'));
        });

        $app->get('/sessioni', function () use($options, $app) {
            $app->render('admin/sessioni.php', array(
                'options' => $options
            ));
        })
            ->name('sessions');

        $app->get('/reset/sessioni', function () use($options, $app) {
            $app->render('admin/sessioni.php', array(
                'options' => $options,
                'reset' => true
            ));
            $app->redirect($app->urlFor('sessions'));
        });

        $app->get('/scuole', function () use($options, $app) {
            $app->render('admin/scuole.php', array(
                'options' => $options
            ));
        })
            ->name('scuole');
    }

    if (isUserAutenticate() && ! $options["first"] && $options["autogestione"] != null) {
        $app->get('/barcode', function () use($options, $app) {
            $app->response->headers->set('Content-Type', 'image/gif');
            $app->render('barcode/gif.php', array(
                'options' => $options,
                "id" => $options["user"]
            ));
        });

        $app->get('/barcode/:id', function ($id) use($options, $app) {
            $app->response->headers->set('Content-Type', 'image/gif');
            $app->render('barcode/gif.php', array(
                'options' => $options,
                "id" => $id
            ));
        });

        $app->get('/barcode/:id/huge', function ($id) use($options, $app) {
            $app->response->headers->set('Content-Type', 'image/gif');
            $app->render('barcode/gif.php', array(
                'options' => $options,
                "id" => $id,
                "huge" => true
            ));
        });

        $app->map('/barcode', function () use($options, $app) {
            $app->render('login/.php', array(
                'options' => $options,
                'info' => true
            ));
            if (fatto()) $app->redirect($app->urlFor('index'));
        })
            ->via('GET', 'POST');

        $app->map('/impostazioni', function () use($options, $app) {
            $app->render('login/modifica.php', array(
                'options' => $options,
                'info' => true
            ));
            if (fatto()) $app->redirect($app->urlFor('index'));
        })
            ->via('GET', 'POST');

        $app->map('/email', function () use($options, $app) {
            $app->render('login/modifica.php', array(
                'options' => $options,
                'email' => true
            ));
            if (fatto()) $app->redirect($app->urlFor('check'));
        })
            ->via('GET', 'POST');

        $app->get('/corsi', function () use($options, $app) {
            $app->render('corsi.php', array(
                'options' => $options
            ));
        })
            ->name('corsi');

        $app->get('/corsi/:id', function ($id) use($options, $app) {
            $app->render('corsi.php', array(
                'options' => $options,
                'id' => $id
            ));
            $app->redirect($app->urlFor('corsi'));
        });

        $app->get('/corso/:id', function ($id) use($options, $app) {
            $app->render('corsi.php', array(
                'options' => $options,
                'view' => $id
            ));
        });

        $app->get('/presenze', function () use($options, $app) {
            $app->render('corsi.php', array(
                'options' => $options,
                'presenze' => true
            ));
        });

        $app->get('/proposte', function () use($options, $app) {
            $app->render('proposte.php', array(
                'options' => $options
            ));
        })
            ->name('proposte');

        $app->get('/proposte/:id', function ($id) use($options, $app) {
            $app->render('proposte.php', array(
                'options' => $options,
                'id' => $id
            ));
            $app->redirect($app->urlFor('proposte'));
        });

        $app->map('/proposta', function () use($options, $app) {
            $app->render('proposte.php', array(
                'options' => $options,
                'new' => true
            ));
            if (fatto()) $app->redirect($app->urlFor('proposte'));
        })
            ->via('GET', 'POST');

        $app->get('/citazioni', function () use($options, $app) {
            $app->render('citazioni.php', array(
                'options' => $options,
                'citazione' => true
            ));
        })
            ->name('citazioni');

        $app->get('/citazioni/:id', function ($id) use($options, $app) {
            $app->render('citazioni.php', array(
                'options' => $options,
                'citazione' => true,
                'id' => $id
            ));
            $app->redirect($app->urlFor('citazioni'));
        });

        $app->get('/citazione/:id', function ($id) use($options, $app) {
            $app->render('citazioni.php', array(
                'options' => $options,
                'citazione' => true,
                'view' => $id
            ));
        });

        $app->map('/citazione', function () use($options, $app) {
            $app->render('citazioni.php', array(
                'options' => $options,
                'citazione' => true,
                'new' => true
            ));
            if (fatto()) $app->redirect($app->urlFor('citazioni'));
        })
            ->via('GET', 'POST');

        $app->get('/squadra/:id', function ($id) use($options, $app) {
            $app->render('squadra.php', array(
                'options' => $options,
                'id' => $id
            ));
        });

        $app->get('/giocatori/:id', function ($id) use($options, $app) {
            $app->render('squadra.php', array(
                'options' => $options,
                'gioca' => $id
            ));
        });

        $app->map('/squadra', function () use($options, $app) {
            $app->render('squadra.php', array(
                'options' => $options,
                'new' => 1
            ));
            if (fatto()) $app->redirect($app->urlFor('corsi'));
        })
            ->via('GET', 'POST');

        $app->map('/edit/:id', function ($id) use($options, $app) {
            $app->render('squadra.php', array(
                'options' => $options,
                'edit' => $id
            ));
            if (fatto()) $app->redirect($app->urlFor('corsi'));
        })
            ->via('GET', 'POST');

        $app->get('/delete/:id', function ($id) use($options, $app) {
            $app->render('squadra.php', array(
                'options' => $options,
                'id' => $id,
                'delete' => ''
            ));
        });

        $app->get('/delete/yes/:id', function ($id) use($options, $app) {
            $app->render('squadra.php', array(
                'options' => $options,
                'id' => $id,
                'delete' => 'yes'
            ));
            $app->redirect($app->urlFor('corsi'));
        });

        $app->get('/aggiungi/:id/:add', function ($id, $add) use($options, $app) {
            $app->render('squadra.php', array(
                'options' => $options,
                'gioca' => $id,
                'add' => $add
            ));
            $app->redirect($app->urlFor('index') . 'giocatori/' . squadra($options["database"], $options["user"]));
        });

        $app->get('/rimuovi/:id/:remove', function ($id, $remove) use($options, $app) {
            $app->render('squadra.php', array(
                'options' => $options,
                'gioca' => $id,
                'remove' => $remove
            ));
            $app->redirect($app->urlFor('index') . 'giocatori/' . squadra($options["database"], $options["user"]));
        });

        $app->get('/pdf', function () use($options, $app) {
            $app->response->headers->set('Content-Type', 'application/pdf');
            $app->render('pdf/user.php', array(
                'options' => $options
            ));
        });

        if (isAdminUserAutenticate()) {
            $app->get('/notizie', function () use($options, $app) {
                $app->render('admin/notizie.php', array(
                    'options' => $options
                ));
            })
                ->name('notizie');

            $app->map('/notizia', function () use($options, $app) {
                $app->render('admin/notizie.php', array(
                    'options' => $options,
                    'new' => true
                ));
                if (fatto()) $app->redirect($app->urlFor('notizie'));
            })
                ->via('GET', 'POST');

            $app->get('/schedecorsi', function () use($options, $app) {
                $app->response->headers->set('Content-Type', 'application/pdf');
                $app->render('pdf/courses.php', array(
                    'options' => $options
                ));
            });

            $app->get('/schedeclassi', function () use($options, $app) {
                $app->response->headers->set('Content-Type', 'application/pdf');
                $app->render('pdf/classes.php', array(
                    'options' => $options
                ));
            });

            $app->get('/liberi', function () use($options, $app) {
                $app->render('admin/liberi.php', array(
                    'options' => $options
                ));
            });

            $app->get('/random', function () use($options, $app) {
                $app->render('admin/autogestioni.php', array(
                    'options' => $options,
                    'random' => true
                ));
                $app->redirect($app->urlFor('autogestioni'));
            });

            $app->get('/newsletter', function () use($options, $app) {
                $app->render('admin/autogestioni.php', array(
                    'options' => $options,
                    'newsletter' => true
                ));
                $app->redirect($app->urlFor('autogestioni'));
            });

            $app->map('/corso', function () use($options, $app) {
                $app->render('corsi.php', array(
                    'options' => $options,
                    'new' => true
                ));
                if (fatto()) $app->redirect($app->urlFor('corsi'));
            })
                ->via('GET', 'POST');

            $app->get('/accettare/:id', function ($id) use($options, $app) {
                $app->render('corsi.php', array(
                    'options' => $options,
                    'stato' => $id
                ));
                $app->redirect($app->urlFor('proposte') . '#blocked');
            });

            $app->get('/stato/:id', function ($id) use($options, $app) {
                $app->render('corsi.php', array(
                    'options' => $options,
                    'stato' => $id
                ));
                $app->redirect($app->urlFor('corsi') . '#blocked');
            });

            $app->get('/cit/:id', function ($id) use($options, $app) {
                $app->render('citazioni.php', array(
                    'options' => $options,
                    'stato' => $id
                ));
                $app->redirect($app->urlFor('citazioni') . '#blocked');
            });

            $app->get('/blocca/:id', function ($id) use($options, $app) {
                $app->render('proposte.php', array(
                    'options' => $options,
                    'cambia' => $id
                ));
                $app->redirect($app->urlFor('proposte') . '#blocked');
            });

            $app->get('/cambia/:id', function ($id) use($options, $app) {
                $app->render('citazioni.php', array(
                    'options' => $options,
                    'cambia' => $id
                ));
                $app->redirect($app->urlFor('citazioni') . '#blocked');
            });
        }
    }
}
$app->run();

