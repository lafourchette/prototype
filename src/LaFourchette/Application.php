<?php

namespace LaFourchette;

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use HipChat\HipChat;
use LaFourchette\Controller\MainControllerProvider;
use LaFourchette\Creator\VmCreator;
use LaFourchette\Entity\Vm;
use LaFourchette\Ldap\LdapManager;
use LaFourchette\Ldap\MockLdapManager;
use LaFourchette\Manager\IntegManager;
use LaFourchette\Manager\UserManager;
use LaFourchette\Manager\UserNotifyManager;
use LaFourchette\Manager\VmManager;
use LaFourchette\Notify\Expired;
use LaFourchette\Notify\ExpireSoon;
use LaFourchette\Notify\Killed;
use LaFourchette\Notify\Ready;
use LaFourchette\Notify\UnableToStart;
use LaFourchette\Provisioner\Vagrant;
use LaFourchette\Service\NotifyService;
use LaFourchette\Service\VmService;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Knp\Provider\ConsoleServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct();

        $app = $this;

        $app->register(new UrlGeneratorServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => array(__DIR__ . '/../../templates'),
            'twig.options' => array('cache' => __DIR__ . '/../../cache/twig'),
        ));

        $versionFile = __DIR__ . '../VERSION';
        $version = file_exists($versionFile) ? file_get_contents($versionFile) : 'dev';

        $app->register(new ConsoleServiceProvider(), array(
            'console.name'              => 'Prototype',
            'console.version'           => $version,
            'console.project_directory' => __DIR__.'/../..'
        ));

        $app['config'] = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true);
        $app['debug'] = $debug = $app['config']['debug'];

        if ($debug) {
            $app->register(new \Silex\Provider\MonologServiceProvider(), array(
                'monolog.logfile' => __DIR__.'/../../logs/silex_dev.log',
            ));
        }

        $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
            // add custom globals, filters, tags, ...
            $twig->addExtension(new \LaFourchette\Twig\Extensions\LaFourchettePrototypeExtension($app['integ.manager']));
            $twig->addGlobal('asset_version', $app['config']['asset.version']);

            return $twig;
        }));

        $app->before( function () use ($app) {
            $flash = $app[ 'session' ]->get( 'flash' );
            $app[ 'session' ]->set( 'flash', null );

            if ( !empty( $flash ) ) {
                $app[ 'twig' ]->addGlobal( 'flash', $flash );
            }
        });

        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver' => 'pdo_sqlite',
                'path' => __DIR__ . '/../../db.sqlite3',
            ),
        ));

        $app->register(new SessionServiceProvider());

        $app->register(new DoctrineOrmServiceProvider(), array(
            "orm.proxies_dir" => __DIR__ . '/../../cache/doctrine/proxies',
            "orm.em.options" => array(
                "mappings" => array(
                    array(
                        "type" => "annotation",
                        "use_simple_annotation_reader" => false,
                        "namespace" => "LaFourchette\Entity",
                        "path" => __DIR__ . "/../src/LaFourchette/Entity",
                    )
                ),
            ),
        ));

        $app['vm.manager'] = $app->share(function () use ($app) {
            return new VmManager($app['orm.em'],'\LaFourchette\Entity\Vm');
        });

        $app['integ.manager'] = $app->share(function () use ($app) {
            return new IntegManager($app['orm.em'], $app['config'],'\LaFourchette\Entity\Integ');
        });

        $app['user.manager'] = $app->share(function () use ($app) {
            return new UserManager($app['orm.em'],'\LaFourchette\Entity\User');
        });

        $app['user_notify.manager'] = $app->share(function () use ($app) {
            return new UserNotifyManager($app['orm.em'],'\LaFourchette\Entity\UserNotify');
        });

        $app['notify.service'] = $app->share(function () use ($app) {
            $notify = new NotifyService(/*$app['hipchat.client']*/);
            $notify->addNotifyMessage('expired', new Expired());
            $notify->addNotifyMessage('expire_soon', new ExpireSoon($app['config']['vm.to_expire_in']));
            $notify->addNotifyMessage('ready', new Ready());
            $notify->addNotifyMessage('killed', new Killed());
            $notify->addNotifyMessage('unable_to_start', new UnableToStart());

            return $notify;
        });

        $app['vm.provisionner2'] = $app->share(function () use ($app) {
            //TODO: use a factory
            $provisionner = new Vagrant(
                $app['integ.manager'],
                isset($app['config']['provisioners']) ? $app['config']['provisioners'] : array()
            );

            return $provisionner;
        });

        $app['vm.service'] = $app->share(function () use ($app) {
            $vmService = new VmService();
            $vmService->setVmManager($app['vm.manager']);
            $vmService->setProvisionner(Vm::TYPE_V2, $app['vm.provisionner2']);
            //$vmService->setNotifyService($app['notify.service']);
            $vmService->setDispatcher($app['notify.service']);

            return $vmService;
        });

        $app['login.basic_login_response'] = $app->share(function () use ($app) {
            $response = new Response();
            $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Ldap Authentication'));
            $response->setStatusCode(401, 'Please sign in.');

            return $response;
        });

        $app['ldap.manager'] = $app->share(function () use ($app) {

            if ($app['debug']) { // anonymous connection in debug mode

                return new MockLdapManager();
            }

            return new LdapManager(
                $app['config']['ldap.host'],
                $app['config']['ldap.port'],
                $app['config']['ldap.username'],
                $app['config']['ldap.password'],
                $app['config']['ldap.basedn']
            );
        });

        $app['hipchat.client'] = $app->share(function () use ($app) {
            return new HipChat($app['config']['hipchat']); // notification...
        });

        // Controller
        $app->mount('/', new MainControllerProvider());
    }
}
