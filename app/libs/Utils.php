<?php

class Utils
{
    /**
     * Controlla che l'username inserito sia univoco.
     *
     * @param medoo  $database Database
     * @param string $username Username da controllare
     * @param int    $user_id  Identificativo dell'utente da considerare Identificativo utente
     *
     * @return bool Username libero
     */
    public static function isUsernameFree($username)
    {
        $where = [['username', '=', $username]];

        $auth = \App\Core\AppContainer::container()->auth;
        if ($auth->check()) {
            array_push($where, ['id', '!=', $auth->user()->id]);
        }

        return \App\Models\User::where($where)->count() == 0;
    }

    /**
     * Controlla che l'indirizzo email inserita sia univoco.
     *
     * @param medoo  $database Connessione con il database
     * @param string $email    Email da controllare
     * @param int    $user_id  Identificativo dell'utente da considerare Identificativo utente
     *
     * @return bool Email libera
     */
    public static function isEmailFree($email)
    {
        $where = [['email', '=', $email]];

        $auth = \App\Core\AppContainer::container()->auth;
        if ($auth->check()) {
            array_push($where, ['id', '!=', $auth->user()->id]);
        }

        return \App\Models\User::where($where)->count() == 0;
    }

    /**
     * Invia email.
     *
     * @param string $receiver
     * @param string $sito
     * @param string $title
     * @param string $content
     */
    public static function send($receiver, $title, $body, $name = null)
    {
        $container = \App\Core\AppContainer::container();
        $settings = $container['settings'];

        $mail = new PHPMailer();

        if ($settings['email']['auth']) {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = $settings['host'];
            $mail->Username = $settings['username'];
            $mail->Password = $settings['password'];
            $mail->SMTPSecure = $settings['secure'];
            $mail->Port = $settings['port'];
        }

        $mail->Subject = $title.' - '.\App\Core\Translator::translate('email.site');
        $mail->isHTML(true); // Set email format to HTML

        $mail->setFrom($settings['email']['default_email'], \App\Core\Translator::translate('base.site'));
        $mail->addAddress($receiver);

        $mail->Body = $container['view']->fetch('email.twig', ['title' => $title, 'body' => $body, 'name' => $name]);
        $mail->AltBody = strip_tags(str_replace(['</p>', '<br>'], '\n', $mail->Body));

        if (empty($settings['debug']['enable'])) {
            if (!$mail->send()) {
                $container['flash']->addMessage('errors', \App\Core\Translator::translate('email.email-error').'<br>'.$mail->ErrorInfo);
            } else {
                $container['flash']->addMessage('infos', \App\Core\Translator::translate('email.email-sent'));
            }
        }
    }

    public static function sendEmail($receiver, $email, $array = [])
    {
        $p = 'email.'.$email.'.p';

        $body = [];
        for ($i = 1; \App\Core\Translator::translate($p.$i, $array) != $p.$i; ++$i) {
            array_push($body, \App\Core\Translator::translate($p.$i, $array));
        }

        \Utils::send($receiver, \App\Core\Translator::translate('email.'.$email.'.title'), $body);
    }

    /**
     * Esegue una ricerca binaria dell'elemento $elemento nel campo $where dell'array.
     * Necessita un'array ordinato!!!
     *
     * @param mixed[] $array
     * @param mixed   $elemento
     * @param string  $where    Campo di ricerca
     *
     * @return int Posizione dell'elemento
     */
    public static function ricercaBinaria($array, $elemento, $where = 'id')
    {
        $start = 0;
        $end = count($array) - 1;
        $centro = 0;
        while ($start <= $end) {
            $centro = intval(($start + $end) / 2);
            if ($elemento < $array[$centro][$where]) {
                $end = $centro - 1;
            } else {
                if ($elemento > $array[$centro][$where]) {
                    $start = $centro + 1;
                } else {
                    return $centro;
                }
            }
        }

        return -1;
    }

    public static function createKey()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    /**
     * Copy from http://www.php.net/manual/en/function.array-merge-recursive.php#92195.
     *
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     *
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    public static function array_merge(array &$array1, array &$array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = \Utils::array_merge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
