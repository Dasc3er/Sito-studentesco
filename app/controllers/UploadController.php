<?php

namespace App\Controllers;

class UploadController extends \App\Core\BaseContainer
{
    public function upload($request, $response, $args)
    {
        $response = $this->view->render($response, 'products/upload.twig', $args);

        return $response;
    }

    public function saveUpload($request, $response, $args)
    {
        $storeFolder = '../../public/files';
        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $targetPath = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.$storeFolder).DIRECTORY_SEPARATOR;

            $name = $_FILES['file']['name'];
            while (file_exists($targetPath.$name)) {
                $name = str_replace('.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION), '', basename($_FILES['file']['name'])).rand(0, 99999).'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            }

            $targetFile = $targetPath.$name;
            move_uploaded_file($tempFile, $targetFile);

            $this->database->insert('files', array('name' => $name));
        }

        return $response;
    }
}
