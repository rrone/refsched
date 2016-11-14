<?php
namespace App\Action\Admin;

use Slim\Container;
use Slim\Views\Twig;;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\SchedulerRepository;
use App\Action\AbstractExporter;

class LogExport extends AbstractExporter
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

        $this->outFileName = 'Access_Log_' . date('Ymd_His') . '.' . $this->getFileExtension();
    }

    public function handler(Request $request, Response $response)
    {
        $this->user = $request->getHeader('user')[0];
        $this->event = $request->getHeader('event')[0];

        // generate the response
        $response = $response->withHeader('Content-Type', $this->contentType);
        $response = $response->withHeader('Content-Disposition', 'attachment; filename=' . $this->outFileName);

        $content = null;

        $this->generateAccessLogData($content);

        $body = $response->getBody();
        $body->write($this->export($content));

        return $response;
    }

    public function generateAccessLogData(&$content)
    {
        $log = $this->sr->getAccessLog();

        //set the header labels
        $labels = array('Timestamp', 'Project Key', 'User', 'Memo');
        $data = array($labels);

        //set the data : game in each row
        foreach ($log as $item) {
            $msg = explode(':', $item->note);
            if (isset($msg[1])) {
                $user = $msg[0];
                $note = $msg[1];
            } else {
                $user = '';
                $note = $item->note;
            }

            $row = array(
                $item->timestamp,
                $item->projectKey,
                $user,
                $note
            );

            $data[] = $row;
        }

        $content['Access_Log']['data'] = $data;
        $content['Access_Log']['options']['freezePane'] = 'A2';

        return $content;

    }
}