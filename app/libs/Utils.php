<?php

use \App\App;
use \App\Models;

class Utils
{
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
        $container = \App\Core\App::getContainer();
        $settings = $container['settings']['email'];

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

        $mail->Subject = $title.' - '.\App\Core\Translator::translate('base.site');
        $mail->isHTML(true); // Set email format to HTML

        $mail->setFrom($settings['default_email'], \App\Core\Translator::translate('base.site'));
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
}
