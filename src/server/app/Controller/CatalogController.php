<?php

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
        $page = $_GET['page'] ?? 1;
        $category = $_GET['category'] ?? "MIXED";

        View::render('catalog/index', [
            'title' => 'Catalog',
            'styles' => [
                '/css/catalog.css',
            ],
            'data' => [
                'catalogs' => $this->catalogService->findAll($page, $category),
                'category' => strtoupper(trim($category))
            ]
        ]);
    }

    public function create(): void
    {
        View::render('catalog/form', [
            'title' => 'Add Catalog',
            'styles' => [
                '/css/catalog-form.css',
            ],
        ]);
    }

    public function edit(): void
    {
        View::render('catalog/form', [
            'title' => 'Edit Catalog',
            'styles' => [
                '/css/catalog-form.css',
            ],
        ]);
    }

    public function detail(): void
    {
        View::render('catalog/detail', [
            'title' => 'Catalog Detail',
            'styles' => [
                '/css/catalog-detail.css',
            ],
            'data' => [
                'title' => 'Snowdrop',
                'category' => 'ANIME',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'poster' => 'jihu-13.jpg',
                'trailer' => 'the-journey-of-elaina.mp4'
            ]
        ]);
    }

    public function postCreate(): void
    {
        $request = new CatalogCreateRequest();
        if (isset($_POST['category'])) {
            $request->category = $_POST['category'];
        }

        $request->title = $_POST['title'];
        $request->description = $_POST['description'];
        $request->poster = $_FILES['poster'];

        if (isset($_FILES['trailer'])) {
            $request->trailer = $_FILES['trailer'];
        }

        try {
            $this->catalogService->create($request);
            View::redirect('/catalog');
        } catch (ValidationException $exception) {
            View::render('catalog/form', [
                'title' => 'Add Catalog',
                'error' => $exception->getMessage(),
                'styles' => [
                    '/css/catalog-form.css',
                ],
                'data' => [
                    'title' => $request->title,
                    'description' => $request->description,
                    'category' => $request->category,
                ]
            ]);
        }
    }
}