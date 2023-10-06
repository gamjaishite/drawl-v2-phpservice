<?php

require_once __DIR__ . '/../Utils/UUIDGenerator.php';

require_once __DIR__ . '/../Repository/SessionRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php';

require_once __DIR__ . '/../Domain/Session.php';
require_once __DIR__ . '/../Domain/User.php';

require_once __DIR__ . '/../Model/session/SessionCreateRequest.php';

class SessionService
{
    public static string $COOKIE_NAME = 'bogoshipo__ohimesama';
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
    }

    public function create(SessionCreateRequest $sessionCreateRequest): Session
    {
        $session = new Session();
        $session->id = UUIDGenerator::uuid4();
        $session->userId = $sessionCreateRequest->userId;
        $session->expired = gmdate(DATE_RFC3339, strtotime("+1 week"));

        $this->sessionRepository->save($session);

        setcookie(self::$COOKIE_NAME, $session->id, time() + (60 * 60 * 24 * 7), "/", "", false, true);

        return $session;
    }

    public function destroy()
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $this->sessionRepository->deleteBy("id", $sessionId);

        setcookie(self::$COOKIE_NAME, '', 1, "/");
    }

    public function current(): ?User
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $session = $this->sessionRepository->findOne("id", $sessionId);

        if ($session == null || $session->expired < gmdate(DATE_RFC3339)) {
            $this->destroy();
            return null;
        }

        return $this->userRepository->findOne("id", $session->userId);
    }
}
