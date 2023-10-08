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

class CatalogController
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

    public function index(): void
    {
        $page = $_GET['page'] ?? 1;
        $category = $_GET['category'] ?? "MIXED";

        $user = $this->sessionService->current();

        View::render('catalog/index', [
            'title' => 'Catalog',
            'styles' => [
                '/css/catalog.css',
            ],
            'js' => [
                '/js/catalog/delete.js'
            ],
            'data' => [
                'catalogs' => $this->catalogService->findAll($page, $category),
                'category' => strtoupper(trim($category)),
                'userRole' => $user ? $user->role : null
            ]
        ], $this->sessionService);
    }

    public function create(): void
    {
        View::render('catalog/form', [
            'title' => 'Add Catalog',
            'styles' => [
                '/css/catalog-form.css',
            ],
            'js' => [
                '/js/catalog/createUpdate.js'
            ],
            'type' => 'create'
        ], $this->sessionService);
    }

    public function edit($uuid): void
    {
        $catalog = $this->catalogService->findByUUID($uuid);

        if (!$catalog) {
            View::redirect('/404');
        }

        View::render('catalog/form', [
            'title' => 'Edit Catalog',
            'styles' => [
                '/css/catalog-form.css',
            ],
            'js' => [
                '/js/catalog/createUpdate.js'
            ],
            'type' => 'edit',
            'data' => $catalog->toArray()
        ], $this->sessionService);
    }

    public function detail($uuid): void
    {
        $catalog = $this->catalogService->findByUUID($uuid);


        if (!$catalog) {
            View::redirect('/404');
        }

        $user = $this->sessionService->current();
        View::render('catalog/detail', [
            'title' => 'Catalog Detail',
            'styles' => [
                '/css/catalog-detail.css',
            ],
            'js' => [
                '/js/catalog/delete.js'
            ],
            'data' => [
                'item' => $catalog->toArray(),
                'userRole' => $user ? $user->role : null
            ]
        ], $this->sessionService);
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
            $response = $this->catalogService->create($request);
            http_response_code(200);
            $response = [
                "status" => 200,
                "message" => "Successfully created catalog",
                "data" => [
                    "uuid" => $response->catalog->uuid,
                    "title" => $response->catalog->title,
                ]
            ];

            echo json_encode($response);
        } catch (ValidationException $exception) {
            http_response_code(400);
            $response = [
                "status" => 400,
                "message" => $exception->getMessage(),
            ];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response = [
                "status" => 500,
                "message" => "Something went wrong.",
            ];

            echo json_encode($response);
        }
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
            $page = $catalogs->catalogs['page'];
            require __DIR__ . '/../View/components/modal/watchlistAddSearchItem.php';
        }
    }

    public function update($uuid): void
    {
        $user = $this->sessionService->current();
        try {
            if (!$user || $user->role !== 'ADMIN') {
                throw new ValidationException("You are not authorized to update this catalog.");
            }

            $request = new CatalogUpdateRequest();

            $request->uuid = $uuid;
            $request->title = $_POST['title'];
            $request->description = $_POST['description'];
            $request->category = $_POST['category'];

            if (isset($_FILES['poster'])) {
                $request->poster = $_FILES['poster'];
            }

            if (isset($_FILES['trailer'])) {
                $request->trailer = $_FILES['trailer'];
            }

            $this->catalogService->update($request);
            http_response_code(200);
            $response = [
                "status" => 200,
                "message" => "Successfully update catalog",
            ];

            echo json_encode($response);
        } catch (ValidationException $exception) {
            http_response_code(400);
            $response = [
                "status" => 400,
                "message" => $exception->getMessage(),
            ];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response = [
                "status" => 500,
                "message" => "Something went wrong.",
            ];

            echo json_encode($response);
        }
    }

    public function delete(string $uuid)
    {
        $user = $this->sessionService->current();

        try {
            if ($user && $user->role === 'ADMIN') {
                $this->catalogService->deleteByUUID($uuid);
                http_response_code(200);

                $response = [
                    "status" => 200,
                    "message" => "Successfully delete catalog",
                ];

                echo json_encode($response);
            } else {
                throw new ValidationException("You are not authorized to delete this catalog.");
            }
        } catch (ValidationException $exception) {
            http_response_code(400);

            $response = [
                "status" => 400,
                "message" => $exception->getMessage(),
            ];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response = [
                "status" => 500,
                "message" => "Something went wrong.",
            ];

            echo json_encode($response);
        }
    }
}