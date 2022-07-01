<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

//Контроллер управления профилем пользователя
class ProfileController extends AbstractControlController
{
	protected $module = 'profile';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
	}

	public function index()
	{
		if($this->input()->post('save'))
		{
			$result = ['status'=>true,'descr'=>''];

			//POST
			$profile = $this->input()->post('profile', true);
			$profile = array_map('trim', $profile);

			//Отфильтруем недопустимые поля
			foreach($profile as $field => $value)
				if(!in_array($field, ['Username','email','phone','last_name','first_name','second_name']))
					unset($profile[$field]);

			$this->vars['post'] = $profile;

			//Введен ли Email?
			if(!$profile['email']){
				$result['status'] = false;
				$result['descr'] .= 'Не указан E-mail<br />';
			}

			//Если Email меняется, то проверим на уникальность
			if($profile['email'] && $profile['email'] != $this->currentUser()->getEmail() && UserModel::getByEmail($profile['email'])){
				$result['status'] = false;
				$result['descr'] .= 'Указанный E-mail уже используется<br />';
			}

			// Введен ли Phone?
			$profile['phone'] = trim($profile['phone']);

			//Введен ли Username?
			if (!$profile['Username']) {
				$result['status'] = false;
				$result['descr'] .= 'Не указан Логин<br />';
			}

			//Если Username меняется, то проверим на уникальность
			if($profile['Username'] && $profile['Username'] != $this->currentUser()->getUsername() && UserModel::getByUsername($profile['Username'])){
				$result['status'] = false;
				$result['descr'] .= 'Указанный Логин уже используется<br />';
			}

			//Введено ли Имя?
			if(!$profile['first_name']){
				$result['status'] = false;
				$result['descr'] .= 'Не указано Имя<br />';
			}

			if($result['status']){

				//Определяем параметры загрузки Картинки
				$upl = new Upload([
					'upload_path'	=> VF_TMP_DIR,
					'allowed_types'	=> 'jpg|jpeg|png',
					'max_size'		=> '4096',
					'overwrite'		=> true,
				]);

				if($upl->do_upload('avatar'))
				{
					$upInfo = $upl->data();
					$filename = md5($this->currentUser()->getId().'~'.microtime(1)).strtolower($upInfo['file_ext']);	//Имя конечного файла
					$filepath = USER_AVATAR_PATH . '/' . $filename; //Полный путь
					//Переносим файл
					if(@rename($upInfo['full_path'], $filepath)){
						$profile['avatar'] = USER_AVATAR_URL . '/' . $filename;

						//обработка картинки
						$img = new Image([
							'source_image'	=> $filepath,
							'height'		=> 250,
							'width'			=> 250,
						]);
						$img->resize();

					}else{
						$result['status'] = false;
						$result['descr'] = 'Ошибка переноса файла';
					}
				}elseif($upl->display_errors()){
					$result['status'] = false;
					$result['descr'] = 'Проверьте загружаемый файл: '.$upl->display_errors();
				}
			}

			//обновляем запись
			if($result['status']){
				if($this->users()->update($this->currentUser()->getId(), $profile)){
					$result['descr'] = 'Профиль успешно сохранен';

					//Удаляем старый аватар, если нужно
					if(isset($profile['avatar']) && $this->currentUser()->getAvatarExist() && is_file(VF_PUBLIC_DIR . $this->currentUser()->getAvatar()))
						@unlink(VF_PUBLIC_DIR . $this->currentUser()->getAvatar());

					$this->auth()->refresh();

				}else{
					$result['status'] = false;
					$result['descr'] .= 'Ошибка обновления';
				}
			}

			$this->session()->setFlash('result', $result);
		}

		//Flash
		$this->vars['result'] = $this->session()->flash('result');

		$this->addJs[] = '/admin/js/modules/profile.js'.Func::modifyTime('/admin/js/modules/profile.js');

		$this->vars['header'] = 'Мой профиль';
		$this->tpl()->template($this->module.'/index');
	}

	//Удаление
	public function deleteAvatar()
	{
		//Проверяем корректность запроса.
		if (!$this->input()->isAjax()) die();

		$id = $this->input()->post('id', true);

		$user = $this->users()->get($id);
		if(!$user){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Пользователь не найден';
			die (json_encode($this->ajaxResponse));
		}

		if($user->getId() != $this->currentUser()->getId()){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Такое тут не прокатит';
			die (json_encode($this->ajaxResponse));
		}

		$this->users()->update($user->getId(), ['avatar'=>null]);

		$this->auth()->refresh();

		if(is_file(VF_PUBLIC_DIR . $user->getAvatar()))
			@unlink(VF_PUBLIC_DIR . $user->getAvatar());

		$this->ajaxResponse['descr'] = 'Аватар успешно удален';

		die (json_encode($this->ajaxResponse));
	}
}