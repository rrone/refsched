<?php
namespace App\Action\Sched;

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Action\AbstractController;
use App\Action\SchedulerRepository;

class SchedSchedDBController extends AbstractController
{
    // SchedulerRepository //
    private $sr;
    
	public function __construct(Container $container, SchedulerRepository $repository) {
		
		parent::__construct($container);
        
        $this->sr = $repository;
		
    }
    public function __invoke(Request $request, Response $response, $args)
    {
        $this->authed = isset($_SESSION['authed']) ? $_SESSION['authed'] : null;
         if (!$this->authed) {
            return $response->withRedirect($this->logonPath);
         }

        $this->logger->info("Schedule schedule database page action dispatched");

        $this->rep = isset($_SESSION['unit']) ? $_SESSION['unit'] : null;        
		$this->event = isset($_SESSION['event']) ?  $_SESSION['event'] : false;		

		if ( $request->isPost() ) {
			$this->handleRequest($request);
		}
		
        $content = array(
            'view' => array (
                'content' => $this->renderSched(),
                'topmenu' => $this->menu(),
                'menu' => $this->menu(),
                'title' => $this->page_title,
				'dates' => $this->dates,
				'location' => $this->location,
				'description' => $this->rep . ' Schedule'
            )
        );        
      
        $this->view->render($response, 'sched.html.twig', $content);
    }
	private function handleRequest($request)
	{
		if ( $this->rep != 'Section 1' ) {
			$projectKey = $this->event->projectKey;
			$locked = $this->sr->getLocked($projectKey);
			$msgHtml = null;

			//load limits if any or none
			$limits = $this->sr->getLimits($projectKey);
			if ( !count( $limits ) ) {
				$no_limit = true;
			}
			else {
				foreach($limits as $group){
					$limit_list[ $group->division ] = $group->limit;
				}
			}
			
			$array_of_keys = array_keys( $_POST );
			
			//parse the POST data
			$adds = [];
			$assign = [];
			foreach ($array_of_keys as $key){
				$change = explode(':',$key);
				switch  ($change[0]) {
					case 'assign':
						$adds[ $change[1] ] = $this->rep;
						break;
					case 'games':
						$assign[ $change[1] ] = $this->rep;
						break;
					default:
						continue;
				}
			}

			if ( !$locked ) {
				//remove drops if not locked
				$assigned_games = $this->sr->getGamesByRep($projectKey, $this->rep);
				if(count($assign) != count($assigned_games)){
					$removed = [];
					$unassign = [];
					foreach($assigned_games as $game) {
						if(!in_array($game->id, array_keys($assign)) ){
							$removed[$game->id] = $game;
							$unassign[$game->id] = '';
							$msgHtml .= "<p>You have <strong>removed</strong> your referee team from Game no. $game->game_number on $game->date at $game->time on $game->field</p>\n";
						}					
					}
					$this->sr->updateAssignor($unassign);	
					//initialize counting groups
					$assigned_games = $this->sr->getGamesByRep($projectKey, $this->rep);
					foreach ($assigned_games as $game) {
						$div = $this->divisionAge($game->division);
						$games_now[ $div ] = isset($games_now[ $div ]) ? $games_now[ $div ]++ : 0;
					}
				}
			}
			
			if ( count($assign)) {
				//Update based on add/returned games
				$added = [];
				$unavailable = [];
				$games = $this->sr->getGames($projectKey);		
				foreach($games as $game) {
					$div = $this->divisionAge($game->division);
					//ensure all indexes exist
					$games_now[ $div ] = isset($games_now[ $div ]) ? $games_now[$div] : 0;
					$atLimit[ $div ] = isset($atLimit[ $div ]) ? $atLimit[$div] : 0;;
					//if requested
					if(in_array($game->id, array_keys($adds)) ) {
						//and available

						if ( empty($game->assignor) ) {
							//and below the limit if there is one
							if ($games_now[$div] < $limit_list[$div] or $no_limit)  {
								//make the assignment
								$data = [ $game->id => $this->rep ];
								$this->sr->updateAssignor($data);
								$added[$game->id] = $game;
								$games_now[$div]++;
							}
							else {
								$atLimit[$div]++;
							}
						}
						else {
							$unavailable[$game->id] = $game;
						}
					}
				}

				$assigned_update = $this->sr->getGamesByRep($projectKey, $this->rep);
			}

//				$html .= "<p>You have <strong>scheduled</strong> Game no. $record[0] on $record[2], $record[1], $record[4] at $record[3]</p>\n";
//				$html .= "<p>You have <strong>scheduled</strong> Game no. $record[0] on $record[2], $record[1], $record[4] at $record[3]</p>\n";
//				$html .= "<p>You have <strong>not scheduled</strong> Game no. $record[0] on $record[2], $record[1], $record[4] at $record[3] because you are at your game limit!</p>\n";
//			   $html .= "<p>You have <strong>removed</strong> your referee team from Game no. $record[0] on $record[2], $record[1], $record[4] at $record[3]</p>\n";
//                       $html .= "<p>Your referee team has been <strong>removed</strong> from Game no. $record[0] on $record[2], $record[1], $record[4] at $record[3] because you are over the game limit.</p>\n";
//				$html .= "<p>I'm sorry, game no. $record[0] has been taken.</p>";
		}
	}

    private function renderSched()
    {
        $html = null;
        
        $showgroup = null;
		$event = $this->event;
        
		if (!empty($event)) {
			$projectKey = $event->projectKey;
	
			if ( count( $_GET ) && array_key_exists( 'group', $_GET ) ) {
			   $showgroup = $_GET[ 'group' ];
			}
			else {
				$showgroup = null;
			}
	
			$locked = $this->sr->getLocked($projectKey);
	
			$color1 = '#D3D3D3';
			$color2 = '#B7B7B7';
			$allatlimit = 1;
			$oneatlimit = 0;
			$showavailable = 1;
			$a_init = substr( $this->rep, -1 );
			$used_list[ 'none' ] = null;
			$assigned_list = null;
			
			if ( $this->rep != 'Section 1') {
				$limits = $this->sr->getLimits($projectKey);
				$groups = $this->sr->getGroups($projectKey);
	
				foreach($limits as $group){
					$limit_list[ $group->division ] = $group->limit;
					$used_list[ $group->division ] = 0;
					$assigned_list[ $group->division ] = 0;
				}
				if ( !count( $limit_list ) ) {
					$limit_list[ 'none' ] = 1;
				}
			}
			else {
				$limit_list[ 'none' ] = 1;
			}
			
			$this->page_title = $event->name;
			$this->dates = $event->dates;
			$this->location = $event->location;
	
			$no_assigned = 0;		
			$kount = 0;
			$kant = 0;
			$testtime = null;
	
			$locked = $this->sr->getLocked($projectKey);
			$_SESSION['locked'] = $locked;
	
			$games = $this->sr->getGames($projectKey, $showgroup);

			foreach( $games as $game ) {
				$game_id[] = $game->id;
				$game_no[] = $game->game_number;
				$date[] = $game->date;
				$day[] = date('D',strtotime($game->date));
				$field[] = $game->field;
				$time[] = date('H:i', strtotime($game->time));
				$div[] = $game->division;
				$home[] = $game->home;
				$away[] = $game->away;
				$ref_team[] = $game->assignor;
				if ( $game->assignor == $this->rep ) { 
					$no_assigned++;
					if (isset($assigned_list[ $this->divisionAge( $game->division ) ])) {
						$assigned_list[ $this->divisionAge( $game->division ) ]++;
					}
			   }
			   $used_list[ $this->divisionAge( $game->division ) ] = 1;
			   $cr[] = $game->cr;
			   $ar1[] = $game->ar1;
			   $ar2[] = $game->ar2;
			   $r4th[] = $game->r4th;
			   
			   $kount = count($games);
			}
	
			//$free_board = $limit - $no_assigned;
			if ( $locked && array_key_exists( 'none', $limit_list ) ) { 
				$html .= "<center><h3><font color=\"#CC0000\">The schedule has been locked.<br>You may sign up for games but not unassign yourself.</font></h3></center>\n"; 
				$allatlimit = 0;
			}
			elseif ( $locked && array_key_exists( 'all', $limit_list ) && $no_assigned < $limit_list[ 'all' ] ) { 
				$html .= "<center><h3><font color=\"#CC0000\">The schedule has been locked.<br>You may sign up for games but not unassign yourself.</font></h3></center>\n"; 
				$allatlimit = 0;
			}
			elseif ( $locked && array_key_exists( 'all', $limit_list ) && $no_assigned == $limit_list[ 'all' ] ) { 
				$html .= "<center><h3><font color=\"#CC0000\">The schedule has been locked and you are at your game limit.<br>\nYou will not be able to unassign yourself from games to sign up for others.<br>\nThe submit button on this page has been disabled and available games are not shown.<br>\nYou probably want to <a href=\"$this->greetPath\">Go to the Main Page</a> or <a href=\"$this->endPath\">Log Off</a></font></h3></center>\n";
				$showavailable = 0;
			}
			elseif ( $locked && array_key_exists( 'all', $limit_list ) && $no_assigned > $limit_list[ 'all' ] ) { 
				$html .= "<center><h3><font color=\"#CC0000\">The schedule has been locked and you are above your game limit.<br>\nThe extra games were probably assigned by the Section staff.<br>\nYou will not be able to unassign yourself from games to sign up for others.<br>\nThe Submit button has been disabled and available games are not shown.<br>\nYou probably want to <a href=\"$this->greetPath\">Go to the Main Page</a> or <a href=\"$this->endPath\">Log Off</a></font></h3></center>\n";
				$showavailable = 0; 
			}
			elseif ( !$locked && array_key_exists( 'all', $limit_list ) && $no_assigned < $limit_list['all'] ) { 
				$tmplimit = $limit_list['all'];
				$html .= "<center><h3>You are currently assigned to <font color=\"#CC00CC\">$no_assigned</font> of your <font color=\"#CC00CC\">$tmplimit</font> games.</h3></center>\n"; 
			}
			elseif ( !$locked && array_key_exists( 'all', $limit_list ) && $no_assigned == $limit_list['all'] ) { $html .= "<center><h3><font color=\"#CC0000\">You are at your game limit.<br>You will have to unassign yourself from games to sign up for others.</font></h3></center>\n"; }
			elseif ( !$locked && array_key_exists( 'all', $limit_list ) && $no_assigned > $limit_list['all'] ) { $html .= "<center><h3><font color=\"#CC0000\">You are above your game limit.<br>\nThe extra games were probably assigned by the Section staff.<br>\nIf you continue from here you will not be able to keep all the games you are signed up for and may lose some of the games you already have.<br>\nIf you want to keep these games and remain over the game limit it is recommended that you do not hit submit but do something else instead.<br>\n<a href=\"$this->greetPath\">Go to the Main Page</a></font></h3></center>\n"; }
			elseif ( $locked && count( $limit_list ) ) {
				$html .= "<center><h3><font color=\"#CC0000\">The system is locked.<br>You can add games to divisions that are below the limit but not unassign your Area from games.</font><br><br>\n";
					foreach ( $limit_list as $k => $v ) {
						$tempassign = $assigned_list[$k];
						if ( $used_list[ $k ] ) { 
							$html .= "For $k you are assigned to <font color=\"#CC00CC\">$tempassign</font> with a limit of <font color=\"#CC00CC\">$v</font> games.<br>\n"; 
							if ( $assigned_list[$k] < $limit_list[$k] ) { $allatlimit = 0;}
						}
					}
				if ( $allatlimit ) { 
				   $html .= "<br><font color=\"#CC0000\">All of your divisions are at or above their limits.<br>Because the system is locked, games can not be unassigned to select new ones.<br>No changes are possible: Available games are not shown and the Submit button has been disabled.</font>\n";
				   $showavailable = 0;
				} 
				$html .= "</h3></center>\n";
			}
			elseif ( !$locked && count( $limit_list ) ) {
				$html .= "<center><h3>\n";
				foreach ( $limit_list as $k => $v ) {
					$tempassign = $assigned_list[$k];
					if ( $used_list[ $k ] ) { 
						$html .= "For $k you are assigned to <font color=\"#CC00CC\">$tempassign</font> with a limit of <font color=\"#CC00CC\">$v</font> games.<br>\n"; 
						if ( $assigned_list[$k] >= $limit_list[$k] ) { $oneatlimit = 1;}
					}
				}
				if ( $oneatlimit ) { 
				   $html .= "<br><font color=\"#CC0000\">One or more of your divisions are at or above their limits.<br>You will need to unassign games in that division before you can select additional games.</font>\n";
				} 
				$html .= "</h3></center>\n";
			}
	  
			$html .= "<form name=\"form1\" method=\"post\" action=\"$this->schedPath\">\n";
	
			$html .= "<div align=\"left\">";
   
			$html .= "<h3 class=\"h3-btn\" >Available games - Color change indicates different start times.";
			if ( !$locked && (!$allatlimit && !empty($assigned_list) || $showavailable) ) {
				$html .=  "<input class=\"btn btn-primary btn-xs right\" type=\"submit\" name=\"Submit\" value=\"Submit\">\n";
				$html .=  "<div class='clear-fix'></div>";
			}
			$html .= "</h3>\n";
			if ( !$showavailable ) {
				$html .= "<tr align=\"center\" bgcolor=\"$this->colorHighlight\">";   
				$html .= "	<td>No other games available.</td>";
				$html .= "</tr>\n";
			} else {
				$html .= "<table class=\"sched_table\" >\n";
				$html .= "   <tr align=\"center\" bgcolor=\"$this->colorTitle\">";
				$html .= "     <th>Game No.</th>";
				$html .= "       <th>Assign to $this->rep</th>";
				$html .= "		  <th>Day</th>";
				$html .= "		  <th>Time</th>";
				$html .= "		  <th>Field</th>";
				$html .= "    	  <th>Division</th>";
				$html .= "		  <th>Home</th>";
				$html .= "	      <th>Away</th>";
				$html .= "		  <th>Referee Team</th>";
				$html .= "		</tr>";
	  
				for ( $kant=0; $kant < $kount; $kant++ ) {
					if ( ( $showgroup && $showgroup == $this->divisionAge( $div[$kant] ) ) || !$showgroup ) {
						if ( $a_init != substr( $home[$kant], 0, 1) && $a_init != substr( $away[$kant], 0, 1) && !$ref_team[$kant] && $showavailable ) {
			   
							if ( !$testtime ) { $testtime = $time[$kant]; }
							elseif ( $testtime != $time[$kant] ) {
								$testtime = $time[$kant];
								$tempcolor = $color1;
								$color1 = $color2;
								$color2 = $tempcolor;
							}
							$html .= "		<tr align=\"center\" bgcolor=\"$color1\">";
							$html .= "		<td>$game_no[$kant]</td>";
							$html .= "		<td><input type=\"checkbox\" name=\"assign:$game_id[$kant]\" value=\"$game_id[$kant]\"></td>";
							$html .= "		<td>$day[$kant]<br>$date[$kant]</td>";
							$html .= "		<td>$time[$kant]</td>";
							$html .= "		<td>$field[$kant]</td>";
							$html .= "		<td>$div[$kant]</td>";
							$html .= "		<td>$home[$kant]</td>";
							$html .= "		<td>$away[$kant]</td>";
							$html .= "		<td>&nbsp;</td>";
							$html .= "		</tr>\n";
						}
					}
				}
			}
			$html .= "            </table>";
	  
			$html .= "	  <h3>Assigned games</h3>\n";
			if ( empty($kount) ) {
				$html .= "	  <table class=\"sched_table\" >\n";
				$html .= "		<tr align=\"center\" bgcolor=\"$this->colorHighlight\">";   
				$html .= "		<td>$this->rep has no games assigned.</td>";
				$html .= "		</tr>\n";
			} else {            
				$html .= "	  <table class=\"sched_table\" >\n";
				$html .= "	    <tr align=\"center\" bgcolor=\"$this->colorTitle\">\n";
				$html .= "		<th>Game No.</th>\n";
				$html .= "		<th>Assigned</th>\n";
				$html .= "		<th>Day</th>\n";
				$html .= "		<th>Time</th>\n";
				$html .= "		<th>Field</th>\n";
				$html .= "		<th>Division</th>\n";
				$html .= "		<th>Home</th>\n";
				$html .= "		<th>Away</th>\n";
				$html .= "		<th>Referee Team</th>\n";
				$html .= "          </tr>\n";
		  
				for ( $kant=0; $kant < $kount; $kant++ ) {
				   if ( $this->rep == $ref_team[$kant]) {
						$html .= "		<tr align=\"center\" bgcolor=\"$this->colorGroup\">";
						$html .= "		<td>$game_no[$kant]</td>";
						if ( $locked ) {
						   $html .= "		<td>Locked</td>";
						}
						else {
						   $html .= "		<td><input name=\"games:$game_id[$kant]\" type=\"checkbox\" value=\"$game_id[$kant]\" checked></td>";
						}
						$html .= "		<td>$day[$kant]<br>$date[$kant]</td>";
						$html .= "		<td>$time[$kant]</td>";
						$html .= "		<td>$field[$kant]</td>";
						$html .= "		<td>$div[$kant]</td>";
						$html .= "		<td>$home[$kant]</td>";
						$html .= "		<td>$away[$kant]</td>";
						$html .= "		<td>$ref_team[$kant]</td>";
						$html .= "		</tr>\n";
					}
				}
				}
			$html .= "            </table>";
			if ( $locked && $allatlimit ) {
				$html .= "<h3>Submit Disabled</h3>\n";
			}
			else {
				if ( !$locked && (!$allatlimit && !empty($assigned_list) || $showavailable) ) {
					$html .=  "      <input class=\"btn btn-primary btn-xs right\" type=\"submit\" name=\"Submit\" value=\"Submit\">\n";
					$html .=  "      <div class='clear-fix'></div>";
				}
			}
			$html .= "            </form>\n";      
			$_SESSION['locked'] = $locked;
	
			if ( $this->rep == 'Section 1' ) {
				$html .=  "<center><h1>You should be on this<br>";
				$html .= "<a href=\"$this->masterPath\">Schedule Page</a></h1>";
			}
		}
		else {
			$html .=  $this->errorCheck();
		}
		
        return $html;
          
    }
    private function menu()
    {
        $html =
<<<EOD
    <h3 align="center"><a href="$this->greetPath">Home</a>&nbsp;-&nbsp;
    <a href="$this->fullPath">View the full schedule</a>&nbsp;-&nbsp;
    <a href="$this->refsPath">Edit $this->rep referees</a>&nbsp;-&nbsp;
    <a href="$this->endPath">Log off</a></h3>
EOD;
        
        return $html;
    }
}
