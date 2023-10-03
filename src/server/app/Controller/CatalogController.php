<?php

require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Service/CatalogService.php';
require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Model/CatalogSearchRequest.php';

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
            'type' => 'create'
        ]);
    }

    public function edit($uuid): void
    {
        $catalog = $this->catalogService->findByUUID($uuid);

        if (!$catalog) {
            View::render('catalog/not-found', [
                'title' => 'Catalog Not Found',
                'styles' => [
                    '/css/catalog-not-found.css',
                ],
            ]);
        }

        View::render('catalog/form', [
            'title' => 'Edit Catalog',
            'styles' => [
                '/css/catalog-form.css',
            ],
            'type' => 'edit',
            'data' => $catalog->toArray()
        ]);
    }

    public function detail($uuid): void
    {
        $catalog = $this->catalogService->findByUUID($uuid);

        if (!$catalog) {
            View::redirect('/404');
        }

        View::render('catalog/detail', [
            'title' => 'Catalog Detail',
            'styles' => [
                '/css/catalog-detail.css',
            ],
            'data' => $catalog->toArray()
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

        if (isset($_FILES['poster'])) {
            $request->poster = $_FILES['poster'];
        }

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
                'type' => 'create',
                'data' => [
                    'title' => $request->title,
                    'description' => $request->description,
                    'category' => $request->category,
                ]
            ]);
        }
    }

    public function postEdit($uuid): void
    {
        $request = new CatalogCreateRequest();
        $request->title = $_POST['title'];
        $request->description = $_POST['description'];
        $request->category = $_POST['category'];

        if (isset($_FILES['poster'])) {
            $request->poster = $_FILES['poster'];
        }

        if (isset($_FILES['trailer'])) {
            $request->trailer = $_FILES['trailer'];
        }

        try {
            $this->catalogService->update($uuid, $request);
            View::redirect('/catalog/' . $uuid);
        } catch (ValidationException $exception) {
            $catalog = $this->catalogService->findByUUID($uuid);
            $catalog->title = $request->title;
            $catalog->description = $request->description;
            $catalog->category = $request->category;
            View::render('catalog/form', [
                'title' => 'Edit Catalog',
                'error' => $exception->getMessage(),
                'styles' => [
                    '/css/catalog-form.css',
                ],
                'type' => 'edit',
                'data' => $catalog->toArray()
            ]);
        }
    }

    public function postDelete($uuid): void
    {
        $this->catalogService->deleteByUUID($uuid);
        View::redirect('/catalog');
    }

    public function search()
    {
        $request = new CatalogSearchRequest();
        $request->title = $_GET["title"];
        $request->page = $_GET["page"];
        $request->pageSize = $_GET["pageSize"];

        $catalogs = $this->catalogService->search($request);

        foreach ($catalogs->catalogs['items'] as $item) {
            $title = $item->title;
            $poster = $item->poster;
            $uuid = $item->uuid;
            $description = $item->description;
            $category = $item->category;
            require __DIR__ . '/../View/components/modal/watchlistAddSearchItem.php';
        }
    }
}
