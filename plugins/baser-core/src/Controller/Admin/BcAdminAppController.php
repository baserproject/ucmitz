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

namespace BaserCore\Controller\Admin;
use Cake\Event\EventInterface;
use BaserCore\Controller\AppController;
use Cake\Utility\Inflector;
use Exception;

/**
 * Class BcAdminAppController
 * @package BaserCore\Controller\Admin
 */
class BcAdminAppController extends AppController
{
    /**
     * Initialize
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcMessage');
        $this->loadComponent('Authentication.Authentication');
    }

	/**
	 * 画面の情報をセットする
	 *
	 * @param array $targetModel ターゲットとなるモデル
	 * @param array $options オプション
	 */
	protected function setViewConditions($targetModel = [], $options = []): void
	{
		$this->saveViewConditions($targetModel, $options);
		$this->loadViewConditions($targetModel, $options);
	}

	/**
	 * 画面の情報をセッションに保存する
	 *
	 * @param array $filterModels
	 * @param array $options オプション
	 * @return    void
	 * @access    protected
	 */
	protected function saveViewConditions($targetModel = [], $options = []): void
	{
		$options = array_merge([
		    'action' => '',
		    'group' => '',
		    'post' => true,
		    'get' => true,
		    'named' => true
		], $options);

		if (!$options['action']) {
			$options['action'] = $this->request->getParam('action');
		}
		$contentsName = $this->name . Inflector::classify($options['action']);
		if ($options['group']) {
			$contentsName .= "." . $options['group'];
		}

		if (!is_array($targetModel)) {
			$targetModel = [$targetModel];
		}

        $session = $this->request->getSession();

        if($options['post']) {
            if ($targetModel) {
                foreach($targetModel as $model) {
                    if ($this->request->getData($model)) {
                        $session->write("BcApp.viewConditions.{$contentsName}.data.{$model}", $this->request->getData($model));
                    }
                }
            } else {
                if ($this->request->getData()) {
                    $session->write("BcApp.viewConditions.{$contentsName}.data", $this->request->getData());
                }
            }
        }

        if($options['get'] && $this->request->getQuery()) {
            $session->write("BcApp.viewConditions.{$contentsName}.query", $this->request->getQuery());
        }

		if ($options['named'] && $this->request->getParam('named')) {
			if ($session->check("BcApp.viewConditions.{$contentsName}.named")) {
				$named = array_merge($session->read("BcApp.viewConditions.{$contentsName}.named"), $this->request->getParam('named'));
			} else {
				$named = $this->request->getParam('named');
			}
			$session->write("BcApp.viewConditions.{$contentsName}.named", $named);
		}
	}

	/**
	 * 画面の情報をセッションから読み込む
	 *
	 * @param array $targetModel
	 * @param array|string $options オプション
	 */
	protected function loadViewConditions($targetModel = [], $options = []): void
	{
		$options = array_merge([
		    'default' => [],
		    'action' => '',
		    'group' => '',
		    'post' => true,
		    'get' => true,
		    'named' => true
		], $options);

		if (!$options['action']) {
			$options['action'] = $this->request->getParam('action');
		}
		$contentsName = $this->name . Inflector::classify($options['action']);
		if ($options['group']) {
			$contentsName .= "." . $options['group'];
		}

		if (!is_array($targetModel)) {
			$targetModel = [$targetModel];
		}

        $session = $this->request->getSession();

		if ($targetModel) {
            foreach($targetModel as $model) {
                if ($session->check("BcApp.viewConditions.{$contentsName}.data.{$model}")) {
                    $data = $session->read("BcApp.viewConditions.{$contentsName}.data.{$model}");
                } elseif (!empty($options['default'][$model])) {
                    $data = $options['default'][$model];
                } else {
                    $data = [];
                }
                if($data) {
                    $this->request = $this->request->withData($model, $data);
                }
            }
        }

        $query = [];
        if ($session->check("BcApp.viewConditions.{$contentsName}.query")) {
            $query = $data = $session->read("BcApp.viewConditions.{$contentsName}.query");
            unset($query['url']);
            unset($query['ext']);
            unset($query['x']);
            unset($query['y']);
        }
        if (empty($query) && !empty($options['default']['query'])) {
            $query = $options['default']['query'];
        }
        if($query) {
            $this->request = $this->request->withQueryParams($query);
        }

        $named = [];
        if (!empty($options['default']['named'])) {
            $named = $options['default']['named'];
        }
        if ($session->check("BcApp.viewConditions.{$contentsName}.named")) {
            $named = array_merge($named, $session->read("BcApp.viewConditions.{$contentsName}.named"));
        }

        $named['?'] = $query;
        $this->request = $this->request->withParam('pass', $named);
	}

    /**
     * Before Render
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     */
	public function beforeRender(EventInterface $event): void
	{
	    $this->viewBuilder()->setClassName('BaserCore.BcAdminApp');
		$this->viewBuilder()->setTheme('BcAdminThird');
	}

    /**
     * Set Title
     * @param string $title
     */
    protected function setTitle($title): void
    {
        $this->set('title', $title);
    }

    /**
     * Set Search
     * @param string $template
     */
	protected function setSearch($template): void
	{
        $this->set('search', $template);
	}

    /**
     * Set Help
     * @param string $template
     */
	protected function setHelp($template): void
    {
        $this->set('help', $template);
    }

}
