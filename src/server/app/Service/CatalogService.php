<?php

require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Model/CatalogCreateResponse.php';
require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Utils/FileUploader.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

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
        $this->trailerUploader->maxFileSize = 100000000;
    }

    public function findAll(int $page = 1, string $category = "MIXED"): array
    {
        $filter = [];
        if ($category != "MIXED") {
            $filter['category'] = strtoupper(trim($category));
        }

        $projection = ['id', 'uuid', 'title', 'category', 'description', 'poster'];
        $catalogs = $this->catalogRepository->findAll($filter, [], $projection, $page);
        return $catalogs;
    }

    public function findByUUID(string $uuid): ?Catalog
    {
        $catalog = $this->catalogRepository->findOne('uuid', $uuid);
        return $catalog;
    }

    public function deleteByUUID(string $uuid): void
    {
        $this->catalogRepository->deleteBy('uuid', $uuid);
    }

    public function deleteById(int $id): void
    {
        $this->catalogRepository->deleteBy('id', $id);
    }

    public function create(CatalogCreateRequest $request): CatalogCreateResponse
    {
        $this->validateCatalogCreateRequest($request);

        try {
            Database::beginTransaction();

            $catalog = new Catalog();

            $catalog->uuid = UUIDGenerator::uuid4();
            $catalog->title = trim($request->title);
            $catalog->description = trim($request->description);

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

    public function update(string $uuid, CatalogCreateRequest $request)
    {
        try {
            Database::beginTransaction();

            $catalog = $this->catalogRepository->findOne('uuid', $uuid);

            $catalog->title = trim($request->title);
            $catalog->description = trim($request->description);

            if ($request->poster && $request->poster['error'] == UPLOAD_ERR_OK) {
                $postername = $this->posterUploader->uploadFie($request->poster, $catalog->title);
                $catalog->poster = $postername;
            }

            if ($request->trailer && $request->trailer['error'] == UPLOAD_ERR_OK) {
                $trailername = $this->trailerUploader->uploadFie($request->trailer, $catalog->title);
                $catalog->trailer = $trailername;
            }

            if ($request->category != null && trim($request->category) != "") {
                $catalog->category = strtoupper(trim($request->category));
            }

            $this->catalogRepository->update($catalog);

            Database::commitTransaction();
        } catch (FileUploaderException $exception) {
            Database::rollbackTransaction();
            throw new ValidationException($exception->getMessage());
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }
}