<?php

require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Model/CatalogCreateResponse.php';
require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Utils/FileUploader.php';

class CatalogService
{
    private CatalogRepository $catalogRepository;
    private FileUploader $posterUploader;
    private FileUploader $trailerUploader;

    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
        $this->posterUploader = new FileUploader('Poster', 'assets/images/catalogs/posters/');
        $this->trailerUploader = new FileUploader('Trailer', 'assets/videos/catalogs/trailers/');

        $this->trailerUploader->allowedExtTypes = ["mp4"];
        $this->trailerUploader->allowedMimeTypes = ["video/mp4"];
    }

    public function findAll(int $page = 1, string $category = "MIXED"): array
    {
        $filter = [];
        if ($category != "MIXED") {
            $filter['category'] = strtoupper(trim($category));
        }
        $catalogs = $this->catalogRepository->findAll($filter, $page);
        return [
            'items' => $catalogs,
            'page' => $page,
            'totalPage' => $this->catalogRepository->countPage()
        ];
    }

    public function create(CatalogCreateRequest $request): CatalogCreateResponse
    {
        $this->validateCatalogCreateRequest($request);

        try {
            Database::beginTransaction();

            $catalog = new Catalog();

            $catalog->uuid = uniqid();
            $catalog->title = $request->title;
            $catalog->description = $request->description;

            $postername = $this->posterUploader->uploadFie($request->poster, $catalog->title);
            if ($request->trailer && $request->trailer['error'] == UPLOAD_ERR_OK) {
                $trailername = $this->trailerUploader->uploadFie($request->trailer, $catalog->title);
            }

            $catalog->poster = $postername;
            $catalog->trailer = $trailername ?? null;
            $catalog->category = strtoupper(trim($request->category));

            $this->catalogRepository->save($catalog);

            $response = new CatalogCreateResponse();
            $response->catalog = $catalog;

            Database::commitTransaction();
            return $response;
        } catch (FileUploaderException $exception) {
            Database::rollbackTransaction();
            throw new ValidationException($exception->getMessage());
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCatalogCreateRequest(CatalogCreateRequest $request)
    {
        if (
            $request->title == null || trim($request->title) == ""
        ) {
            throw new ValidationException("Title cannot be blank.");
        }

        if ($request->category == null || trim($request->category) == "") {
            throw new ValidationException("Category cannot be blank.");
        }

        if ($request->poster == null || $request->poster['error'] != UPLOAD_ERR_OK) {
            throw new ValidationException("Poster cannot be blank.");
        }
    }
}