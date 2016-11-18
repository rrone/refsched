<?php
namespace App\Action\Admin;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\SchedulerRepository;
use App\Action\AbstractExporter;
use Slim\Views\Twig;

class SchedTemplateExport extends AbstractExporter
{
    /* @var Container */
    private $container;

    /* @var SchedulerRepository */
    private $sr;

    /* @var Twig */
    private $view;

    private $outFileName;
    private $user;
    private $event;

    public function __construct(Container $container, SchedulerRepository $schedulerRepository)
    {
        parent::__construct('xls');

        $this->container = $container;
        $this->sr = $schedulerRepository;
        $this->view = $container->get('view');

        $this->outFileName = 'GameScheduleTemplate_' . date('Ymd_His') . '.' . $this->getFileExtension();
    }
    public function handler(Request $request, Response $response)
    {
        $this->user = $request->getHeader('user')[0];
        $this->event = $request->getHeader('event')[0];

        $file = $this->renderFile();
        if ($file['valid']) {
            // generate the response
            $response = $response->withHeader('Content-Type', $this->contentType);
            $response = $response->withHeader('Content-Disposition', 'attachment; filename='. $this->outFileName);

            $body = $response->getBody();
            $content = $file['content'];
            $body->write($this->export($content));

            return $response;

        } else {
            if (!empty($this->event)) {
                $this->render($response);

                return $response;
            }
        }

        return null;
    }
    protected function render(Response &$response)
    {
        $msg = $this->event->name . ' at ' . $this->event->location . ' on ' . $this->event->dates;
        $content = array(
            'view' => array(
                'admin' => $this->user->admin,
                'action' => $this->getBaseURL('adminPath'),
                'message' => "There are no games in the database for the event: $msg",
            )
        );

        $this->view->render($response, 'modal.html.twig', $content);
    }
    protected function renderFile()
    {
        $content = null;

        $hdr = null;
        $row = null;
        $dateCol = null;

        if (!empty($this->event)) {
            $projectKey = $this->event->projectKey;

            //set the header labels
            $labels = $this->sr->getGamesHeader();

            if (!is_null($labels)){
                foreach($labels as $key=>$label) {
                    $hdr[] = $label;
                    switch ($label) {
                        case 'projectKey':
                            $row[] = $projectKey;
                            break;
                        case 'medalRound':
                            $row[] = '0';
                            break;
                        case 'date':
                            $dateCol = $key;
                            break;
                        default:
                            $row[] = null;
                    }
                }

                $data[] = $hdr;
                $data[] = $row;

                $dateCol = chr($dateCol+65) . ":" . chr($dateCol+65);

                $wkbk['FullSchedule']['data'] = $data;
                $wkbk['FullSchedule']['options']['freezePane'] = 'A2';
                $wkbk['FullSchedule']['options']['style'] = array($dateCol=>'yyyy-mm-dd');

                $content = array('valid'=>true, 'content'=>$wkbk);

            } else {

                $content = array('valid'=>false, 'content'=>null);
            }
        }

        return $content;
    }
}