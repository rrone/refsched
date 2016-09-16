<?php
namespace App\Action\Lock;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Action\AbstractController;

class SchedLockController extends AbstractController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $this->logger->info("Schedule greet page action dispatched");
        
        $content = array(
            'sched' => array (
                'ulock' => $this->renderLock()
            )
        );        
        
        $this->view->render($response, 'sched.ulock.html.twig', $content);
;
    }

    private function renderLock()
    {
        $html = null;
        
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['HTTP_HOST'];
        $from_url = parse_url( $referer );

        $from = $from_url['path'];
  //      $html .= "<p>$from</p>\n";
        $this->authed = isset($_SESSION['authed']) ? $_SESSION['authed'] : false;
        
        $rep = $_SESSION['unit'];
        $schedule_file = isset($_SESSION['eventfile']) ? $_SESSION['eventfile'] : null;
        if ( $this->authed && $rep == 'Section 1') {
            $lock_yes = 0;
   //         print_r($_POST);
            copy( $schedule_file, $this->refdata . "temp_lock.dat");
            $outfile = fopen( $schedule_file, "w");
            if (flock( $outfile, LOCK_EX )) {
                $tmpfile = fopen( $this->refdata . "temp_lock.dat", "r");
                $sched_no = fgets( $tmpfile, 1024 );
                fputs( $outfile, $sched_no );
                $sched_title = fgets( $tmpfile, 1024 );
                fputs( $outfile, $sched_title );
                $page_title = substr( $sched_title, 1);
    
                $html .= "<center><h1>$page_title</h1></center>";
                $lock_line = "#Locked\n";
                fputs( $outfile, $lock_line );
    
                while ( $line = fgets( $tmpfile, 1024 ) ) {
                    if ( substr( $line, 0, 1 ) == '#' ) {
                        if ( strtoupper( trim( $line ) ) == '#LOCKED' ) {
                            $lock_yes = 1;
                        }
                        else {
                            fputs( $outfile, $line );
                        }
                    }
                    else {
                       fputs( $outfile, $line );
                    }
                }
                fclose ( $tmpfile );
                flock( $outfile, LOCK_UN );
            }
            fclose ( $outfile );
            if ( $lock_yes ) {
               $html .= "<h3 align=\"center\">The schedule was already locked!</h3>\n";
            }
            else {
               $html .= "<h3 align=\"center\">The schedule has been locked!</h3>\n";
            }
        }
        elseif ( $this->authed && $rep == 'Section 1') {
           $html .= "<center><h2>You seem to have gotten here by a different path<br>\n";
           $html .= "You should go to the <a href=\"/master\">Schedule Page</a></h2></center>";
        }
        elseif ( $this->authed ) {
           $html .= "<center><h2>You seem to have gotten here by a different path<br>\n";
           $html .= "You should go to the <a href=\"/sched\">Schedule Page</a></h2></center>";
        }
        elseif ( !$this->authed ) {
           $html .= "<center><h2>You need to <a href=\"/\">logon</a> first.</h2></center>";
        }
        else {
           $html .= "<center><h1>Something is not right</h1></center>";
        }    
        return $html;
          
    }
}


