<?php

namespace App\Controllers\Site;

use Core\Controller\BaseController;
use App\Services\FileUploadService;

class FileController extends BaseController
{
    public function uploadImage()
    {
        $file = $_FILES['file'];
        $uploadDir = 'images';

        if ($this->isValidImage($file)) {
            $fileUploadService = new FileUploadService($uploadDir);
            $fileUploadService->upload($file);
        } else {
            echo "Tipo de arquivo de imagem não suportado.";
        }
    }

    public function uploadPdf()
    {
        $file = $_FILES['file'];
        $uploadDir = 'pdfs';

        if ($this->isValidPdf($file)) {
            $fileUploadService = new FileUploadService($uploadDir);
            $fileUploadService->upload($file);
        } else {
            echo "Tipo de arquivo PDF não suportado.";
        }
    }

    public function uploadAvatar()
    {
        $file = $_FILES['file'];
        $uploadDir = 'avatars';

        if ($this->isValidImage($file)) {
            $fileUploadService = new FileUploadService($uploadDir);
            if ($fileUploadService->upload($file)) {
                $this->resizeImage($fileUploadService->getUploadedFilePath(), 100, 100);
                echo json_encode(['status' => true, 'filename' => basename($file['name'])]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Erro ao enviar o arquivo.']);
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'Tipo de arquivo de imagem não suportado.']);
        }
    }

    private function isValidImage($file)
    {
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($fileType, ['jpg', 'jpeg', 'png', 'gif']);
    }

    private function isValidPdf($file)
    {
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return $fileType === 'pdf';
    }

    private function resizeImage($filePath, $width = 160, $height = 160)
    {
        // Obter as dimensões da imagem original
        list($originalWidth, $originalHeight) = getimagesize($filePath);

        // Criar uma nova imagem com as dimensões desejadas
        $newImage = imagecreatetruecolor($width, $height);

        // Criar a imagem original a partir do arquivo
        $imageType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        switch ($imageType) {
            case 'jpg':
            case 'jpeg':
                $originalImage = imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $originalImage = imagecreatefrompng($filePath);
                break;
            case 'gif':
                $originalImage = imagecreatefromgif($filePath);
                break;
            default:
                echo "Tipo de imagem não suportado.";
                return false;
        }

        // Redimensionar a imagem
        imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

        // Salvar a imagem redimensionada
        switch ($imageType) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($newImage, $filePath);
                break;
            case 'png':
                imagepng($newImage, $filePath);
                break;
            case 'gif':
                imagegif($newImage, $filePath);
                break;
        }

        // Liberar memória
        imagedestroy($originalImage);
        imagedestroy($newImage);

        return true;
    }
}