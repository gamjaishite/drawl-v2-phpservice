<?php

require_once __DIR__ . '/../App/Controller.php';
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Service/CatalogService.php';
require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Exception/ValidationException.php';


class CatalogController
{
    private CatalogService $catalogService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $catalogRepository = new CatalogRepository($connection);
        $this->catalogService = new CatalogService($catalogRepository);
    }

    public function index(): void
    {
        View::render('catalog/index', [
            'title' => 'Drawl | Catalog',
            'styles' => [
                './css/catalog.css',
            ],
        ]);
    }

    public function create(): void
    {
        View::render('catalog/create', [
            'title' => 'Drawl | Add Catalog',
            'styles' => [
                './css/catalog.css',
            ],
        ]);
    }

    public function postCreate(): void
    {
        $request = new CatalogCreateRequest();
        $request->category = $_POST['category'];
        $request->title = $_POST['title'];
        $request->description = $_POST['description'];

        $request->poster = $_FILES['poster'];

        if (isset($_FILES["trailer"]) && $_FILES["trailer"]["error"] === UPLOAD_ERR_OK) {
            $request->trailer = $_FILES["trailer"];
        }

        try {
            $this->catalogService->create($request);
            View::redirect('/catalog');
        } catch (ValidationException $exception) {
            echo $exception->getMessage();
            View::render('catalog/create', [
                'title' => 'Drawl | Add Catalog',
                'error' => $exception->getMessage(),
                'styles' => [
                    './css/catalog.css',
                ],
            ]);
        }
    }
}