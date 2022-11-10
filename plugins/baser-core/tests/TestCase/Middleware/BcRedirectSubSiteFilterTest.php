<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Middleware\BcRedirectSubSiteFilter;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcRedirectSubSiteFilterTest
 * @property BcRedirectSubSiteFilter $BcRedirectSubSiteFilter
 */
class BcRedirectSubSiteFilterTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Pages',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcRedirectSubSiteFilter = new BcRedirectSubSiteFilter();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcRedirectSubSiteFilter);
        parent::tearDown();
    }

    /**
     * test Process
     */
    public function test_process(): void
    {
        SiteFactory::make([
            'id' => 1,
            'name' => '',
            'title' => 'baserCMS inc.',
            'status' => true
        ])->persist();
        SiteFactory::make([
            'id' => 2,
            'name' => '',
            'title' => 'baserCMS inc. sub',
            'status' => true,
            'main_site_id' => 1,
            'device' => 'smartphone',
            'auto_redirect' => true
        ])->persist();
        PageFactory::make(['id' => 1])->persist();
        ContentFactory::make([
            'id' => 1,
            'url' => '/about',
            'name' => 'about',
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
            'site_root' => 2,
            'status' => true
        ])->persist();
        SiteConfigFactory::make([
            'name' => 'use_site_device_setting',
            'value' => 'iPhone'
        ])->persist();

        $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
        $request = $this->getRequest('/about')->withParam('plugin', 'BaserCore')->withParam('controller', 'Pages')->withParam('action', 'view');
        $this->_response = $this->BcRedirectSubSiteFilter->process($request, $this->Application);
        $this->assertResponseCode(302);
    }

}
