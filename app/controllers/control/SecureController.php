<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

use Helper\Text;
use Entity\Result;

//Настройки безопасности
class SecureController extends AbstractControlController
{
	protected $module = 'secure';

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;
		$this->vars['currentMenu'] = 'secure';
	}

	public function index()
	{
		$result = new Result();

		if($this->input()->post('change_password')){
			//POST
			$password = $this->input()->post('password');
			$result = $this->auth()->changePassword($this->currentUser(), $password['new'], $password['confirm'], $password['current']);
		}

		// Results
		$this->vars['error'] = $result->getError();
		$this->vars['success'] = $result->getMessage();

		//TFA
		switch($this->currentUser()->getTfaType()){
			case 'disable':
				//Покажем список для выбора способа
				$this->vars['tfa_list'] = true;
			break;
			case 'sms':
				$this->vars['tfa_type'] = 'sms';
				$this->vars['tfa_type_description'] = 'по СМС';
			break;
			case 'ga':
				$this->vars['tfa_type'] = 'ga';
				$this->vars['tfa_type_description'] = 'с помощью приложения Google Authenticator';
			break;
		}

		//Чистка "мусора"
		$this->session()->flash('security_tfa_sms_code');
		$this->session()->flash('security_tfa_ga_secret');
		$this->session()->flash('security_tfa_ga_username');

		$this->addJs[] = '/admin/js/modules/security.js'.Func::modifyTime('/admin/js/modules/security.js');

		$this->vars['header'] = 'Безопасность';
		$this->tpl()->template($this->module.'/index');
	}

	public function show_tfa_panel()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$type = $this->input()->post('type', true);
		$action = $this->input()->post('action', true);

		switch($type){
			case 'sms':
				//Проверить наличие телефона
				if($this->currentUser()->getPhone()->isEmpty()){
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Укажите номер телефона в Профиле';
					die (json_encode($this->ajaxResponse));
				}
				$this->ajaxResponse['panel'] = $this->tpl()->get($this->module.'/_panel_tfa_sms', [
					'hash'			=> md5($this->currentUser()->getPhone().'~'.$this->coreConfig['security_key']),
					'phone'			=> $this->currentUser()->getPhone(),
					'action'		=> $action,
					'hashAction'	=> md5($action.'~'.$this->coreConfig['security_key']),
				]);

			break;
			case 'ga':
				switch($action){
					case 'enable':

						//Генерация кода
						$secret = $this->load()->library('googleAuthenticator')->generateSecret();
						$this->session()->setFlash('security_tfa_ga_secret', $secret);

						//Аккаунт
						$username = DOMAIN . ' ('.$this->currentUser()->getEmail().')';
						$this->session()->setFlash('security_tfa_ga_username', $username);

						$this->ajaxResponse['panel'] = $this->tpl()->get($this->module.'/_panel_tfa_ga_enable', [
							'secret'		=> $secret,
							'username'		=> $username,
							'action'		=> $action,
							'hashAction'	=> md5($action.'~'.$this->coreConfig['security_key']),
						]);
					break;
					case 'disable':
						$this->ajaxResponse['panel'] = $this->tpl()->get($this->module.'/_panel_tfa_ga_disable', [
							'action'		=> $action,
							'hashAction'	=> md5($action.'~'.$this->coreConfig['security_key']),
						]);
					break;
				}
			break;
		}


		die (json_encode($this->ajaxResponse));
	}

	public function tfa_send_sms()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$phone = $this->input()->post('phone', true);
		$hash = $this->input()->post('hash', true);

		if($hash != md5($phone.'~'.$this->coreConfig['security_key'])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong request';
			die (json_encode($this->ajaxResponse));
		}

		$code = mt_rand(100000, 999999);
		$smsText = Text::format($this->config['tfa_sms_text'], ['code' => $code]);
		if($this->sms()->send($phone, $smsText)){
			$this->session()->setFlash('security_tfa_sms_code', $code);
			$this->ajaxResponse['descr'] = 'Код успешно отправлен';
		}else{
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Ошибка отправки СМС';
		}

		die (json_encode($this->ajaxResponse));
	}

	public function tfa_confirm_sms()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$code = $this->input()->post('code', true);
		$action = $this->input()->post('action', true);
		$hash = $this->input()->post('hash', true);

		if($hash != md5($action.'~'.$this->coreConfig['security_key'])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong request';
			die (json_encode($this->ajaxResponse));
		}

		if($code != $this->session()->flash('security_tfa_sms_code', true)){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверный код';
			die (json_encode($this->ajaxResponse));
		}

		//сброс FLASH
		$this->session()->flash('security_tfa_sms_code');

		switch($action){
			case 'enable':
				if($this->users()->update($this->currentUser()->getId(), ['TfaType' => 'sms'])){
					$this->ajaxResponse['descr'] = 'Двухфакторная аутентификация по СМС успешно активирована';
				}else{
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Ошибка сохранения';
				}
			break;
			case 'disable':
				if($this->users()->update($this->currentUser()->getId(), ['TfaType' => 'disable'])){
					$this->ajaxResponse['descr'] = 'Двухфакторная аутентификация отключена';
				}else{
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Ошибка сохранения';
				}
			break;
		}

		die (json_encode($this->ajaxResponse));
	}

	public function tfa_confirm_ga()
	{
		//Проверяем корректность запроса
		if (!$this->input()->isAjax()) die();

		$code = $this->input()->post('code', true);
		$action = $this->input()->post('action', true);
		$hash = $this->input()->post('hash', true);

		if($hash != md5($action.'~'.$this->coreConfig['security_key'])){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Wrong request';
			die (json_encode($this->ajaxResponse));
		}

		$rightCode = $this->load()->library('googleAuthenticator')->getCode($action == 'disable' ? $this->currentUser()->getTfaKey() : $this->session()->flash('security_tfa_ga_secret', true));
		if($code != $rightCode){
			$this->ajaxResponse['status'] = false;
			$this->ajaxResponse['descr'] = 'Неверный код';
			die (json_encode($this->ajaxResponse));
		}

		switch($action){
			case 'enable':
				if($this->users()->update($this->currentUser()->getId(), ['TfaType' => 'ga','TfaGaKey' => $this->session()->flash('security_tfa_ga_secret')])){
					$this->ajaxResponse['descr'] = 'Двухфакторная аутентификация с помощью приложения Google Authenticator успешно активирована';
				}else{
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Ошибка сохранения';
				}
			break;
			case 'disable':
				if($this->users()->update($this->currentUser()->getId(), ['TfaType' => 'disable','TfaGaKey' => null])){
					$this->ajaxResponse['descr'] = 'Двухфакторная аутентификация отключена';
				}else{
					$this->ajaxResponse['status'] = false;
					$this->ajaxResponse['descr'] = 'Ошибка сохранения';
				}
			break;
		}

		die (json_encode($this->ajaxResponse));
	}

	public function tfa_ga_show_qr()
	{
		$_tfa_ga_ga_secret = $this->session()->flash('security_tfa_ga_secret', true);
		$_tfa_ga_ga_username = $this->session()->flash('security_tfa_ga_username', true);
		if($_tfa_ga_ga_secret && $_tfa_ga_ga_username){
			$this->load()->library('qr')->get($this->load()->library('googleAuthenticator')->getQrText($_tfa_ga_ga_username, $_tfa_ga_ga_secret));
		}else
			return false;
	}
}