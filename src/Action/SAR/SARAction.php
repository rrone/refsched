<?php
namespace App\Action\SAR;

use App\Action\SchedulerRepository;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

final class SARAction
{
    private $sr;

    public function __construct(Container $container, SchedulerRepository $sr)
    {
        $this->sr = $sr;
    }

    public function __invoke(Request $request, Response $response)
    {
        $portal = $request->getAttribute('portal');

        $content = $this->retrieveSAR($portal);

// headers to tell that result is JSON
        $newResponse = $response->withHeader('Content-type', 'application/json');

        $newResponse = $newResponse->withJson($content, 200,JSON_UNESCAPED_SLASHES);

        return $newResponse;

    }

    protected function retrieveSAR($portalName)
    {
        $sarRec = $this->sr->getSARRec($portalName);

        if (!empty($sarRec)) {
            $sar = $sarRec->section.'/'.$sarRec->area.'/'.$sarRec->region;
        } else {
            $sar = 'SAR not found';
        }

        return $sar;

    }

}
