<?php
namespace App\Action\Lock;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractController;

class SchedUnlockDBController extends AbstractController
{
    private $lulView;

    public function __construct(Container $container, SchedLockView $lockView)
    {
        parent::__construct($container);

        $this->lulView = $lockView;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        if(!$this->isAuthorized()) {
            return $response->withRedirect($this->getBaseURL('logonPath'));
        };

        $this->logStamp($request);

        $request = $request->withHeader('user', $this->user);
        $request = $request->withHeader('event', $this->event);

        $this->lulView->handler($request, $response);
        $this->lulView->renderUnlock();

        return $response->withRedirect($this->getBaseURL('greetPath'));
    }
}


