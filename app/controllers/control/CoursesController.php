<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Academy\Courses\Entity\Course;
use Academy\Courses\Entity\Lesson;
use Academy\Courses\Entity\Tariff;
use Academy\Courses\Entity\Review;
use Entity\Filter;
use Entity\Result;

class CoursesController extends AbstractControlController
{
	protected $module = 'courses';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'courses';

		$this->addCss[] = '/admin/css/modules/academy.css'.Func::modifyTime('/admin/css/modules/academy.css');
		$this->addJs[] = '/admin/js/modules/academy.js' . Func::modifyTime('/admin/js/modules/academy.js');
		$this->addJs[] = '/admin/js/jquery-ui.sortable.min.js';
		$this->addCss[] = '/admin/plugins/select2/select2.css' . Func::modifyTime('/admin/plugins/select2/select2.css');
		$this->addJs[] = '/admin/plugins/select2/select2.min.js';

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);
	}

	public function index()
	{
		$filter = new Filter(['order_by' => 'sort']);

		$this->vars['filter'] = $filter;
		$this->vars['courses'] = $this->academy()->courses()->gets($filter);
		//echo '<pre>',print_r($this->vars['courses'], 1),'</pre>'; die();

		// Сохраняем текущие настройки фильтра
		$this->_setBackQuery();

		$this->layout()->page()->setHeader('Список курсов');
		$this->tpl()->template($this->module . '/index');
	}

	public function setSorting()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) {
			die();
		}
		$str = $this->input()->post('str', true);
		$split = explode('&', str_replace('id=', '', str_replace(' ', '', $str)));
		$sort = 0;
		foreach ($split as $id) {
			$sort = $sort + 5;
			$n = $this->academy()->courses()->_new()->setSort($sort);
			$this->academy()->courses()->update($id, $n->toArray());
		}
		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function create()
	{
		$this->_create();
		$this->layout()->page()->setHeader('Новый курс');
		$this->tpl()->template($this->module . '/create');
	}

	private function _create()
	{
		if ($this->input()->post()) {
			$errors = [];
			$this->vars['post'] = $post = $this->input()->post('field');

			$post['name'] = trim($post['name']);
			if (!$post['name'])
				$errors[] = 'Не указано название курса';

			// Если насобирали ошибки, то выход..
			if ($errors)
				return $this->_notify(implode('<br/>', $errors), false);

			// добавляем запись
			$item = $this->academy()->courses()->_new()->setName($post['name']);
			if (!$this->academy()->courses()->create($item))
				return $this->_notify('Ошибка создания', false);

			$this->_notify('Элемент успешно создан');
			Func::redirect(ADMIN . "/{$this->module}/edit/{$item->getId()}");
		}
	}

	public function deleteCourse()
	{
		if (!$this->input()->isAjax()) die();

		if (!$this->academy()->courses()->delete(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Элемент удален';
		die (json_encode($this->ajaxResponse));
	}

	public function toggleCourse()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = strtolower($this->input()->post('id', true));
		$field = $this->input()->post('field', true);
		$value = intval($this->input()->post('value', true));

		if (!in_array($field, ['status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Неверное поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->academy()->courses()->_new();
		switch ($field) {
			case 'status': $n->setStatus($value ? 'active' : 'hidden'); break;
		}

		// Обновление
		if (!$this->academy()->courses()->update($id, $n->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function edit($id = false)
	{
		$course = $this->academy()->courses()->get($id);
		if (!$course) $this->show404();

		// Saving
		$save = $this->_save($course);
		if ($save->getMessage()) $this->_notify($save->getMessage());
		elseif ($save->getError()) $this->_notify($save->getError(), false);
		if ($save->success()) $course = $this->academy()->courses()->get($id);

		$this->vars['course'] = $course;
		$this->vars['lessons'] = $this->academy()->courses()->getLessons(new Filter(['course_id' => $course->getId()]));
		$this->vars['tariffs'] = $this->academy()->courses()->getTariffs(new Filter(['course_id' => $course->getId()]));
		$this->vars['reviews'] = $this->academy()->courses()->getReviews(new Filter(['course_id' => $course->getId()]));

		$this->addJs[] = '/admin/js/dropzone.js';
		$this->addJs[] = '/admin/js/ajaxupload.3.5.js';
		$this->addJs[] = '/admin/js/jquery-ui.sortable.min.js';
		$this->addCss[] = '/admin/css/modules/academy.css'.Func::modifyTime('/admin/css/modules/academy.css');
		$this->addJs[] = '/admin/js/modules/academy.js' . Func::modifyTime('/admin/js/modules/academy.js');
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.js';
		$this->addJs[] = '/admin/js/ckeditor/adapters/jquery.js';
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.init.js';
		$this->addCss[] = '/admin/plugins/select2/select2.css' . Func::modifyTime('/admin/plugins/select2/select2.css');
		$this->addJs[] = '/admin/plugins/select2/select2.min.js';
		$this->layout()->page()->setHeader('Редактирование курса');
		$this->tpl()->template($this->module . '/edit');
	}

	/**
	 * @param Course
	 * @return Result
	 */
	public function _save(Course $post)
	{
		$result = new Result();

		if ($this->input()->post('save')) {
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach (array_keys($fields) as $field)
				if (!in_array($field,['name','description','html_paid','html_unpaid','promo_title','promo_description','is_free','status']))
					unset($fields[$field]);

			$this->vars['post'] = $fields;

			if (!$fields['name'])
				$result->setError($result->getError() . 'Не указано Название курса' . '<br />');

			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			$n = $this->academy()->courses()->_new()
				->setName($fields['name'])
				->setDescription($fields['description'])
				->setHtmlPaid($fields['html_paid'])
				->setHtmlUnpaid($fields['html_unpaid'])
				->setPromoTitle($fields['promo_title'])
				->setPromoDescription($fields['promo_description'])
				->setIsFree(boolvalue($fields['is_free']))
				->setStatus($fields['status']);

			// обновляем запись
			if ($this->academy()->courses()->update($post->getId(), $n->toArray()))
				$result->setStatus(true)->setMessage('Изменения успешно сохранены');
			else
				$result->setError('Ошибка обновления');
		}

		return $result;
	}

	public function uploadCoursePhoto()
	{
		$id = $this->input()->post('id', true);
		$field = $this->input()->post('field', true);
		$course = $this->academy()->courses()->get($id);

		if (!$course) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		//Определяем параметры загрузки Картинки
		$upl = new Upload([
			'upload_path' => VF_TMP_DIR,
			'allowed_types' => 'jpg|jpeg|png|bmp',
			'max_size' => '10120',
			'overwrite' => true,
		]);
		if ($upl->do_upload('file')) {

			$upInfo = $upl->data();
			$fileName = str_pad($course->getId(), 3, '0', STR_PAD_LEFT) . "_course_{$field}_" . md5(microtime(1)) . strtolower($upInfo['file_ext']);	//Имя конечного файла
			$filePath = COURSES_PATH . '/' . $fileName; //Полный путь

			if (!isset($upInfo['image_width']) || !isset($upInfo['image_height']) || !$upInfo['image_width'] || !$upInfo['image_height']) {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Размер не определен';
				@unlink($upInfo['full_path']);
				die (json_encode($this->ajaxResponse));
			}

			$n = $this->academy()->courses()->_new();

			// Переносим/переименовываем файл
			if (rename($upInfo['full_path'], $filePath)) {

				if($field == 'photo')
					$n->setPhoto(COURSES_URL . '/' . $fileName);
				elseif($field == 'photo_header')
					$n->setPhotoHeader(COURSES_URL . '/' . $fileName);
				elseif($field == 'photo_footer')
					$n->setPhotoFooter(COURSES_URL . '/' . $fileName);

				if ($this->academy()->courses()->update($course->getId(), $n->toArray())) {
					$this->ajaxResponse['descr'] = 'Файл успешно добавлен';
					$this->ajaxResponse['src'] = COURSES_URL . '/' . $fileName;
					$this->ajaxResponse['del'] = true;
					// Unlink old photo
					if($field == 'photo') {
						$photo = $course->getPhoto();
					}
					if($field == 'photo_header') {
						$photo = $course->getPhotoHeader();
					}
					if($field == 'photo_header') {
						$photo = $course->getPhotoFooter();
					}
					if ($photo && is_file(VF_PUBLIC_DIR . $photo)) {
						@unlink(VF_PUBLIC_DIR . $photo);
					}
				} else {
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Ошибка сохранения';
				}

				die (json_encode($this->ajaxResponse));
			} else {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Ошибка переноса файла';
				die (json_encode($this->ajaxResponse));
			}
		} elseif ($upl->display_errors()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $upl->display_errors('', '');//'Ошибка загрузки файла';
			die (json_encode($this->ajaxResponse));
		}

		die (json_encode($this->ajaxResponse));
	}

	public function deleteCoursePhoto()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$field = $this->input()->post('field', true);
		$course = $this->academy()->courses()->get($this->input()->post('id', true));
		if (!$course) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->academy()->courses()->_new();
		if($field == 'photo')
			$n->setPhoto(false);
		elseif($field == 'photo_header')
			$n->setPhotoHeader(false);
		elseif($field == 'photo_footer')
			$n->setPhotoFooter(false);

		if ($this->academy()->courses()->update($course->getId(), $n->toArray())) {
			$this->ajaxResponse['descr'] = 'Файл удален';
			$this->ajaxResponse['del'] = false;
			$this->ajaxResponse['src'] = COURSES_DEFAULT_PHOTO;
			// Unlink old photo
			if($field == 'photo')
				$photo = $course->getPhoto();
			if($field == 'photo_header')
				$photo = $course->getPhotoHeader();
			if($field == 'photo_header')
				$photo = $course->getPhotoFooter();
			if ($photo && is_file(VF_PUBLIC_DIR . $photo))
				@unlink(VF_PUBLIC_DIR . $photo);
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка сохранения';
		}

		die (json_encode($this->ajaxResponse));
	}

// LESSONS START
	public function lessons($courseId = false)
	{
		$course = $this->academy()->courses()->get($courseId);
		if (!$course) Func::redirect(ADMIN . "/{$this->module}");

		$this->vars['courseId'] = $course->getId();
		$this->vars['lessons'] = $this->academy()->courses()->getLessons(new Filter(['course_id' => $course->getId(), 'order_by' => 'number']));

		$this->layout()->page()->setHeader("Уроки курса {$course->getName()}");
		$this->tpl()->template($this->module . '/lessons/index');
	}

	public function setSortingLessons()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) {
			die();
		}
		$str = $this->input()->post('str', true);
		$split = explode('&', str_replace('id=', '', str_replace(' ', '', $str)));
		$sort = 0;
		foreach ($split as $id) {
			$sort = $sort + 1;
			$n = $this->academy()->courses()->_newLesson()->setNumber($sort);
			$this->academy()->courses()->updateLesson($id, $n->toArray());
		}
		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function lessonCreate($courseId = false)
	{
		$course = $this->academy()->courses()->get($courseId);
		if (!$course) Func::redirect(ADMIN . "/{$this->module}");

		$this->_lessonCreate($course->getId());

		$this->vars['course'] = $course;

		$this->layout()->page()->setHeader("Новый урок курса {$course->getName()}");
		$this->tpl()->template($this->module . '/lessons/create');
	}

	private function _lessonCreate($courseId)
	{
		if ($this->input()->post()) {
			$errors = [];
			$this->vars['post'] = $post = $this->input()->post('field');

			$post['name'] = trim($post['name']);
			if (!$post['name']) $errors[] = 'Не указано название урока';

			// Если насобирали ошибки, то выход..
			if ($errors)
				return $this->_notify(implode('<br/>', $errors), false);

			// добавляем запись
			$item = $this->academy()->courses()->_newLesson()
				->setCourseId($courseId)
				->setName($post['name']);
			if (!$this->academy()->courses()->createLesson($item))
				return $this->_notify('Ошибка создания', false);

			$this->_notify('Элемент успешно создан');
			Func::redirect(ADMIN . "/{$this->module}/lessonEdit/{$item->getId()}");
		}
	}

	public function toggleLesson()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = strtolower($this->input()->post('id', true));
		$field = $this->input()->post('field', true);
		$value = intval($this->input()->post('value', true));

		if (!in_array($field, ['status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Неверное поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->academy()->courses()->_newLesson();
		switch ($field) {
			case 'status': $n->setStatus($value ? 'active' : 'hidden'); break;
		}

		// Обновление
		if (!$this->academy()->courses()->updateLesson($id, $n->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function lessonEdit($id = false)
	{
		$lesson = $this->academy()->courses()->getLesson($id);
		if (!$lesson) $this->show404();

		$course = $this->academy()->courses()->get($lesson->getCourseId());
		if (!$course) $this->show404();

		// Saving
		$save = $this->_lessonSave($lesson);
		if ($save->getMessage()) $this->_notify($save->getMessage());
		elseif ($save->getError()) $this->_notify($save->getError(), false);
		if ($save->success()) $lesson = $this->academy()->courses()->getLesson($id);

		foreach ($this->academy()->courses()->getFiles(new Filter(['lesson_id'	=> $lesson->getId(), 'order_by' => 'sort'])) as $file)
			$lesson->addFile($file);

		$this->vars['lesson'] = $lesson;
		$this->vars['course'] = $course;

		$this->addJs[] = '/admin/js/dropzone.js';
		$this->addJs[] = '/admin/js/ajaxupload.3.5.js';
		$this->addJs[] = '/admin/js/jquery-ui.sortable.min.js';
		$this->addJs[] = '/admin/js/modules/academy.js' . Func::modifyTime('/admin/js/modules/academy.js');
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.js';
		$this->addJs[] = '/admin/js/ckeditor/adapters/jquery.js';
		$this->addJs[] = '/admin/js/ckeditor/ckeditor.init.js';
		$this->addCss[] = '/admin/plugins/select2/select2.css' . Func::modifyTime('/admin/plugins/select2/select2.css');
		$this->addJs[] = '/admin/plugins/select2/select2.min.js';
		$this->layout()->page()->setHeader('Редактирование урока');
		$this->tpl()->template($this->module . '/lessons/edit');
	}

	/**
	 * @param Lesson
	 * @return Result
	 */
	public function _lessonSave(Lesson $lesson)
	{
		$result = new Result();

		if ($this->input()->post('save')) {
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach (array_keys($fields) as $field)
				if (!in_array($field,['name','video','description','duration','status']))
					unset($fields[$field]);

			$this->vars['post'] = $fields;

			if (!$fields['name'])
				$result->setError($result->getError() . 'Не указано Название курса' . '<br />');

			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			$n = $this->academy()->courses()->_newLesson()
				->setName($fields['name'])
				->setDescription($fields['description'])
				->setVideo($fields['video'])
				->setDuration(floatval($fields['duration']));

			// обновляем запись
			if ($this->academy()->courses()->updateLesson($lesson->getId(), $n->toArray()))
				$result->setStatus(true)->setMessage('Изменения успешно сохранены');
			else
				$result->setError('Ошибка обновления');
		}

		return $result;
	}

	public function lessonDelete()
	{
		if (!$this->input()->isAjax()) die();

		if ($this->academy()->courses()->deleteLesson(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['descr'] = 'Элемент удален';
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}
		die (json_encode($this->ajaxResponse));
	}

	public function uploadLessonPhoto()
	{
		$id = $this->input()->post('id', true);
		$field = $this->input()->post('field', true);
		$lesson = $this->academy()->courses()->getLesson($id);

		if (!$lesson) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		//Определяем параметры загрузки Картинки
		$upl = new Upload([
			'upload_path' => VF_TMP_DIR,
			'allowed_types' => 'jpg|jpeg|png|bmp',
			'max_size' => '10120',
			'overwrite' => true,
		]);
		if ($upl->do_upload('file')) {

			$upInfo = $upl->data();
			$fileName = str_pad($lesson->getId(), 3, '0', STR_PAD_LEFT) . "_lesson_{$field}_" . md5(microtime(1)) . strtolower($upInfo['file_ext']);	//Имя конечного файла
			$filePath = COURSES_PATH . '/' . $fileName; //Полный путь

			if (!isset($upInfo['image_width']) || !isset($upInfo['image_height']) || !$upInfo['image_width'] || !$upInfo['image_height']) {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Размер не определен';
				@unlink($upInfo['full_path']);
				die (json_encode($this->ajaxResponse));
			}

			$n = $this->academy()->courses()->_newLesson();

			// Переносим/переименовываем файл
			if (rename($upInfo['full_path'], $filePath)) {

				if($field == 'photo_paid')
					$n->setPhotoPaid(COURSES_URL . '/' . $fileName);
				elseif($field == 'photo_unpaid')
					$n->setPhotoUnpaid(COURSES_URL . '/' . $fileName);

				if ($this->academy()->courses()->updateLesson($lesson->getId(), $n->toArray())) {
					$this->ajaxResponse['descr'] = 'Файл успешно добавлен';
					$this->ajaxResponse['src'] = COURSES_URL . '/' . $fileName;
					$this->ajaxResponse['del'] = true;
					// Unlink old photo
					if($field == 'photo_paid') {
						$photo = $lesson->getPhotoPaid();
					}
					if($field == 'photo_unpaid') {
						$photo = $lesson->getPhotoUnpaid();
					}
					if ($photo && is_file(VF_PUBLIC_DIR . $photo)) {
						@unlink(VF_PUBLIC_DIR . $photo);
					}
				} else {
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Ошибка сохранения';
				}

				die (json_encode($this->ajaxResponse));
			} else {
				$this->ajaxResponse['status'] = false;
				$this->ajaxResponse['descr'] = 'Ошибка переноса файла';
				die (json_encode($this->ajaxResponse));
			}
		} elseif ($upl->display_errors()) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = $upl->display_errors('', '');//'Ошибка загрузки файла';
			die (json_encode($this->ajaxResponse));
		}

		die (json_encode($this->ajaxResponse));
	}

	public function deleteLessonPhoto()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$field = $this->input()->post('field', true);
		$lesson = $this->academy()->courses()->getLesson($this->input()->post('id', true));
		if (!$lesson) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->academy()->courses()->_newLesson();
		if($field == 'photo_paid')
			$n->setPhotoPaid(false);
		elseif($field == 'photo_unpaid')
			$n->setPhotoUnpaid(false);

		if ($this->academy()->courses()->updateLesson($lesson->getId(), $n->toArray())) {
			$this->ajaxResponse['descr'] = 'Файл удален';
			$this->ajaxResponse['del'] = false;
			$this->ajaxResponse['src'] = COURSES_DEFAULT_PHOTO;
			// Unlink old photo
			if($field == 'photo_paid')
				$photo = $lesson->getPhotoPaid();
			if($field == 'photo_unpaid')
				$photo = $lesson->getPhotoUnpaid();
			if ($photo && is_file(VF_PUBLIC_DIR . $photo))
				@unlink(VF_PUBLIC_DIR . $photo);
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка сохранения';
		}

		die (json_encode($this->ajaxResponse));
	}

// FILES
	public function uploadLessonFile()
	{
		$lesson = $this->academy()->courses()->getLesson($this->input()->post('LessonId', true));
		if (!$lesson) die('Элемент не найден');

		$course = $this->academy()->courses()->get($lesson->getCourseId());
		if (!$course) die('Элемент не найден');

		//Определяем параметры загрузки Картинки
		$upl = new Upload([
			'upload_path'	=> VF_TMP_DIR,
			'allowed_types'	=> 'xls|xlsx|pdf|docx|doc|doc|jpg|jpeg|png|bmp',
			'max_size'		=> '100000',
			'overwrite'		=> true,
		]);
		if ($upl->do_upload('files')) {

			$upInfo = $upl->data();

			$fileName =
				str_pad($course->getId(), 3, '0', STR_PAD_LEFT) . "_" .
				str_pad($lesson->getId(), 3, '0', STR_PAD_LEFT) . "_file_" . md5(microtime(1)) . strtolower($upInfo['file_ext']);	//Имя конечного файла
			$filePath = COURSES_PATH . '/' . $fileName; //Полный путь

			// Переносим/переименовываем файл
			if (@rename($upInfo['full_path'], $filePath)) {
				$file = $this->academy()->courses()->_newFile()
					->setLessonId($lesson->getId())
					->setPath(COURSES_URL . '/' . $fileName)
					->setName($upInfo['client_name']);
				$this->academy()->courses()->createFile($file);
			} else {
				echo json_encode(['status' => false,'message' => 'Upload error']);
			}
		} elseif ($upl->display_errors()) {
			echo json_encode(['status' => false, 'message' => 'Upload error']);
		}

		exit();
	}

	public function getLessonFiles()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$lesson = $this->academy()->courses()->getFile($this->input()->post('ItemId', true));
		if(!$lesson){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Элемент не найден';
			die (json_encode($this->ajaxResponse));
		}

		$resultHTML = '';
		foreach ($this->academy()->courses()->getFiles(new Filter(['lesson_id'	=> $lesson->getId(), 'order_by' => 'sort'])) as $file)
			$resultHTML .= $this->tpl()->get($this->module.'/lessons/_file_item', ['f' => $file]);
		$this->ajaxResponse['resultHTML'] = $resultHTML;

		die (json_encode($this->ajaxResponse));
	}

	public function setLessonFilesSorting()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$str_sort = $this->input()->post('str_sort', true);

		$split = explode('&', str_replace('id=','',str_replace(' ','',$str_sort)));

		$sort = 0;
		foreach($split as $id){
			$sort = $sort + 5;
			$n = $this->academy()->courses()->_newFile()->setSort($sort);
			$this->academy()->courses()->updateFile($id, $n->toArray());
		}

		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function deleteLessonFile()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$file = $this->academy()->courses()->getFile($this->input()->post('fileId', true));
		if(!$file){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Файл не найден';
			die(json_encode($this->ajaxResponse));
		}

		if (!$this->academy()->courses()->deleteFile($file)) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления файла';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Файл успешно удален';

		die (json_encode($this->ajaxResponse));
	}
// -END FILES

// Tariff
	public function tariffs($courseId = false)
	{
		$course = $this->academy()->courses()->get($courseId);
		if (!$course) Func::redirect(ADMIN . "/{$this->module}");

		$this->vars['course'] = $course;
		$this->vars['tariffs'] = $this->academy()->courses()->getTariffs(new Filter(['course_id' => $course->getId(), 'order_by' => 'sort']));

		$this->layout()->page()->setHeader("Тарифы курса: {$course->getName()}");
		$this->tpl()->template($this->module . '/tariffs/index');
	}

	public function setSortingTariffs()
	{
		// Проверяем корректность запроса
		if (!$this->input()->isAjax()) {
			die();
		}
		$str = $this->input()->post('str', true);
		$split = explode('&', str_replace('id=', '', str_replace(' ', '', $str)));
		$sort = 0;
		foreach ($split as $id) {
			$sort = $sort + 5;
			$n = $this->academy()->courses()->_newTariff()->setSort($sort);
			$this->academy()->courses()->updateTariff($id, $n->toArray());
		}
		$this->ajaxResponse['descr'] = 'Сортировка сохранена успешно';
		die (json_encode($this->ajaxResponse));
	}

	public function tariffCreate($courseId = false)
	{
		$course = $this->academy()->courses()->get($courseId);
		if (!$course) Func::redirect(ADMIN . "/{$this->module}");

		$this->_tariffCreate($course->getId());

		$this->vars['course'] = $course;

		$this->layout()->page()->setHeader("Новый тариф курса: {$course->getName()}");
		$this->tpl()->template($this->module . '/tariffs/create');
	}

	private function _tariffCreate($courseId)
	{
		if ($this->input()->post()) {
			$errors = [];
			$this->vars['post'] = $post = $this->input()->post('field');

			$post['name'] = trim($post['name']);
			if (!$post['name']) $errors[] = 'Не указано название тарифа';

			// Если насобирали ошибки, то выход..
			if ($errors)
				return $this->_notify(implode('<br/>', $errors), false);

			// добавляем запись
			$item = $this->academy()->courses()->_newTariff()
				->setCourseId($courseId)
				->setPrice(0)->setOldPrice(0)
				->setName($post['name']);
			if (!$this->academy()->courses()->createTariff($item))
				return $this->_notify('Ошибка создания', false);

			$this->_notify('Элемент успешно создан');
			Func::redirect(ADMIN . "/{$this->module}/tariffEdit/{$item->getId()}");
		}
	}

	public function toggleTariff()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$id = strtolower($this->input()->post('id', true));
		$field = $this->input()->post('field', true);
		$value = intval($this->input()->post('value', true));

		if (!in_array($field, ['status'])) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = "Неверное поле: {$field}";
			die (json_encode($this->ajaxResponse));
		}

		$n = $this->academy()->courses()->_newTariff();
		switch ($field) {
			case 'status': $n->setStatus($value ? 'active' : 'hidden'); break;
		}

		// Обновление
		if (!$this->academy()->courses()->updateTariff($id, $n->toArray())) {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка обновления';
			die (json_encode($this->ajaxResponse));
		}

		$this->ajaxResponse['descr'] = 'Изменения сохранены успешно';

		die (json_encode($this->ajaxResponse));
	}

	public function tariffEdit($id = false)
	{
		$tariff = $this->academy()->courses()->getTariff($id);
		if (!$tariff) $this->show404();

		$course = $this->academy()->courses()->get($tariff->getCourseId());
		if (!$course) $this->show404();

		// Saving
		$save = $this->_tariffSave($tariff);
		if ($save->getMessage()) $this->_notify($save->getMessage());
		elseif ($save->getError()) $this->_notify($save->getError(), false);
		if ($save->success()) $tariff = $this->academy()->courses()->getTariff($id);

		$this->vars['tariff'] = $tariff;
		$this->vars['course'] = $course;

		$this->layout()->page()->setHeader('Редактирование тарифа');
		$this->tpl()->template($this->module . '/tariffs/edit');
	}

	/**
	 * @param Lesson
	 * @return Result
	 */
	public function _tariffSave(Tariff $tariff)
	{
		$result = new Result();

		if ($this->input()->post('save')) {
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach (array_keys($fields) as $field)
				if (!in_array($field,['name','price','old_price','description']))
					unset($fields[$field]);

			$this->vars['post'] = $fields;

			if (!$fields['name'])
				$result->setError($result->getError() . 'Не указано Название тарифа' . '<br />');

			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			$n = $this->academy()->courses()->_newTariff()
				->setName($fields['name'])
				->setPrice($fields['price'])
				->setOldPrice($fields['old_price'])
				->setDescription($fields['description']);

			// обновляем запись
			if ($this->academy()->courses()->updateTariff($tariff->getId(), $n->toArray()))
				$result->setStatus(true)->setMessage('Изменения успешно сохранены');
			else
				$result->setError('Ошибка обновления');
		}

		return $result;
	}

	public function tariffDelete()
	{
		if (!$this->input()->isAjax()) die();

		if ($this->academy()->courses()->deleteTariff(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['descr'] = 'Элемент удален';
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}
		die (json_encode($this->ajaxResponse));
	}
// --END Tariffs

// Reviews
	public function reviews($courseId = false)
	{
		$course = $this->academy()->courses()->get($courseId);
		if (!$course) Func::redirect(ADMIN . "/{$this->module}");

		$this->vars['course'] = $course;
		$this->vars['reviews'] = $this->academy()->courses()->getReviews(new Filter(['course_id' => $course->getId()]));

		$this->layout()->page()->setHeader("Отзывы курса: {$course->getName()}");
		$this->tpl()->template($this->module . '/reviews/index');
	}

	public function reviewCreate($courseId = false)
	{
		$course = $this->academy()->courses()->get($courseId);
		if (!$course) Func::redirect(ADMIN . "/{$this->module}");

		$this->_reviewCreate($course->getId());

		$this->vars['course'] = $course;

		$this->layout()->page()->setHeader("Новый отзыв курса: {$course->getName()}");
		$this->tpl()->template($this->module . '/reviews/create');
	}

	private function _reviewCreate($courseId)
	{
		if ($this->input()->post()) {
			$errors = [];
			$this->vars['post'] = $post = $this->input()->post('field');

			$post['name'] = trim($post['name']);
			if (!$post['name']) $errors[] = 'Не указано имя';

			// Если насобирали ошибки, то выход..
			if ($errors)
				return $this->_notify(implode('<br/>', $errors), false);

			// добавляем запись
			$item = $this->academy()->courses()->_newReview()
				->setCourseId($courseId)
				->setName($post['name']);
			if (!$this->academy()->courses()->createReview($item))
				return $this->_notify('Ошибка создания', false);

			$this->_notify('Элемент успешно создан');
			Func::redirect(ADMIN . "/{$this->module}/reviewEdit/{$item->getId()}");
		}
	}

	public function reviewEdit($id = false)
	{
		$review = $this->academy()->courses()->getReview($id);
		if (!$review) $this->show404();

		$course = $this->academy()->courses()->get($review->getCourseId());
		if (!$course) $this->show404();

		// Saving
		$save = $this->_reviewSave($review);
		if ($save->getMessage()) $this->_notify($save->getMessage());
		elseif ($save->getError()) $this->_notify($save->getError(), false);
		if ($save->success()) $review = $this->academy()->courses()->getReview($id);

		$this->vars['review'] = $review;
		$this->vars['course'] = $course;

		$this->layout()->page()->setHeader('Редактирование отзыва');
		$this->tpl()->template($this->module . '/reviews/edit');
	}

	/**
	 * @param Review
	 * @return Result
	 */
	public function _reviewSave(Review $review)
	{
		$result = new Result();

		if ($this->input()->post('save')) {
			//POST
			$fields = $this->input()->post('field');
			$fields = array_map('trim', $fields);

			//Отфильтруем недопустимые поля
			foreach (array_keys($fields) as $field)
				if (!in_array($field,['name','video','company','content']))
					unset($fields[$field]);

			$this->vars['post'] = $fields;

			if (!$fields['name'])
				$result->setError($result->getError() . 'Не указано Имя' . '<br />');

			// Если насобирали ошибки, то выход..
			if ($result->getError()) return $result;

			$n = $this->academy()->courses()->_newReview()
				->setName($fields['name'])
				->setVideo($fields['video'])
				->setCompany($fields['company'])
				->setContent($fields['content']);

			// обновляем запись
			if ($this->academy()->courses()->updateReview($review->getId(), $n->toArray()))
				$result->setStatus(true)->setMessage('Изменения успешно сохранены');
			else
				$result->setError('Ошибка обновления');
		}

		return $result;
	}

	public function reviewDelete()
	{
		if (!$this->input()->isAjax()) die();

		if ($this->academy()->courses()->deleteReview(intval($this->input()->post('id', true)))) {
			$this->ajaxResponse['descr'] = 'Элемент удален';
		} else {
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка удаления';
		}
		die (json_encode($this->ajaxResponse));
	}
// --END Reviews
}
