<?php
namespace Tests;

use App\Action\AbstractController;
use App\Action\AbstractView;
use App\Action\Logon\LogonController;
use App\Action\Logon\LogonView;

class LogonTest extends AppTestCase
{
    protected $eventLabel;
    protected $userName;
    protected $passwd;

    /**
     *
     */
    public function setUp()
    {
//     Setup App controller
        $this->app = $this->getSlimInstance();
        $this->client = new AppWebTestClient($this->app);
    }

    /**
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testRoot()
    {
        // instantiate the view and test it

        $view = new LogonView($this->c, $this->sr);
        $this->assertTrue($view instanceof AbstractView);

        // instantiate the controller

        $controller = new LogonController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        $view = $this->client->get('/');

        $this->assertContains('<h1>Section 7 Event Schedule</h1>', $view);
    }

    /**
     *
     */
    public function testLogonAsUser()
    {
        $this->eventLabel = $this->config['user_test']['event'];
        $this->userName = $this->config['user_test']['user'];
        $this->passwd = $this->config['user_test']['passwd'];

        $url = '/';
        $headers = array(
            'cache-control' => 'no-cache',
            'content-type' => 'multipart/form-data;'
        );
        $body = array(
            'event' => $this->eventLabel,
            'user' => $this->userName,
            'passwd' => $this->passwd,
            'Submit' => 'Logon'
        );

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->post($url, $body, $headers);

        $url = implode($response->getHeader('Location'));
        $this->assertEquals('/greet', $url);

        $response = (object)$this->client->get($url);
        $view = (string)$response->getBody();
        $this->assertContains("<h3 class=\"center\">Welcome $this->userName Assignor</h3>", $view);
    }

    /**
     *
     */
    public function testLogonAsUserWithBadPW()
    {
        $this->eventLabel = $this->config['user_test']['event'];
        $this->userName = $this->config['user_test']['user'];

        $url = '/';
        $headers = array(
            'cache-control' => 'no-cache',
            'content-type' => 'multipart/form-data;'
        );
        $body = array(
            'event' => $this->eventLabel,
            'user' => $this->userName,
            'passwd' => '',
            'Submit' => 'Logon'
        );

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->post($url, $body, $headers);
        $view = (string)$response->getBody();
        $this->assertContains('<h1>Section 7 Event Schedule</h1>', $view);
        $this->assertContains("Unrecognized password for $this->userName", $view);
    }


    /**
     *
     */
    public function testLogonAsAdmin()
    {
        $this->eventLabel = $this->config['admin_test']['event'];
        $this->userName = $this->config['admin_test']['user'];
        $this->passwd = $this->config['admin_test']['passwd'];

        $url = '/';
        $headers = array(
            'cache-control' => 'no-cache',
            'content-type' => 'multipart/form-data;'
        );
        $body = array(
            'event' => $this->eventLabel,
            'user' => $this->userName,
            'passwd' => $this->passwd,
            'Submit' => 'Logon'
        );

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->post($url, $body, $headers);

        $url = implode($response->getHeader('Location'));
        $this->assertEquals('/greet', $url);

        $response = (object)$this->client->get($url);
        $view = (string)$response->getBody();
        $this->assertContains("<h3 class=\"center\">Welcome $this->userName</h3>", $view);

    }

    /**
     *
     */
    public function testLogonAsDeveloper()
    {
        $this->eventLabel = $this->config['dev_test']['event'];
        $this->userName = $this->config['dev_test']['user'];
        $this->passwd = $this->config['dev_test']['passwd'];

        $url = '/';
        $headers = array(
            'cache-control' => 'no-cache',
            'content-type' => 'multipart/form-data;'
        );
        $body = array(
            'event' => $this->eventLabel,
            'user' => $this->userName,
            'passwd' => $this->passwd,
            'Submit' => 'Logon'
        );

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->post($url, $body, $headers);

        $url = implode($response->getHeader('Location'));
        $this->assertEquals('/greet', $url);

        $response = (object)$this->client->get($url);
        $view = (string)$response->getBody();
        $this->assertContains("<h3 class=\"center\">Welcome $this->userName</h3>", $view);

    }

}