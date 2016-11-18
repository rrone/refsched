<?php
namespace App\Action\Lock;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractView;
use App\Action\SchedulerRepository;

class SchedLockView extends AbstractView
{
    public function __construct(Container $container, SchedulerRepository $schedulerRepository)
    {
        parent::__construct($container, $schedulerRepository);

        $this->sr = $schedulerRepository;
    }
    public function handler(Request $request, Response $response)
    {
        $this->user = $request->getHeader('user')[0];
        $this->event = $request->getHeader('event')[0];
    }
    public function render(Response &$response)
    {
        $content = array(
            'view' => array (
                'user' => $this->user,
                'ulock' => $this->renderLock(),
                'topmenu' => $this->menu(),
                'menu' => $this->menu(),
                'title' => $this->page_title,
				'dates' => $this->dates,
				'location' => $this->location,
            )
        );

        $this->view->render($response, 'sched.ulock.html.twig', $content);
    }
    public function renderLock()
    {
        $html = null;

        if (!empty($this->event)) {
            $projectKey = $this->event->projectKey;
            $locked = $this->sr->getLocked($projectKey);

            if ( $locked ) {
                $html .= "<h3 align=\"center\">The schedule is already locked!</h3>\n";
            }
            elseif ( $this->user->admin) {
                $this->sr->lockProject($projectKey);
                $html .= "<h3 align=\"center\">The schedule has been locked!</h3>\n";
            }
        }
        elseif ( $this->user->admin ) {
            $html .= "<h2 class=\"center\">You seem to have gotten here by a different path<br>\n";
            $html .= "You should go to the <a href=" . $this->getBaseURL('masterPath') . ">Schedule Page</a></h2>";
        }
        elseif ( !$this->user->admin ) {
            $html .= "<h2 class=\"center\">You seem to have gotten here by a different path<br>\n";
            $html .= "You should go to the <a href=" . $this->getBaseURL('schedPath') . ">Schedule Page</a></h2>";
        }
        else {
            $html .= "<h2 class=\"center\">You need to <a href=" . $this->getBaseURL('logonPath') . ">logon</a> first.</h2>";
        }

        return $html;

    }
    public function renderUnlock()
    {
        $html = null;

        if (!empty($this->event)) {
            $projectKey = $this->event->projectKey;
            $locked = $this->sr->getLocked($projectKey);

            if ( !$locked ) {
                $html .= "<h3 align=\"center\">The schedule is already unlocked!</h3>\n";
            }
            elseif ( $this->user->admin ) {
                $this->sr->unlockProject($projectKey);
                $html .= "<h3 align=\"center\">The schedule has been unlocked!</h3>\n";
            }
        }
        elseif ( $this->user->admin ) {
            $html .= "<h2 class=\"center\">You seem to have gotten here by a different path<br>\n";
            $html .= "You should go to the <a href=" . $this->getBaseURL('masterPath') . ">Schedule Page</a></h2>";
        }
        else {
            $html .= "<h2 class=\"center\">You seem to have gotten here by a different path<br>\n";
            $html .= "You should go to the <a href=" . $this->getBaseURL('schedPath') . ">Schedule Page</a></h2>";
        }

        return $html;

    }
    private function menu()
    {
        $html = "<h3 align=\"center\"><a href=" . $this->getBaseURL('greetPath') . ">Go to main screen</a>&nbsp;-&nbsp;\n";
        $html .= "<a href=" . $this->getBaseURL('masterPath') . ">Go to schedule</a>&nbsp;-&nbsp;\n";
        $html .= "<a href=" . $this->getBaseURL('endPath') . ">Log off</a></h3>\n";

        return $html;
    }
}