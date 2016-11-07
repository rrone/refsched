<?php

namespace  App\Action\Full;

use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use App\Action\AbstractController;

class SchedExportController extends AbstractController
{
    private $exportXl;

	public function __construct(Container $container, SchedExportXl $exportXl)
    {
		parent::__construct($container);

        $this->exportXl = $exportXl;
        
    }
    public function __invoke(Request $request, Response $response, $args)
    {
        parent::__invoke($request, $response, $args);

        if (is_null($this->event)) {
            return $response->withRedirect($this->container->get('fullPath'));
        }

        $this->logStamp($request);

        $request = $request->withHeader('user', $this->user);
        $request = $request->withHeader('event', $this->event);

        $response = $this->exportXl->handler($request, $response);

        return $response;
		
    }
}
