<?php
// DIC configuration
use Twig\Extension\DebugExtension;

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
/**
 * @param \Slim\Container $c
 * @return \Slim\Views\Twig
 */
$container['view'] = function (\Slim\Container $c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    //Manage Twig base_url() returns port 80 when used over HTTPS connection
    $view['env_uri'] = $c->get('settings')['env_uri'];
    $view['assignoremail'] = $c->get('settings')['assignor']['email'];
    $view['issueTracker'] = $c->get('settings')['issueTracker'];
    $view['banner'] = $c->get('settings')['banner'];

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new DebugExtension());

    $Version = new Twig\TwigFunction('version', function () use ($settings) {
        $ver = 'Version ' . $settings['version']['version'];

        return $ver;
    });

    $twig = $view->getEnvironment();

    $twig->addFunction($Version);

    return $view;
};

// Flash messages
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};

unset($container['errorHandler']);
//$container['errorHandler'] = function ($c) {
//    if ($c['settings']['debug']) {
//        return;
//    }
//
//    return function ($request, $response, $exception) use ($c) {
//
//    var_dump($exception);                             
//
//        return $c['response']->withStatus(500)
//                             ->withHeader('Content-Type', 'text/html')
//                             ->write($exception->xdebug_message);
//        //// 404.html, or 40x.html, or 4xx.html, or error.html
//        //
//        //$templates = array(
//        //    'errors/'.$exception.'.html.twig',
//        //    'errors/'.substr($exception, 0, 2).'x.html.twig',
//        //    'errors/'.substr($exception, 0, 1).'xx.html.twig',
//        //    'errors/default.html.twig',
//        //);
//        //
//        //return new Response($container['view']->resolveTemplate($templates)->render(array('code' => $exception)), $exception);
//    };
//};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function (\Slim\Container $c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());

    //Added to remove empty brackets
    //Reference: http://stackoverflow.com/questions/13968967/how-not-to-show-last-bracket-in-a-monolog-log-line
    $handler = new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG);
    // the last "true" here tells it to remove empty []'s
    $formatter = new Monolog\Formatter\LineFormatter(null, null, false, true);
    $handler->setFormatter($formatter);
    //End of added

    $logger->pushHandler($handler);
    return $logger;
};

$container['db'] = function ($c) {
    $capsule = new Illuminate\Database\Capsule\Manager;

    $capsule->addConnection($c['settings']['dbConfig']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container['sr'] = function (\Slim\Container $c) {
    $db = $c->get('db');
    $scheduleRepository = new \App\Action\SchedulerRepository($db);

    return $scheduleRepository;
};

$container['p'] = function () {
    $parser = new FullNameParser();

    return $parser;
};

// -----------------------------------------------------------------------------
// Action dependency Injection
// -----------------------------------------------------------------------------
$db = $container['db'];

/* @var App\Action\SchedulerRepository $sr */
$sr = $container['sr'];

/** @var FullNameParser $p */
$p = $container['p'];

$view = $container['view'];
$uploadPath = $container['settings']['upload_path'];

$container[App\Action\SchedulerRepository::class] = function ($db) {

    return new \App\Action\SchedulerRepository($db);
};

// -----------------------------------------------------------------------------
// Admin class
// -----------------------------------------------------------------------------
$container[App\Action\Admin\AdminView::class] = function ($c) use ($sr) {

    return new \App\Action\Admin\AdminView($c, $sr);
};

$container[App\Action\Admin\AdminController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Admin\AdminView($c, $sr);

    return new \App\Action\Admin\AdminController($c, $v);
};

// -----------------------------------------------------------------------------
// Logon class
// -----------------------------------------------------------------------------
$container[App\Action\Logon\LogonView::class] = function ($c) use ($sr) {

    return new \App\Action\Logon\LogonView($c, $sr);
};

$container[App\Action\Logon\LogonController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Logon\LogonView($c, $sr);

    return new \App\Action\Logon\LogonController($c, $v);
};

$container[App\Action\Logon\LogonUsersController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Logon\LogonView($c, $sr);

    return new \App\Action\Logon\LogonUsersController($c, $v);
};

// -----------------------------------------------------------------------------
// Greet class
// -----------------------------------------------------------------------------
$container[App\Action\Greet\GreetView::class] = function ($c) use ($sr) {

    return new \App\Action\Greet\GreetView($c, $sr);
};

$container[App\Action\Greet\SchedGreetController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Greet\GreetView($c, $sr);

    return new \App\Action\Greet\SchedGreetController($c, $v);
};

// -----------------------------------------------------------------------------
// SchedFull class
// -----------------------------------------------------------------------------
$container[App\Action\Full\SchedFullView::class] = function ($c) use ($sr) {

    return new \App\Action\Full\SchedFullView($c, $sr);
};

$container[App\Action\Full\SchedFullController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Full\SchedFullView($c, $sr);

    return new \App\Action\Full\SchedFullController($c, $v);
};

// -----------------------------------------------------------------------------
// SchedExport class
// -----------------------------------------------------------------------------
$container[App\Action\Full\SchedExportXl::class] = function () use ($sr) {

    return new \App\Action\Full\SchedExportXl($sr);
};

$container[App\Action\Full\SchedExportController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Full\SchedExportXl($sr);

    return new \App\Action\Full\SchedExportController($c, $v);
};

// -----------------------------------------------------------------------------
// SchedSched class
// -----------------------------------------------------------------------------
$container[App\Action\Sched\SchedSchedView::class] = function ($c) use ($sr) {

    return new \App\Action\Sched\SchedSchedView($c, $sr);
};

$container[App\Action\Sched\SchedSchedController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Sched\SchedSchedView($c, $sr);

    return new \App\Action\Sched\SchedSchedController($c, $v);
};

// -----------------------------------------------------------------------------
// SchedMaster class
// -----------------------------------------------------------------------------
$container[App\Action\Master\SchedMasterView::class] = function ($c) use ($sr) {

    return new \App\Action\Master\SchedMasterView($c, $sr);
};

$container[App\Action\Master\SchedMasterController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Master\SchedMasterView($c, $sr);

    return new \App\Action\Master\SchedMasterController($c, $v);
};

// -----------------------------------------------------------------------------
// Lock & Unlock classes
// -----------------------------------------------------------------------------
$container[App\Action\Lock\SchedLockView::class] = function ($c) use ($sr) {

    return new \App\Action\Lock\SchedLockView($c, $sr);
};

$container[App\Action\Lock\SchedLockController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Lock\SchedLockView($c, $sr);

    return new \App\Action\Lock\SchedLockController($c, $v);
};

$container[App\Action\Lock\SchedUnlockController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Lock\SchedLockView($c, $sr);

    return new \App\Action\Lock\SchedUnlockController($c, $v);
};

// -----------------------------------------------------------------------------
// SchedRefs class
// -----------------------------------------------------------------------------
$container[App\Action\Refs\SchedRefsView::class] = function ($c) use ($sr) {

    return new \App\Action\Refs\SchedRefsView($c, $sr);
};

$container[App\Action\Refs\SchedRefsController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Refs\SchedRefsView($c, $sr);

    return new \App\Action\Refs\SchedRefsController($c, $v);
};

// -----------------------------------------------------------------------------
// EditRef class
// -----------------------------------------------------------------------------
$container[App\Action\EditRef\SchedEditRefView::class] = function ($c) use ($sr, $p) {

    return new \App\Action\EditRef\SchedEditRefView($c, $sr, $p);
};

$container[App\Action\EditRef\SchedEditRefController::class] = function ($c) use ($sr, $p) {
    $v = new \App\Action\EditRef\SchedEditRefView($c, $sr, $p);

    return new \App\Action\EditRef\SchedEditRefController($c, $v);
};

// -----------------------------------------------------------------------------
// SchedTemplateExport class
// -----------------------------------------------------------------------------
$container[App\Action\Admin\SchedTemplateExport::class] = function ($c) use ($sr) {

    return new \App\Action\Admin\SchedTemplateExport($c, $sr);
};

$container[App\Action\Admin\SchedTemplateExportController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Admin\SchedTemplateExport($c, $sr);

    return new \App\Action\Admin\SchedTemplateExportController($c, $v);
};

// -----------------------------------------------------------------------------
// SchedImport class
// -----------------------------------------------------------------------------
$container[App\Action\Admin\SchedImport::class] = function ($c) use ($sr, $uploadPath) {

    return new \App\Action\Admin\SchedImport($c, $sr, $uploadPath);
};

$container[App\Action\Admin\SchedImportController::class] = function ($c) use ($sr, $uploadPath) {
    $v = new \App\Action\Admin\SchedImport($c, $sr, $uploadPath);

    return new \App\Action\Admin\SchedImportController($c, $v);
};

// -----------------------------------------------------------------------------
// LogExport class
// -----------------------------------------------------------------------------
$container[App\Action\Admin\LogExport::class] = function ($c) use ($sr) {

    return new \App\Action\Admin\LogExport($c, $sr);
};

$container[App\Action\Admin\LogExportController::class] = function ($c) use ($sr) {
    $v = new \App\Action\Admin\LogExport($c, $sr);

    return new \App\Action\Admin\LogExportController($c, $v);
};

// -----------------------------------------------------------------------------
// SchedEnd class
// -----------------------------------------------------------------------------
$container[App\Action\End\SchedEndController::class] = function ($c) {

    return new \App\Action\End\SchedEndController($c);
};

// -----------------------------------------------------------------------------
// NoEventsView class
// -----------------------------------------------------------------------------
$container[App\Action\NoEvents\NoEventsView::class] = function ($c) use ($sr) {

    return new \App\Action\NoEvents\NoEventsView($c, $sr);
};

$container[App\Action\NoEvents\NoEventsController::class] = function ($c) use ($sr) {
    $v = new \App\Action\NoEvents\NoEventsView($c, $sr);

    return new \App\Action\NoEvents\NoEventsController($c, $v);
};

// -----------------------------------------------------------------------------
// EditGameView class
// -----------------------------------------------------------------------------
$container[App\Action\EditGame\EditGameView::class] = function ($c) use ($sr) {

    return new \App\Action\EditGame\EditGameView($c, $sr);
};

$container[App\Action\EditGame\EditGameController::class] = function ($c) use ($sr) {
    $v = new \App\Action\EditGame\EditGameView($c, $sr);

    return new \App\Action\EditGame\EditGameController($c, $v);
};

// -----------------------------------------------------------------------------
// FieldMapView class
// -----------------------------------------------------------------------------
$container[App\Action\PDF\ExportPDF::class] = function () {

    return new \App\Action\PDF\ExportPDF();
};

$container[App\Action\PDF\PDFController::class] = function ($c) {
    $v = new \App\Action\PDF\ExportPDF();

    return new \App\Action\PDF\PDFController($c, $v);
};

// -----------------------------------------------------------------------------
// MedalRound classes
// -----------------------------------------------------------------------------
$container[App\Action\MedalRound\MedalRoundView::class] = function ($c) use ($sr) {

    return new \App\Action\MedalRound\MedalRoundView($c, $sr);
};

$container[App\Action\MedalRound\ShowMedalRoundController::class] = function ($c) use ($sr) {
    $v = new \App\Action\MedalRound\MedalRoundView($c, $sr);

    return new \App\Action\MedalRound\ShowMedalRoundController($c, $v);
};

$container[App\Action\MedalRound\HideMedalRoundController::class] = function ($c) use ($sr) {
    $v = new \App\Action\MedalRound\MedalRoundView($c, $sr);

    return new \App\Action\MedalRound\HideMedalRoundController($c, $v);
};

$container[App\Action\MedalRound\ShowMedalRoundDivisionsController::class] = function ($c) use ($sr) {
    $v = new \App\Action\MedalRound\MedalRoundDivisionsView($c, $sr);

    $dv = new \App\Action\MedalRound\ShowMedalRoundDivisionsController($c, $v);
    return $dv;
};

$container[App\Action\MedalRound\HideMedalRoundDivisionsController::class] = function ($c) use ($sr) {
    $v = new \App\Action\MedalRound\MedalRoundDivisionsView($c, $sr);

    return new \App\Action\MedalRound\HideMedalRoundDivisionsController($c, $v);
};

// -----------------------------------------------------------------------------
// SAR Function class
// -----------------------------------------------------------------------------
$container[App\Action\SAR\SARAction::class] = function () use ($sr) {

    return new \App\Action\SAR\SARAction($sr);
};

// -----------------------------------------------------------------------------
// InfoModal class
// -----------------------------------------------------------------------------
$container[App\Action\InfoModal\InfoModalController::class] = function ($c) use ($sr) {
    $v = new \App\Action\InfoModal\InfoModalView($c, $sr);

    return new App\Action\InfoModal\InfoModalController($c, $v);
};
