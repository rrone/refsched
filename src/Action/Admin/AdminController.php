<?php
namespace App\Action\Admin;

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Action\AbstractController;
use App\Action\SchedulerRepository;

class AdminController extends AbstractController
{
	private $userName;
	
	public function __construct(Container $container, SchedulerRepository $repository) {
		
		parent::__construct($container);
		
		$this->sr = $repository;
		
    }
    public function __invoke(Request $request, Response $response, $args)
    {
        $this->authed = isset($_SESSION['authed']) ? $_SESSION['authed'] : null;
		$this->rep = isset($_SESSION['unit']) ? $_SESSION['unit'] : null;        

         if (!$this->authed || $this->rep != 'Section 1') {
            return $response->withRedirect($this->greetPath);
         }

        $this->logger->info("Schedule user update page action dispatched");

        $result = $this->handleRequest($request);

        switch ($result) {
             case 'Cancel':

                 return $response->withRedirect($this->greetPath);

            case 'SchedTemplateExport':

                return $response->withRedirect($this->schedTemplatePath);

        }

        $content = array(
            'view' => array (
                'users' => $this->renderUsers(),
                'action' => $this->userUpdatePath,
				'message' => $this->msg,
                'messageStyle' => $this->msgStyle,
            )
        );        

        $this->view->render($response, 'admin.html.twig', $content);

        return $response;

    }
	private function handleRequest($request)
	{
        if ( $request->isPost() ) {
            if (in_array('btnUpdate', array_keys($_POST))) {
                $this->userName = $_POST['selectUser'];
                $userName = $this->userName;
                $pw = $_POST['passwordInput'];

                if (!empty($pw)) {

                    $userDb = $this->sr->getUserByName($userName);

                    if (empty($userDb)) {
                        $userData = array(
                            'name' => $userName,
                            'enabled' => false,
                        );
                    } else {
                        $userData = array(
                            'name' => $userDb->name,
                            'enabled' => $userDb->enabled,
                        );
                    }

                    $userData['hash'] = password_hash($pw, PASSWORD_BCRYPT);
                    $userData['password'] = crypt($pw, 11);

                    $this->sr->setUser($userData);

                    $this->msg = "$userName password has been updated.";
                    $this->msgStyle = "color:#000000";
                } else {
                    $this->msg = "Password may not be blank.";
                    $this->msgStyle = "color:#FF0000";
                }

                return 'Update';

            } elseif (in_array('btnCancel', array_keys($_POST))) {

                return 'Cancel';

            } elseif (in_array('btnExport', array_keys($_POST))) {

                $this->msg = null;

                return 'SchedTemplateExport';
            }
        } else {
            $this->msg = null;
        }

		return null;

	}
    private function renderUsers()
    {
		$users = $this->sr->getAllUsers();

		$selectOptions = null;
		foreach($users as $user) {
			if ($user->name == $this->userName) {
				$selectOptions .= "<option selected>$user->name</option>\n";
			}
			else {
				$selectOptions .= "<option>$user->name</option>\n";
			}
		}
		return $selectOptions;
    }
}
