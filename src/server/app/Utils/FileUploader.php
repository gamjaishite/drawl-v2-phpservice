<?php

require_once __DIR__ . '/../Exception/FileUploaderException.php';

class FileUploader
{
    public string $id;
    public int $maxFilenameSize = 255;
    public int $maxFileSize = 200000;
    public string $targetDir = '.';
    public array $allowedExtTypes = ["jpg", "jpeg", "png", "webp"];
    public array $allowedMimeTypes = ["image/jpeg", "image/png", "image/webp"];

    public function __construct($id, $targetDir)
    {
        $this->id = $id;
        $this->targetDir = $targetDir;
    }
    public function uploadFie($file, $filename)
    {
        if ($file == null) {
            throw new FileUploaderException($this->id . " File is required.");
        }

        $this->validateFile($file);
        $isImage = in_array("image/jpeg", $this->allowedMimeTypes) || in_array("image/png", $this->allowedMimeTypes);
        if ($isImage) {
            $fileext = "webp";
        } else {
            $fileext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        }
        $filename = UUIDGenerator::uuid4() . '.' . $fileext;
        $targetFile = $this->targetDir . $filename;

        if (file_exists($targetFile)) {
            throw new FileUploaderException($this->id . " File already exists.");
        }


        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $filename;
        }
        throw new FileUploaderException($this->id . " File is not a valid upload file.");
    }

    private function validateFile($file)
    {
        $this->checkFileUploadedExt($file);
        $this->checkFileUploadedSize($file);
        $this->checkFileUploadedMimeType($file);
    }

    private function checkFileUploadedExt($file)
    {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtTypes)) {
            throw new FileUploaderException($this->id . " File does not have a valid extension. Only allowed " . implode(", ", $this->allowedExtTypes) . ".");
        }
    }

    private function checkFileUploadedSize($file)
    {
        if ($file["size"] > $this->maxFileSize) {
            throw new FileUploaderException($this->id . " File is too big. Maximum " . $this->maxFileSize . " bytes.");
        }
    }

    private function checkFileUploadedMimeType($file)
    {
        $mimeType = $file['type'];

        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            throw new FileUploaderException($this->id . " File does not have a valid mime type.");
        }
    }
}