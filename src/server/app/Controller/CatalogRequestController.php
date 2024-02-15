<?php

require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

require_once __DIR__ . '/../Service/CatalogService.php';
require_once __DIR__ . '/../Service/SessionService.php';

require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/SessionRepository.php';

require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Model/catalog/CatalogUpdateRequest.php';
require_once __DIR__ . '/../Model/CatalogSearchRequest.php';

require_once __DIR__ . '/../Utils/SOAPRequest.php';
require_once __DIR__ . '/../Utils/GetRequestHeader.php';

require_once __DIR__ . '/../Utils/UUIDGenerator.php';
require_once __DIR__ . '/../Utils/FileUploader.php';

class CatalogRequestController
{
    private CatalogService $catalogService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $catalogRepository = new CatalogRepository($connection);
        $this->catalogService = new CatalogService($catalogRepository);
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    public function request(): void
    {
        View::render('catalog/form', [
            'title' => 'Request Catalog',
            'styles' => [
                '/css/catalog-form.css',
            ],
            'js' => [
                '/js/catalog/createRequest.js'
            ],
            'type' => 'create'
        ], $this->sessionService);
    }

    public function create()
    {
        $posterUploader = new FileUploader('Poster', 'assets/images/catalogs/posters/');
        $trailerUploader = new FileUploader('Trailer', 'assets/videos/catalogs/trailers/');
        $trailerUploader->allowedExtTypes = ["mp4"];
        $trailerUploader->allowedMimeTypes = ["video/mp4"];
        $trailerUploader->maxFileSize = 100000000;

        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == UPLOAD_ERR_OK) {
            $postername = $posterUploader->uploadFie($_FILES['poster'], $_POST['title']);
        }

        if (isset($_FILES['trailer']) && $_FILES['trailer']['error'] == UPLOAD_ERR_OK) {
            $trailername = $trailerUploader->uploadFie($_FILES['trailer'], $_POST['title']);
        }

        $body = [
            "uuid" => UUIDGenerator::uuid4(),
            "title" => isset($_POST['title']) ? $_POST['title'] : "",
            "description" => isset($_POST['description']) ? $_POST['description'] : "",
            "poster" => isset($postername) ? $postername : "",
            "trailer" => isset($trailername) ? $trailername : "",
            "category" => isset($_POST['category']) ? $_POST['category'] : "ANIME",
        ];


        $soapRequest = new SOAPRequest("catalog-request", "CatalogCreateRequest", [], [], $body);
        $response = $soapRequest->post();
        echo json_encode($response);
    }

    public function deletePoster($poster)
    {
        if (isset($poster)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/images/catalogs/posters/' . $poster);
        }

        echo json_encode([
            "status" => 200,
            "message" => "Successfully delete catalog request",
        ]);
    }

    public function deleteTrailer($trailer)
    {
        if (isset($trailer)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/videos/catalogs/trailers/' . $trailer);
        }

        echo json_encode([
            "status" => 200,
            "message" => "Successfully delete catalog request",
        ]);
    }
}