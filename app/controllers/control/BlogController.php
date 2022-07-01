<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Entity\Filter;
use Entity\Result;
use Entity\DateTime;
use Layout\Blog\Entity\Post;

class BlogController extends AbstractControlController
{
	protected $module = 'blog';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'blog';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		//Фильтрация
		$filter = new Filter([
			'Page'		=> $this->input()->get('page') ?: 1,
			'PerPage'	=> ADMIN_PERPAGE,
			'order_by'	=> 'PostId',
			'order_dir'	=> 'DESC',
		]);

		// Language
		if (!is_null($this->input()->get('Language')))
			$filter->set('Language', strtolower($this->input()->get('Language')));

		// Status
		if (!is_null($this->input()->get('status'))) {
			$v = strval($this->input()->get('status'));
			switch($v){
				case 'all':break;
				default: $filter->set('status', $v); break;
			}
		}

		// Search
		if (trim($this->input()->get('search')))
			$filter->set('search', trim($this->input()->get('search')));

		$filter->formLimits();
		$this->vars['filter'] = $filter;

		$this->vars['posts'] = $this->layout()->blog()->getPosts($filter);

		$filter->setTotal($this->layout()->blog()->getTotal());

		// Постраничная навигация
		$this->pagination()->initialize([
			'page'		=> $filter->get('Page'),
			'perPage'	=> $filter->get('PerPage'),
			'total'		=> $filter->getTotal(),
			'template'	=> ADMIN.'/_units/pagination',
		]);
		$this->vars['pagination'] = $this->pagination()->getHTML();

		// Сохраняем текущие настройки фильтра
		$this->_setBackQuery();

		$this->addCss[] = '/admin/css/modules/blog.css'.Func::modifyTime('/admin/css/modules/blog.css');
		$this->addJs[] = '/admin/js/modules/blog.js'.Func::modifyTime('/admin/js/modules/blog.js');
		$this->layout()->page()->setHeader('Список постов блога');
		$this->tpl()->template($this->module.'/posts/index');
	}

	public function create()
	{
		$this->_create();
		$this->layout()->page()->setHeader('Новый пост');
		$this->tpl()->template($this->module.'/posts/create');
	}

	private function _create()
	{
		if ($this->input()->post()) {

			$errors = [];
			$this->vars['post'] = $post = $this->input()->post('field');

			$post['name'] = trim($post['name']);
			if (!$post['name']) $errors[] = 'Не указана сумма';

			// Если насобирали ошибки, то выход..
			if ($errors)
				return $this->_notify(implode('<br/>', $errors), false);

			// добавляем запись
			$item = $this->layout()->blog()->_newPost()
				->setLanguage($post['Language'])
				->setName($post['name'])
				->setCreated(new DateTime());

			if (!$this->layout()->blog()->createPost($item))
				return $this->_notify('Ошибка создания', false);

			$this->_notify('Элемент успешно создан');
			Func::redirect(ADMIN . '/' . $this->module);
		}
	}

	public function delete()
	{
		if (!$this->input()->isAjax()) die();

		if ($this->layout()->blog()->deletePost(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['descr'] = 'Элемент удален';
		}else{
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}
		die (json_encode($this->ajaxResponse));
	}

	public function toggle()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = intval($this->input()->post('id', true));
		$field = $this->input()->post('field', true);
		$value = intval($this->input()->post('value', true));

		if (!in_array($field, ['Main', 'status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Неверное поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		$u = $this->layout()->blog()->_newPost();
		switch ($field) {
			case 'Main': $u->setMain($value ? 'active' : 'hidden'); break;
			case 'status': $u->setStatus($value ? 'active' : 'hidden'); break;
		}

		// Обновление
		if (!$this->layout()->blog()->updatePost($id, $u->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function edit($id = false)
	{
		$post = $this->layout()->blog()->getPost($id);
		if (!$post) $this->show404();

		// Saving
		$save = $this->_save($post);
		if ($save->getMessage()) $this->_notify($save->getMessage());
		elseif ($save->getError()) $this->_notify($save->getError(), false);
		if ($save->success())
			$post = $this->layout()->blog()->getPost($id);

		$this->vars['post'] = $post;

		$this->addJs[] = '/admin/js/dropzone.js';
		$this->addJs[] = '/admin/js/ajaxupload.3.5.js';
		$this->addJs[] = '/admin/js/jquery-ui.sortable.min.js';
		$this->addJs[] = '/admin/js/modules/blog.js'.Func::modifyTime('/admin/js/modules/blog.js');
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.js';
		$this->addJs[] = '/admin/js/ckeditor/adapters/jquery.js';
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.init.js';
		$this->addCss[] = '/admin/css/modules/blog.css'.Func::modifyTime('/admin/css/modules/blog.css');

		$this->layout()->page()->setHeader('Редактирование поста');
		$this->tpl()->template($this->module.'/posts/edit');
	}

	/**
	 * @param Post
	 * @return Result
	 */
	public function _save(Post $post)
	{
		$result = new Result();

		if ($this->input()->post('save')) {
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach(array_keys($fields) as $field)
				if(!in_array($field, ['name','Alias','Header','title','Tags','Preview','text','Robots','Keywords','description','status']))
					unset($fields[$field]);

			if (!$fields['name'])
				$result->setError($result->getError() . 'Не указано описание' . '<br />');

			$alias = \Helper\Text::translitUrl($fields['Alias'] ?: ($fields['Header'] ?: ($fields['title'] ?: $fields['name'])));
			if (!$alias)
				$result->setError($result->getError() . 'Не указан Алиас' . '<br />');
			else if (false != ($exist = $this->layout()->blog()->getPostByAlias($alias)) && $exist->getId() != $post->getId())
				$result->setError($result->getError() . 'Указанный Алиас уже есть в системе' . '<br />');
			$fields['Alias'] = $alias;

			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			$n = $this->layout()->blog()->_newPost()
				->setName($fields['name'])
				->setAlias($fields['Alias'])
				->setHeader($fields['Header'])
				->setTitle($fields['title'])
				->setText($fields['text'])
				->setTags($fields['Tags'])
				->setPreview($fields['Preview'])
				->setRobots($fields['Robots'])
				->setStatus($fields['status'])
				->setKeywords($fields['Keywords'])
				->setDescription($fields['description']);

			// обновляем запись
			if ($this->layout()->blog()->updatePost($post->getId(), $n->toArray()))
				$result->setStatus(true)->setMessage('Изменения успешно сохранены');
			else
				$result->setError('Ошибка обновления');
		}

		return $result;
	}

	public function uploadPhoto()
    {
		$blogId = $this->input()->post('id', true);
		$post = $this->layout()->blog()->getPost($blogId);
		if (!$post) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		//Определяем параметры загрузки Картинки
		$upl = new Upload([
			'upload_path'	=> VF_TMP_DIR,
			'allowed_types'	=> 'jpg|jpeg|png|bmp',
			'max_size'		=> '10120',
			'overwrite'		=> true,
		]);

		if ($upl->do_upload('file')) {
			$upInfo = $upl->data();
			$fileName = str_pad($post->getId(), 3, '0', STR_PAD_LEFT) . '_' . md5(microtime(1)) . strtolower($upInfo['file_ext']);	//Имя конечного файла
			$filePath = BLOG_PATH . '/' . $fileName; //Полный путь

			if(!isset($upInfo['image_width']) || !isset($upInfo['image_height']) || !$upInfo['image_width'] || !$upInfo['image_height']){
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Размер не определен';
				@unlink($upInfo['full_path']);
				die (json_encode($this->ajaxResponse));
			}

			// Переносим/переименовываем файл
			if (@rename($upInfo['full_path'], $filePath)) {

				$maxAllowedSize = 800;

				if ($maxAllowedSize < max($upInfo['image_width'], $upInfo['image_height'])) {
					//обработка картинки
					$img = new Image([
						'source_image'	=> $filePath,
						'quality'		=> 98,
						'height'		=> $maxAllowedSize,
						'width'			=> $maxAllowedSize,
					]);
					$img->resize();
				}

				$n = $this->layout()->blog()->_newPost()->setPhotoSmall(BLOG_URL . '/' . $fileName);
				if ($this->layout()->blog()->updatePost($post->getId(), $n->toArray())) {
					$this->ajaxResponse['descr'] = 'Файл успешно добавлен';
					$this->ajaxResponse['src'] = BLOG_URL . '/' . $fileName;
					$this->ajaxResponse['del'] = true;
					// Unlink old photo
					if ($post->getPhotoSmall() && is_file(VF_PUBLIC_DIR . $post->getPhotoSmall()))
						@unlink(VF_PUBLIC_DIR . $post->getPhotoSmall());
				} else {
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Ошибка сохранения';
				}

				die (json_encode($this->ajaxResponse));
			}else{
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Ошибка переноса файла';
				die (json_encode($this->ajaxResponse));
			}
		}elseif($upl->display_errors()){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $upl->display_errors('','');//'Ошибка загрузки файла';
			die (json_encode($this->ajaxResponse));
		}

		die (json_encode($this->ajaxResponse));
    }

	public function deletePhoto()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$blogId = $this->input()->post('id', true);
		$post = $this->layout()->blog()->getPost($blogId);
		if (!$post) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->layout()->blog()->_newPost()->setPhotoSmall(false);
		if ($this->layout()->blog()->updatePost($post->getId(), $n->toArray())) {
			$this->ajaxResponse['descr'] = 'Файл удален';
			$this->ajaxResponse['del'] = false;
			$this->ajaxResponse['src'] = BLOG_DEFAULT_PHOTO;
			// Unlink photo
			if ($post->getPhotoSmall() && is_file(VF_PUBLIC_DIR . $post->getPhotoSmall()))
				@unlink(VF_PUBLIC_DIR . $post->getPhotoSmall());
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка сохранения';
		}

		die (json_encode($this->ajaxResponse));
	}

// FILES
	public function getFiles()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$blog = $this->blog()->items()->get($this->input()->post('BlogId', true));
		if(!$blog){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$resultHTML = '';
		foreach ($this->blog()->files()->gets(new Filter(['BlogId'	=> $blog->getId()])) as $file)
			$resultHTML .= $this->tpl()->get($this->module.'/_file_item', ['f' => $file]);
		$this->ajaxResponse['resultHTML'] = $resultHTML;

		die (json_encode($this->ajaxResponse));
	}

	public function setFilesSorting()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$str_sort = $this->input()->post('str_sort', true);

		$split = explode('&', str_replace('id=','',str_replace(' ','',$str_sort)));

		$sort = 0;
		foreach($split as $id){
			$sort = $sort + 5;
			$this->blog()->files()->update($id, ['sort' => $sort]);
		}

		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function deleteFile()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$file = $this->blog()->files()->get($this->input()->post('fileId', true));
		if(!$file){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Файл не найден';
			die(json_encode($this->ajaxResponse));
		}

		$result = $this->blog()->files()->delete($file);
		if (!$result->success()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления файла';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Файл успешно удален';

		die (json_encode($this->ajaxResponse));
	}

	public function uploadFile()
	{
		$blog = $this->blog()->items()->get($this->input()->post('BlogId', true));
		if (!$blog) die('Элемент не найден');

		//Определяем параметры загрузки Картинки
		$upl = new Upload([
			'upload_path'	=> VF_TMP_DIR,
			'allowed_types'	=> 'jpg|jpeg|png|gif|bmp',
			'max_size'		=> '16000',
			'overwrite'		=> true,
		]);
		if($upl->do_upload('files'))
		{
			$upInfo = $upl->data();
			$fileName = str_pad($blog->getId(), 3, '0', STR_PAD_LEFT) . '_' . md5(microtime(1)) . strtolower($upInfo['file_ext']);	//Имя конечного файла
			$filePath = BLOG_PATH . '/' . $fileName; //Полный путь

			if(!isset($upInfo['image_width']) || !isset($upInfo['image_height']) || !$upInfo['image_width'] || !$upInfo['image_height']){
				@unlink($upInfo['full_path']);
				die ('Размер не определен');
			}

			//Переносим/переименовываем файл
			if(@rename($upInfo['full_path'], $filePath)){

				// Params
				$imageSize = getimagesize($filePath);
				$width = $imageSize[0];
				$height = $imageSize[1];

				$file = new File();
				$file
					->setBlogId($blog->getId())
					->setWidth($width)
					->setHeight($height)
					->setSize(filesize($filePath))
					->setMime($upInfo['file_type'])
					->setPath(BLOG_URL . '/' . $fileName);

				$res = $this->blog()->files()->create($file);
				if($res->success())
					die ('success');
				else
					die ('error');
			}else{
				die ('Ошибка переноса файла');
			}
		} elseif ($upl->display_errors()) {
			trigger_error($upl->display_errors(), E_USER_ERROR);
		}

		exit();
	}
// -END FILES
}