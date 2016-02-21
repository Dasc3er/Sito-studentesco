<?php
$pageTitle = "Pagina non trovata";
require_once 'header.php';
echo '
            <div class="jumbotron">
                <div class="container text-center">
                    <span class="help">404</span>
                    <h2>Pagina non trovata!</h2>
                    <p>Non siamo riusciti a trovare quello che cercavi :(</p>
                    <p><a href="' . $dati['info']['root'] . '" class="btn btn-success">Torna alla home</a></p>
                </div>
            </div>';
require_once 'footer.php';
?>