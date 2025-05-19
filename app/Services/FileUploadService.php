<?php

namespace App\Services;

class FileUploadService
{
    private $uploadDir;
    private $uploadedFilePath;

    public function __construct($uploadDir)
    {
        $this->uploadDir = BASE_PATH . '/storage/uploads/' . $uploadDir;
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function upload($file)
    {
        $this->uploadedFilePath = $this->uploadDir . '/' . basename($file['name']);
        
        // Tentar enviar o arquivo
        if (move_uploaded_file($file['tmp_name'], $this->uploadedFilePath)) {
            echo "O arquivo " . basename($file['name']) . " foi enviado.";
            return true;
        } else {
            echo "Desculpe, houve um erro ao enviar seu arquivo.";
            return false;
        }
    }

    public function getUploadedFilePath()
    {
        return $this->uploadedFilePath;
    }
}