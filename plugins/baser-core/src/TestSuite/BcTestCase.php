<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\TestSuite;

use App\Application;
use BaserCore\Event\BcControllerEventListener;
use BaserCore\Plugin;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Utility\Inflector;

/**
 * Class BcTestCase
 * @package BaserCore\TestSuite
 */
class BcTestCase extends TestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Set Up
     * @checked
     * @noTodo
     */
    public function setUp(): void
    {
        parent::setUp();
        $application = new Application(CONFIG);
        $application->bootstrap();
        $builder = Router::createRouteBuilder('/');
        $application->routes($builder);
        $plugin = new Plugin();
        $plugin->bootstrap($application);
        $plugin->routes($builder);
    }

    /**
     * Request を取得する
     *
     * @param string $url
     * @return ServerRequest
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getRequest($url = '/')
    {
        $request = new ServerRequest(['url' => $url]);
        $params = Router::parseRequest($request);
        $request = $request->withAttribute('params', $params);
        Router::setRequest($request);
        return $request;
    }

    /**
     * サンプル用のユーザーを取得する
     *
     * @param string $group
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function getUser($id = 1)
    {
        $userTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $user = $userTable->find()
            ->where(['Users.id' => $id])
            ->contain(['UserGroups'])
            ->first();
        return $user;
    }

    /**
     * 管理画面にログインする
     *
     * @param string $group
     * @return object $user
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function loginAdmin($id = 1)
    {
        $sessionKey = Configure::read('BcPrefixAuth.Admin.sessionKey');
        $user = $this->getUser($id);
        $this->session([$sessionKey => $user]);
        // IntegrationTestTrait が提供するsession だけでは、テスト中に取得できないテストがあったため
        // request から取得する session でも書き込むようにした
        $session = $this->getRequest()->getSession();
        $session->write($sessionKey, $user);
        return $user;
    }

    /**
     * モックにコントローラーのイベントを登録する
     * @param $eventName
     * @param $callback
     * @return BcControllerEventListener|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function entryControllerEventToMock($eventName, $callback)
    {
        $aryEventName = explode('.', $eventName);
        $methodName = Inflector::variable(implode('_', $aryEventName));
        // モック作成
        $listener = $this->getMockBuilder(BcControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods([$methodName])
            ->getMock();
        // イベント定義
        $listener->method('implementedEvents')
            ->willReturn([$eventName => ['callable' => $methodName]]);
        // コールバック定義
        $listener->method($methodName)
            ->willReturn($this->returnCallback($callback));
        EventManager::instance()->on($listener);
        return $listener;
    }

}
