<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    #[Route('/test', name: 'test_route', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return new JsonResponse(['message' => 'Hello World']);
    }
}
