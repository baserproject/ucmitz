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

namespace BaserCore\Controller;

use BaserCore\Controller\Component\BcContentsComponent;
use BaserCore\Model\Entity\Page;
use BaserCore\Model\Table\PagesTable;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\Exception\MissingViewException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PagesController
 * @property PagesTable $Pages
 * @property BcContentsComponent $BcContents
 */
class PagesController extends AppController
{

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	// TODO ucmitz 未移行
	/* >>>
	public $helpers = [
		'Html', 'Session', 'BcGooglemaps',
		'BcXml', 'BcText',
		'BcFreeze', 'BcPage'
	];
	<<< */

	/**
	 * コンポーネント
	 *
	 * @var array
	 * @deprecated useViewCache 5.0.0 since 4.0.0
	 *    CakePHP3では、ビューキャッシュは廃止となるため、別の方法に移行する
	 */
	// TODO ucmitz 未移行
	/* >>>
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail', 'BcContents' => ['useForm' => true, 'useViewCache' => true]];
    <<< */

    /**
     * initialize
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcContents');
    }

	/**
	 * ビューを表示する
	 *
	 * @return \Cake\Http\Response|void
     * @throws ForbiddenException When a directory traversal attempt.
	 * @throws NotFoundException When the view file could not be found
	 *   or MissingViewException in debug mode.
	 */
	public function display()
	{
		$path = func_get_args();

		if ($this->request->getParam('Content')->alias_id) {
			$urlTmp = $this->Content->field('url', ['Content.id' => $this->request->getParam('Content')->alias_id]);
		} else {
			$urlTmp = $this->request->getParam('Content')->url;
		}

		if ($this->request->getParam('Content')->alias) {
		    $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
			$site = $sites->findByUrl($urlTmp);
			if ($site && ($site->alias == $this->request->getParam('Site')->alias)) {
				$urlTmp = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/' . $this->request->getParam('Site')->name . '/', $urlTmp);
			}
		}

		if (isset($urlTmp)) {
			$urlTmp = preg_replace('/^\//', '', $urlTmp);
			$path = explode('/', $urlTmp);
		}
		// <<<

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		if (in_array('..', $path, true) || in_array('.', $path, true)) {
			throw new ForbiddenException();
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		$previewCreated = false;
		if ($this->request->getData()) {
			// POSTパラメータのコードに含まれるscriptタグをそのままHTMLに出力するとブラウザによりXSSと判定される
			// 一度データをセッションに退避する
			if ($this->BcContents->preview === 'default') {
				$sessionKey = __CLASS__ . '_preview_default_' . $this->request->getData('Content.entity_id');
				$this->request = $this->request->withParsedBody($this->Content->saveTmpFiles($this->request->getData(), mt_rand(0, 99999999)));
				$this->Session->write($sessionKey, $this->request->getData());
				$query = [];
				if ($this->request->getQuery()) {
					foreach($this->request->getQuery() as $key => $value) {
						$query[] = $key . '=' . $value;
					}
				}
				$redirectUrl = '/';
				if ($this->request->getPath()) {
					$redirectUrl .= $this->request->getPath();
				}
				if ($query) {
					$redirectUrl .= '?' . implode('&', $query);
				}
				$this->redirect($redirectUrl);
				return;
			}

			if ($this->BcContents->preview === 'draft') {
				$this->request = $this->request->withParsedBody($this->Content->saveTmpFiles($this->request->getData(), mt_rand(0, 99999999)));
				$this->request->withParam('Content.eyecatch', $this->request->getData('Content.eyecatch'));
				$uuid = $this->_createPreviewTemplate($this->request->getData());
				$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt'));
				$previewCreated = true;
			}

		} else {

			// プレビューアクセス
			if ($this->BcContents->preview === 'default') {
				$sessionKey = __CLASS__ . '_preview_default_' . $this->request->getParam('Content.entity_id');
				$previewData = $this->request->getSession()->read($sessionKey);
				$this->request->withParam('Content.eyecatch', $previewData['Content']['eyecatch']);

				if (!is_null($previewData)) {
					$this->request->getSession()->delete($sessionKey);
					$uuid = $this->_createPreviewTemplate($previewData);
					$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt'));
					$previewCreated = true;
				}
			}

			// 草稿アクセス
			if ($this->BcContents->preview === 'draft') {
				$data = $this->Page->find('first', ['conditions' => ['Page.id' => $this->request->getParam('Content.entity_id')]]);
				$uuid = $this->_createPreviewTemplate($data, true);
				$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt'));
				$previewCreated = true;
			}
		}

		$page = $this->Pages->find()->where(['Pages.id' => $this->request->getParam('Content.entity_id')])->first();
		/* @var Page $page */
		$template = $page->page_template;
		$pagePath = implode('/', $path);
		if (!$template) {
		    $contentFolders = TableRegistry::getTableLocator()->get('BaserCore.ContentFolders');
			$template = $contentFolders->getParentTemplate($this->request->getParam('Content.id'), 'page');
		}
		$this->set('pagePath', $pagePath);

		try {
			$this->render('/Pages/templates/' . $template);
			if ($previewCreated) {
				@unlink(TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt'));
			}
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

	/**
	 * プレビュー用テンプレートを生成する
	 *
	 * 一時ファイルとしてビューを保存
	 * タグ中にPHPタグが入る為、ファイルに保存する必要がある
	 *
	 * @param $data
	 * @param bool $isDraft
	 * @return string uuid
	 */
	protected function _createPreviewTemplate($data, $isDraft = false)
	{
		if (!$isDraft) {
			// postで送信される前提
			if (!empty($data['Page']['contents_tmp'])) {
				$contents = $data['Page']['contents_tmp'];
			} else {
				$contents = $data['Page']['contents'];
			}
		} else {
			$contents = $data['Page']['draft'];
		}
		$contents = $this->Page->addBaserPageTag(
			null,
			$contents,
			$data['Content']['title'],
			$data['Content']['description'],
			$data['Page']['code']
		);
		$uuid = Text::uuid();
		$path = TMP . 'pages_preview_' . $uuid . Configure::read('BcApp.templateExt');
		$file = new File($path);
		$file->open('w');
		$file->append($contents);
		$file->close();
		unset($file);
		@chmod($path, 0666);
		return $uuid;
	}

}
