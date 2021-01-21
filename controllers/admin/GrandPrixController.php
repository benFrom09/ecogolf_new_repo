<?php

namespace Ecogolf\controllers\admin;

use Ecogolf\Core\AuthController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GrandPrixController extends AuthController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response,$args)
    {
        $this->renderer->render("admin.pages.admin_gprix");
        return $response;
    }
}