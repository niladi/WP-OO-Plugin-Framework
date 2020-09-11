<?php

namespace WPPluginCoreTest\Unit;

use Mockery;
use WPPluginCore\App;
use WPPluginCore\Service\Date;
use WPPluginCore\Logger;
use WPPluginCore\Service\UserPage;
use WPPluginCore\Web\Endpoints\Ajax;
use WPPluginCore\Service\Application;
use WPPluginCore\Service\Implementation\Date as ImplementationDate;
use WPPluginCore\Web\Endpoints\Token;
use WPPluginCore\Service\LicenceUpload;
use WPPluginCore\Service\Wordpress\CF7;
use WPPluginCore\Service\Wordpress\Menu;
use WPPluginCore\Service\Wordpress\User;
use WPPluginCore\Service\Wordpress\Order;
use WPPluginCore\Web\Endpoints\Dashboard;
use WPPluginCore\Web\Endpoints\Userpages;
use WPPluginCore\Service\Wordpress\Assets;
use WPPluginCore\Service\JWTImplementation;
use WPPluginCore\Service\Wordpress\Settings;
use WPPluginCore\Service\Wordpress\JWTAdapter;
use WPPluginCoreTest\Unit\TestHelper\TestCase;
use WPPluginCore\Service\Wordpress\Entity\Save;
use WPPluginCore\Service\Wordpress\Subscription;
use WPPluginCore\Service\Wordpress\Entity\WPEntity;
use WPPluginCore\Service\Wordpress\Entity\Metaboxes;
use WPPluginCore\Service\Wordpress\Assets\Implementation\PDF;
use WPPluginCore\Service\Wordpress\Entity\PostTypeRegistration;
use WPPluginCore\Service\Wordpress\Assets\Implementation\Papaparse;
use WPPluginCore\Service\Wordpress\Dashboard as WordpressDashboard;
use WPPluginCore\Service\Wordpress\Assets\Implementation\CustomAjax;
use WPPluginCore\Service\Wordpress\Assets\Implementation\CustomAdmin;
use WPPluginCore\Service\Wordpress\Assets\Implementation\CustomMetabox;
use WPPluginCore\Service\Wordpress\Assets\Implementation\JSONAttribute;
use WPPluginCore\Service\Wordpress\Assets\Implementation\Dashboard as DashboardRessource;
/**
 * Class AppTest.
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 *
 * @covers \WPPluginCore\App
 */
class AppTest extends TestCase
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var string
     */
    protected $file;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->file = __FILE__;
        $this->app = new App($this->file, $this->file, 'wp-test', true);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->app);
        unset($this->file);
    }

    public function testRun(): void
    {      

        $classes = array(
            Metaboxes::class,
            Save::class,
            PostTypeRegistration::class,
            ImplementationDate::class,
            CustomAdmin::class,
            CustomAjax::class,
            CustomMetabox::class,
            DashboardRessource::class,
            JSONAttribute::class,
            Papaparse::class
        );
        
        foreach ($classes as $class) {
            self::assertFalse($class::isRegistered(), 'This class should be not registerd : ' . $class);
        }
        
        $this->app->run();

        foreach ($classes as $class) {
            self::assertTrue($class::isRegistered(),'This class should be registered : ' . $class );
        }
    }
}
