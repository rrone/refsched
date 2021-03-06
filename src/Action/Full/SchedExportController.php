<?php

namespace  App\Action\Full;


use PhpOffice\PhpSpreadsheet\Exception;
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

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     *
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if(!$this->isAuthorized()) {
            return $response->withRedirect($this->getBaseURL('fullPath'));
        }

        $this->logStamp($request);

        $request = $request->withAttributes([
            'user' => $this->user,
            'event' => $this->event
        ]);

        $response = $this->exportXl->handler($request, $response);

        return $response;
		
    }
}
