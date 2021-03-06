<?php
namespace App\Action\EditRef;


use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractController;

class SchedEditRefController extends AbstractController
{
    /* @var SchedEditRefView */
    private $schedEditRefView;

    public function __construct(Container $container, SchedEditRefView $schedEditRefView)
    {
        parent::__construct($container);

        $this->schedEditRefView = $schedEditRefView;
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
        if(!$this->isAuthorized()) {
            return $response->withRedirect($this->getBaseURL('logonPath'));
        }

        //check for match locked
        $game_id = isset($_SESSION['game_id']) ? $_SESSION['game_id'] : null;
        $sr = $this->container['sr'];
        $target_game = $sr->gameIdToGameNumber($game_id);
        $game = $sr->getGameByKeyAndNumber($this->event->projectKey, $target_game);

        if(!is_null($game) && $game->locked && !$this->user->admin){
            return $response->withRedirect($this->getBaseURL('refsPath'));
        }

        //process edit assignments
        $this->logStamp($request);

        $request = $request->withAttributes([
            'user' => $this->user,
            'event' => $this->event,
            'game_id' => $game_id
        ]);

        $this->schedEditRefView->handler($request, $response);

        if($this->schedEditRefView->isPost()) {
            $response =  $response->withRedirect($this->getBaseURL('refsPath'));
        }
        else {
            $this->schedEditRefView->render($response);
        }

        return $response;
    }
}


