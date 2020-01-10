<?php
namespace App\Action\MedalRound;


use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractController;

class ShowMedalRoundController extends AbstractController
{
    private $mrView;

    public function __construct(Container $container, MedalRoundView $medalRoundView)
    {
        parent::__construct($container);

        $this->mrView = $medalRoundView;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     *
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if(!$this->isAuthorized() || !$this->user->admin) {
            return $response->withRedirect($this->getBaseURL('greetPath'));
        }

        $this->logStamp($request);

        $request = $request->withAttributes([
            'user' => $this->user,
            'event' => $this->event,
            'show' => true
        ]);

        $this->mrView->handler($request, $response);
        $this->mrView->render($response);

        return $response->withRedirect($this->getBaseURL('greetPath'));
    }
}


