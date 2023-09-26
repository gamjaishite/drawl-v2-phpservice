<?php

require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Model/CatalogCreateResponse.php';
require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Config/Database.php';

class CatalogService
{
    private CatalogRepository $catalogRepository;

    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    public function create(CatalogCreateRequest $request): CatalogCreateResponse
    {
        $this->validateCatalogCreateRequest($request);

        try {
            Database::beginTransaction();

            $catalog = new Catalog();

            $filename = $this->uploadFile($request->poster);

            $catalog->title = $request->title;
            $catalog->description = $request->description;
            $catalog->poster = $filename;
            $catalog->trailer = $request->trailer ? $request->trailer['name'] : null;
            $catalog->category = $request->category;

            $this->catalogRepository->save($catalog);

            $response = new CatalogCreateResponse();
            $response->catalog = $catalog;

            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function uploadFile($file): string
    {
        $filename = basename($file['name']);
        $target_dir = "assets/images/";
        $target_file = $target_dir . $filename;

        // SANITIZE FILE
        if (file_exists($target_file)) {
            // echo "Sorry, file already exists.";
            $filename = uniqid() . '-' . $filename;
            $target_file = $target_dir . $filename;
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            // echo "The file " . htmlspecialchars($filename) . " has been uploaded.";
        } else {
            // echo "Sorry, there was an error uploading your file.";
        }

        return $filename;
    }

    private function validateCatalogCreateRequest(CatalogCreateRequest $request)
    {
        if (
            $request->title == null || trim($request->title) == ""
        ) {
            throw new ValidationException("Title, description cannot be blank");
        }

        // more validations goes here
    }
}
