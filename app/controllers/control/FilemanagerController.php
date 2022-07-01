<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

//Файловый менеджер
class FilemanagerController extends AbstractControlController
{
	protected $module = 'filemanager';

	protected $fmgrConfig = [];

	public function __construct()
	{
		parent::__construct();

		$this->vars['module'] = $this->module;

		// Проверка прав доступа в раздел
		$this->_checkAccess($this->modules[$this->module]);

		define('FILEMGR_PUBLIC_PART',	'/admin/js/filemanager/');
		define('FILEMGR_PRIVATE_PART',	'/'.ADMIN.'/'.$this->module.'/');
		define('FILEMGR_FILES_ROOT',	'/upload');

		$this->_initConfig();
	}

	private function _initConfig()
	{
		$fmgrConfig = [
			'FILES_ROOT'			=> FILEMGR_FILES_ROOT,
			'RETURN_URL_PREFIX'		=> "",
			'SESSION_PATH_KEY'		=> "",
			'THUMBS_VIEW_WIDTH'		=> "140",
			'THUMBS_VIEW_HEIGHT'	=> "120",
			'PREVIEW_THUMB_WIDTH'	=> "100",
			'PREVIEW_THUMB_HEIGHT'	=> "100",
			'MAX_IMAGE_WIDTH'		=> "1000",
			'MAX_IMAGE_HEIGHT'		=> "1000",
			'INTEGRATION'			=> 'custom',
			'DIRLIST'				=> FILEMGR_PRIVATE_PART.'dirtree',
			'CREATEDIR'				=> FILEMGR_PRIVATE_PART.'createdir',
			'DELETEDIR'				=> FILEMGR_PRIVATE_PART.'deletedir',
			'MOVEDIR'				=> FILEMGR_PRIVATE_PART.'movedir',
			'COPYDIR'				=> FILEMGR_PRIVATE_PART.'copydir',
			'RENAMEDIR'				=> FILEMGR_PRIVATE_PART.'renamedir',
			'FILESLIST'				=> FILEMGR_PRIVATE_PART.'fileslist',
			'UPLOAD'				=> FILEMGR_PRIVATE_PART.'upload',
			'DOWNLOAD'				=> FILEMGR_PRIVATE_PART.'download',
			'DOWNLOADDIR'			=> FILEMGR_PRIVATE_PART.'downloaddir',
			'DELETEFILE'			=> FILEMGR_PRIVATE_PART.'deletefile',
			'MOVEFILE'				=> FILEMGR_PRIVATE_PART.'movefile',
			'COPYFILE'				=> FILEMGR_PRIVATE_PART.'copyfile',
			'RENAMEFILE'			=> FILEMGR_PRIVATE_PART.'renamefile',
			'GENERATETHUMB'			=> FILEMGR_PRIVATE_PART.'thumb',
			'DEFAULTVIEW'			=> "list",
			'FORBIDDEN_UPLOADS'		=> "zip js jsp jsb mhtml mht xhtml xht php phtml php3 php4 php5 phps shtml jhtml pl sh py cgi exe application gadget hta cpl msc jar vb jse ws wsf wsc wsh ps1 ps2 psc1 psc2 msh msh1 msh2 inf reg scf msp scr dll msi vbs bat com pif cmd vxd cpl htpasswd htaccess",
			'ALLOWED_UPLOADS'		=> "",
			'FILEPERMISSIONS'		=> "0644",
			'DIRPERMISSIONS'		=> "0755",
			'LANG'					=> "ru",
			'LANG_PATH'				=> FILEMGR_PUBLIC_PART,
			'DATEFORMAT'			=> "dd/MM/yyyy HH:mm",
			'OPEN_LAST_DIR'			=> "yes"
		];

		foreach ($fmgrConfig as $k => $v)
			define($k, $v);

		$this->fmgrConfig = $fmgrConfig;
	}

	public function fmgrConfig()
	{
		die( json_encode($this->fmgrConfig));
	}

	public function index()
	{
		$this->vars['header'] = 'Файловый менеджер';
		$this->tpl()->template($this->module.'/index');
	}

	public function frame()
	{
		$this->vars['add_css'] = [
			FILEMGR_PUBLIC_PART.'css/jquery-ui-1.10.4.custom.min.css',
			FILEMGR_PUBLIC_PART.'css/main.min.css',
		];
		$this->vars['add_js'] = [
			FILEMGR_PUBLIC_PART.'js/jquery-1.11.1.min.js',
			FILEMGR_PUBLIC_PART.'js/jquery-ui-1.10.4.custom.min.js',
			FILEMGR_PUBLIC_PART.'js/custom.js',
			FILEMGR_PUBLIC_PART.'js/main.min.js'.Func::modifyTime(FILEMGR_PUBLIC_PART.'js/main.min.js'),
			FILEMGR_PUBLIC_PART.'js/jquery-dateFormat.min.js',
		];
		$this->tpl()->template($this->module.'/frame');
	}

	public function movedir()
	{
		$path = trim(empty($_GET['d'])?'':$_GET['d']);
		$newPath = trim(empty($_GET['n'])?'':$_GET['n']);
		self::verifyPath($path);
		self::verifyPath($newPath);

		if(is_dir(self::rootPath($path))){
			if(mb_strpos($newPath, $path) === 0)
				echo self::gerResultStr($this->lang_str('E_CannotMoveDirToChild'), false);
			elseif(file_exists(self::rootPath($newPath).'/'.basename($path)))
				echo self::gerResultStr($this->lang_str('E_DirAlreadyExists'), false);
			elseif(rename(self::rootPath($path), self::rootPath($newPath).'/'.basename($path)))
				echo self::gerResultStr();
			else
				echo self::gerResultStr($this->lang_str('E_MoveDir').' '.basename($path), false);
		}
		else
			echo self::gerResultStr($this->lang_str('E_MoveDirInvalisPath'), false);

		die();
	}

	public function downloaddir()
	{
		@ini_set('memory_limit', -1);

		$path = trim($_GET['d']);
		self::verifyPath($path);
		$path = self::rootPath($path);

		if (!class_exists('ZipArchive')) {
			echo '<script>alert("Cannot create zip archive - ZipArchive class is missing. Check your PHP version and configuration");</script>';
		} else {
			try {
				$filename = basename($path);
				$zipFile = $filename.'.zip';
				$zipPath = TEMP_PATH.$zipFile;
				self::ZipDir($path, $zipPath);

				header('Content-Disposition: attachment; filename="'.$zipFile.'"');
				header('Content-Type: application/force-download');
				readfile($zipPath);
				function deleteTmp($zipPath){
					@unlink($zipPath);
				}
				register_shutdown_function('deleteTmp', $zipPath);
			} catch(Exception $ex) {
				echo '<script>alert("'.addslashes($this->lang_str('E_CreateArchive')).'");</script>';
			}
		}

		die();
	}

	public function renamedir()
	{
		$path = trim(empty($_POST['d'])? '': $_POST['d']);
		$name = trim(empty($_POST['n'])? '': $_POST['n']);
		self::verifyPath($path);

		if(is_dir(self::rootPath($path))){
			if(self::rootPath($path.'/') == self::rootPath(FILES_ROOT.'/'))
				echo self::gerResultStr($this->lang_str('E_CannotRenameRoot'), false);
			elseif(rename(self::rootPath($path), dirname(self::rootPath($path)).'/'.$name))
				echo self::gerResultStr();
			else
				echo self::gerResultStr($this->lang_str('E_RenameDir').' '.basename($path), false);
		}
		else
			echo self::gerResultStr($this->lang_str('E_RenameDirInvalidPath'), false);

		die();
	}

	public function deletedir()
	{
		$path = trim(empty($_GET['d'])?'':$_GET['d']);
		self::verifyPath($path);

		if(is_dir(self::rootPath($path))){
			if(self::rootPath($path.'/') == self::rootPath(FILES_ROOT.'/'))
				echo self::gerResultStr($this->lang_str('E_CannotDeleteRoot'), false);
			elseif(count(glob(self::rootPath($path)."/*")))
				echo self::gerResultStr($this->lang_str('E_DeleteNonEmpty'), false);
			elseif(rmdir(self::rootPath($path)))
				echo self::gerResultStr();
			else
				echo self::gerResultStr($this->lang_str('E_CannotDeleteDir').' '.basename($path), false);
		}
		else
			echo self::gerResultStr($this->lang_str('E_DeleteDirInvalidPath').' '.$path, false);

		die();
	}

	public function copydir()
	{
		$path = trim(empty($_POST['d'])?'':$_POST['d']);
		$newPath = trim(empty($_POST['n'])?'':$_POST['n']);
		self::verifyPath($path);
		self::verifyPath($newPath);

		if(is_dir(self::rootPath($path))) {
			$this->_copyDir(self::rootPath($path.'/'), self::rootPath($newPath.'/'.basename($path)));
			echo self::gerResultStr();
		}
		else
			echo self::gerResultStr($this->lang_str('E_CopyDirInvalidPath'), false);

		die();
	}

	private function _copyDir($path, $newPath){
		$items = self::listDirectory($path);
		if(!is_dir($newPath)){mkdir ($newPath, octdec(DIRPERMISSIONS));}
		foreach ($items as $item){
			if($item == '.' || $item == '..'){continue;}
			$oldPath = $path.'/'.$item;
			$tmpNewPath = $newPath.'/'.$item;
			if(is_file($oldPath)){
				copy($oldPath, $tmpNewPath);
			}elseif(is_dir($oldPath)){
				$this->_copyDir($oldPath, $tmpNewPath);
			}
		}
	}

	public function createdir()
	{
		$path = trim(empty($_POST['d'])?'':$_POST['d']);
		$name = trim(empty($_POST['n'])?'':$_POST['n']);
		self::verifyPath($path);

		if(is_dir(self::rootPath($path))){
			if(mkdir(self::rootPath($path).'/'.$name, octdec(DIRPERMISSIONS)))
				echo self::gerResultStr();
			else
				echo self::gerResultStr($this->lang_str('E_CreateDirFailed').' '.basename($path), false);
		}
		else
			echo self::gerResultStr($this->lang_str('E_CreateDirInvalidPath'), false);

		die();
	}

	public function movefile()
	{
		$path = trim(empty($_POST['f'])?'':$_POST['f']);
		$newPath = trim(empty($_POST['n'])?'':$_POST['n']);

		if(!$newPath)
			$newPath = FILES_ROOT;

		self::verifyPath($path);
		self::verifyPath($newPath);

		if(!self::verifyFile(basename($newPath))) {
			echo self::gerResultStr($this->lang_str('E_FileExtensionForbidden'), false);
		}
		elseif(is_file(self::rootPath($path))){
			if(file_exists(self::rootPath($newPath)))
				echo self::gerResultStr($this->lang_str('E_MoveFileAlreadyExists').' '.basename($newPath), false);
			elseif(rename(self::rootPath($path), self::rootPath($newPath)))
				echo self::gerResultStr();
			else
				echo self::gerResultStr($this->lang_str('E_MoveFile').' '.basename($path), false);
		}else{
			echo self::gerResultStr($this->lang_str('E_MoveFileInvalisPath'), false);
		}

		die();
	}

	public function copyfile()
	{
		$path = trim(empty($_POST['f'])?'':$_POST['f']);
		$newPath = trim(empty($_POST['n'])?'':$_POST['n']);
		if(!$newPath)
			$newPath = FILES_ROOT;

		self::verifyPath($path);
		self::verifyPath($newPath);

		if(is_file(self::rootPath($path))){
			$newPath = $newPath.'/'.self::getUniqFilename(self::rootPath($newPath), basename($path));
			if(copy(self::rootPath($path), self::rootPath($newPath)))
				echo self::gerResultStr();
			else
				echo self::gerResultStr($this->lang_str('E_CopyFile'), false);
		}
		else
			echo self::gerResultStr($this->lang_str('E_CopyFileInvalisPath'), false);

		die();
	}

	public function deletefile()
	{
		$path = trim($_POST['f']);
		self::verifyPath($path);

		if(is_file(self::rootPath($path))){
			if(unlink(self::rootPath($path)))
				echo self::gerResultStr();
			else
				echo self::gerResultStr($this->lang_str('E_DeletеFile').' '.basename($path), false);
		}
		else
			echo self::gerResultStr($this->lang_str('E_DeleteFileInvalidPath'), false);

		die();
	}

	public function download()
	{
		$path = trim($_GET['f']);
		self::verifyPath($path);

		if(is_file(self::rootPath($path))){
			$file = urldecode(basename($path));
			header('Content-Disposition: attachment; filename="'.$file.'"');
			header('Content-Type: application/force-download');
			readfile(self::rootPath($path));
		}
	}

	public function renamefile()
	{
		$path = trim(empty($_POST['f'])?'':$_POST['f']);
		$name = trim(empty($_POST['n'])?'':$_POST['n']);

		self::verifyPath($path);

		if(is_file(self::rootPath($path))){
			if(!self::verifyFile($name))
				echo self::gerResultStr($this->lang_str('E_FileExtensionForbidden'), false);
			elseif(rename(self::rootPath($path), dirname(self::rootPath($path)).'/'.$name))
				echo self::gerResultStr();
			else
				echo self::gerResultStr($this->lang_str('E_RenameFile').' '.basename($path), false);
		}
		else
			echo self::gerResultStr($this->lang_str('E_RenameFileInvalidPath'), false);

		die();
	}

	public function upload()
	{
		$isAjax = (isset($_POST['method']) && $_POST['method'] == 'ajax');
		$path = trim(empty($_POST['d']) ? FILES_ROOT : $_POST['d']);
		self::verifyPath($path);
		$path = self::rootPath($path);
		$res = '';
		$c = 0;
		if(is_dir($path)){
			if(!empty($_FILES['files']) && is_array($_FILES['files']['tmp_name'])){
				$errors = $errorsExt = [];
				foreach($_FILES['files']['tmp_name'] as $k=>$v){
					$filename = $_FILES['files']['name'][$k];
					$filename = self::getUniqFilename($path, $filename);

					$filePath = $path.'/'.$filename;
					$isUploaded = true;
					if(!self::verifyFile($filename)){
						$errorsExt[] = $filename;
						$isUploaded = false;
					}
					elseif(!move_uploaded_file($v, $filePath)){
						$errors[] = $filename;
						$isUploaded = false;
					}
					if(is_file($filePath)){
						@chmod ($filePath, octdec(FILEPERMISSIONS));
					}
					if($isUploaded && self::IsImage($filename) && (intval(MAX_IMAGE_WIDTH) > 0 || intval(MAX_IMAGE_HEIGHT) > 0)){
						self::Resize($filePath, $filePath, intval(MAX_IMAGE_WIDTH), intval(MAX_IMAGE_HEIGHT));
					}
				}
				if($errors && $errorsExt)
					$res = self::gerResultStr($this->lang_str('E_UploadNotAll').' '.$this->lang_str('E_FileExtensionForbidden'));
				elseif($errorsExt)
					$res = self::gerResultStr($this->lang_str('E_FileExtensionForbidden'));
				elseif($errors)
					$res = self::gerResultStr($this->lang_str('E_UploadNotAll'));
				else
					$res = self::gerResultStr();
			}
			else
				$res = self::gerResultStr($this->lang_str('E_UploadNoFiles'), false);
		}else{
			$res = self::gerResultStr($this->lang_str('E_UploadInvalidPath'), false);
		}

		self::_write_log($res);

		if($isAjax){
			if($errors || $errorsExt)
				$res = self::gerResultStr($this->lang_str('E_UploadNotAll'), false);
			die ($res);

		}else{
			echo '<script>parent.fileUploaded('.$res.');</script>';
		}
	}

	public function thumb()
	{
		header("Pragma: cache");
		header("Cache-Control: max-age=3600");

		$path = urldecode(empty($_GET['f'])?'':$_GET['f']);
		self::verifyPath($path);

		@chmod(self::rootPath(dirname($path)), octdec(DIRPERMISSIONS));
		@chmod(self::rootPath($path), octdec(FILEPERMISSIONS));

		$w = intval(empty($_GET['width'])?'100':$_GET['width']);
		$h = intval(empty($_GET['height'])?'0':$_GET['height']);

		header('Content-type: '.self::GetMIMEType(basename($path)));
		if($w && $h){
			self::CropCenter(self::rootPath($path), null, $w, $h);
		}else{
			self::Resize(self::rootPath($path), null, $w, $h);
		}
	}

	public function fileslist()
	{
		$path = (empty($_POST['d'])? FILES_ROOT : $_POST['d']);
		$type = (empty($_POST['type'])?'':strtolower($_POST['type']));
		if($type != 'image' && $type != 'flash'){
			$type = '';
		}

		self::verifyPath($path);

		$files = self::listDirectory(self::rootPath($path), 0);
		natcasesort($files);

		$items = [];
		foreach ($files as $f){
			$fullPath = $path.'/'.$f;
			if(!is_file(self::rootPath($fullPath)) || ($type == 'image' && !self::IsImage($f)) || ($type == 'flash' && !self::IsFlash($f)))
				continue;
			$size = filesize(self::rootPath($fullPath));
			$time = filemtime(self::rootPath($fullPath));
			$w = 0;
			$h = 0;
			if(self::IsImage($f)){
				$tmp = @getimagesize(self::rootPath($fullPath));
				if($tmp){
					$w = $tmp[0];
					$h = $tmp[1];
				}
			}
			$items[] = '{"p":"'.preg_replace('~"~', '\\"', $fullPath).'","s":"'.$size.'","t":"'.$time.'","w":"'.$w.'","h":"'.$h.'"}';
		}
		die( '['.implode(',', $items).']' );
	}

	public function dirtree()
	{
		$type = strtolower($this->input()->get('type', true));
		if ($type != 'image' && $type != 'flash') $type = '';

		echo "[\n";
		$tmp = self::getFilesNumber(self::rootPath(FILES_ROOT), $type);
		echo '{"p":"'. mb_ereg_replace('"', '\\"', FILES_ROOT).'","f":"'.$tmp['files'].'","d":"'.$tmp['dirs'].'"}';
		$this->GetDirs(FILES_ROOT, $type);
		echo "\n]";

		die();
	}

	public function GetDirs($path, $type){
		$ret = $sort = [];
		$files = self::listDirectory(self::rootPath($path), 0);
		foreach ($files as $f){
			$fullPath = $path.'/'.$f;
			if(!is_dir(self::rootPath($fullPath)) || $f == '.' || $f == '..'){continue;}
			$tmp = self::getFilesNumber(self::rootPath($fullPath), $type);
			$ret[$fullPath] = ['path'=>$fullPath,'files'=>$tmp['files'],'dirs'=>$tmp['dirs']];
			$sort[$fullPath] = $f;
		}
		natcasesort($sort);
		foreach ($sort as $k => $v){
			$tmp = $ret[$k];
			echo ',{"p":"'.mb_ereg_replace('"', '\\"', $tmp['path']).'","f":"'.$tmp['files'].'","d":"'.$tmp['dirs'].'"}';
			$this->GetDirs($tmp['path'], $type);
		}
	}

	static private function getFilesNumber($path, $type){
		$files = 0;
		$dirs = 0;
		$tmp = self::listDirectory($path);
		foreach ($tmp as $ff){
			if($ff == '.' || $ff == '..'){
				continue;
			}
			elseif(is_file($path.'/'.$ff) && ($type == '' || ($type == 'image' && self::IsImage($ff)) || ($type == 'flash' && self::IsFlash($ff))))
			{
				$files++;
			}elseif(is_dir($path.'/'.$ff)){
				$dirs++;
			}
		}
		return ['files'=>$files, 'dirs'=>$dirs];
	}

	/**
	 * Проверяет имя файла на уникальность в данном каталоге, если есть - добавляем суффикс
	*/
	static private function getUniqFilename($dir, $filename){
		$temp = ''; $uniqSuffPatt = "_copy_\d";
		$dir = self::cleanPath($dir.'/');
		$ext = self::getExtension($filename);
		$name = self::getName($filename);
		$name = preg_replace('~[^\w]~i', '_', $name);
		$name = preg_replace('~'.$uniqSuffPatt.'$~i', '', $name);

		$ext = $ext ? '.'.$ext : $ext;
		$name = $name ?: 'file';

		$i = 0;
		do{
			$temp = ($i > 0? $name."_copy_$i": $name).$ext;
			$i++;
		}while(file_exists($dir.$temp));
		return $temp;
	}

	/**
	 * Returns file extension without dot
	*/
	static private function getExtension($filename)
	{
		$ext = '';
		if(mb_strrpos($filename, '.') !== false){
			$ext = mb_substr($filename, mb_strrpos($filename, '.') + 1);
		}
		return strtolower($ext);
	}

	/**
	 * Returns file name without extension
	*/
	static private function getName($filename) {
		$name = '';
		$tmp = mb_strpos($filename, '?');
		if($tmp !== false){$filename = mb_substr ($filename, 0, $tmp);}
		$dotPos = mb_strrpos($filename, '.');
		return $dotPos !== false ? mb_substr($filename, 0, $dotPos) : $filename;
	}



	static private function IsImage($fileName){
		$allowExt = ['jpg','jpeg','jpe','png','gif','ico'];
		return in_array(self::getExtension($fileName), $allowExt);
	}

	static private function IsFlash($fileName){
		$allowExt = array('swf','flv','swc','swt');
		return in_array(self::getExtension($fileName), $allowExt);
	}

	/**
	 * Returns MIME type of $filename
	*/
	static private function GetMIMEType($filename)
	{
		switch(self::getExtension($filename)){
			case 'jpg':		$type = 'image/jpeg';break;
			case 'jpeg':	$type = 'image/jpeg';break;
			case 'gif':		$type = 'image/gif';break;
			case 'png':		$type = 'image/png';break;
			case 'bmp':		$type = 'image/bmp';break;
			case 'tiff':	$type = 'image/tiff';break;
			case 'tif':		$type = 'image/tiff';break;
			case 'pdf':		$type = 'application/pdf';break;
			case 'rtf':		$type = 'application/msword';break;
			case 'doc':		$type = 'application/msword';break;
			case 'xls':		$type = 'application/vnd.ms-excel'; break;
			case 'zip':		$type = 'application/zip'; break;
			case 'swf':		$type = 'application/x-shockwave-flash'; break;
			default: $type = 'application/octet-stream';
		}
		return $type;
	}

	/**
	 * Проверка файла на валидность
	*/
	static private function verifyFile($filename)
	{
		$forbiddenExt = [];
		foreach(preg_split('/[ ,]+/', strtolower(FORBIDDEN_UPLOADS)) as $fe)
			if(trim($fe)) $forbiddenExt[] = trim($fe);

		$allowedExt = [];
		foreach(preg_split('/[ ,]+/', strtolower(ALLOWED_UPLOADS)) as $ae)
			if(trim($ae)) $allowedExt[] = trim($ae);

		array_map('trim', $forbiddenExt, $allowedExt);
		$forbiddenExt = array_unique($forbiddenExt);
		$allowedExt = array_unique($allowedExt);

		$ext = self::getExtension($filename);
		if($forbiddenExt && in_array($ext, $forbiddenExt)){
			self::_write_log("Extension $ext is forbidden");
			return false;
		}
		if($allowedExt && !in_array($ext, $allowedExt)){
			self::_write_log("Extension $ext isn't allowed");
			return false;
		}
		return true;
	}

	private function lang_str($key)
	{
		$lang['fmgr_CreateDir'] = 'Создать';
		$lang['fmgr_RenameDir'] = 'Переименовать';
		$lang['fmgr_DeleteDir'] = 'Удалить';
		$lang['fmgr_AddFile'] = 'Добавить файл';
		$lang['fmgr_Preview'] = 'Просмотр';
		$lang['fmgr_RenameFile'] = 'Переименовать';
		$lang['fmgr_DeleteFile'] = 'Удалить';
		$lang['fmgr_SelectFile'] = 'Выбор';
		$lang['fmgr_OrderBy'] = 'Сортировать';
		$lang['fmgr_Name_asc'] = '&uarr;&nbsp;&nbsp;Имя';
		$lang['fmgr_Size_asc'] = '&uarr;&nbsp;&nbsp;Размер';
		$lang['fmgr_Date_asc'] = '&uarr;&nbsp;&nbsp;Дата';
		$lang['fmgr_Name_desc'] = '&darr;&nbsp;&nbsp;Имя';
		$lang['fmgr_Size_desc'] = '&darr;&nbsp;&nbsp;Размер';
		$lang['fmgr_Date_desc'] = '&darr;&nbsp;&nbsp;Дата';
		$lang['fmgr_Name'] = 'Имя';
		$lang['fmgr_Size'] = 'Объем';
		$lang['fmgr_Date'] = 'Дата';
		$lang['fmgr_Dimensions'] = 'Размеры';
		$lang['fmgr_Cancel'] = 'Отмена';
		$lang['fmgr_LoadingDirectories'] = 'Загрузка директорий...';
		$lang['fmgr_LoadingFiles'] = 'Загрузка файлов...';
		$lang['fmgr_DirIsEmpty'] = 'Директория пуста';
		$lang['fmgr_NoFilesFound'] = 'Файлы не найдены';
		$lang['fmgr_Upload'] = 'Загрузить';
		$lang['fmgr_T_CreateDir'] = 'Создать новую директорию';
		$lang['fmgr_T_RenameDir'] = 'Переименовать директорию';
		$lang['fmgr_T_DeleteDir'] = 'Удалить выбранную директорию';
		$lang['fmgr_T_AddFile'] = 'Загрузить файлы';
		$lang['fmgr_T_Preview'] = 'Просмотр файлов';
		$lang['fmgr_T_RenameFile'] = 'Переименовать файл';
		$lang['fmgr_T_DeleteFile'] = 'Удалить файл';
		$lang['fmgr_T_SelectFile'] = 'Выбрать выделенные файлы';
		$lang['fmgr_T_ListView'] = 'Список';
		$lang['fmgr_T_ThumbsView'] = 'Миниатюры';
		$lang['fmgr_Q_DeleteFolder'] = 'Удалить выбранную директорию?';
		$lang['fmgr_Q_DeleteFile'] = 'Удалить выбранный файл?';
		$lang['fmgr_E_LoadingConf'] = 'Ошибка загрузки конфигурации';
		$lang['fmgr_E_ActionDisabled'] = 'Действие недоступно';
		$lang['fmgr_E_LoadingAjax'] = 'Ошибка загрузки';
		$lang['fmgr_E_MissingDirName'] = 'Потеряно имя директории';
		$lang['fmgr_E_SelectFiles'] = 'Выбор файлов для загрузки';
		$lang['fmgr_E_CannotRenameRoot'] = 'Невозможно переименовать корневую директорию';
		$lang['fmgr_E_NoFileSelected'] = 'Файлы не выбраны';
		$lang['fmgr_E_CreateDirFailed'] = 'Ошибка создания директории';
		$lang['fmgr_E_CreateDirInvalidPath'] = 'Ошибка создания директории - пути не существует';
		$lang['fmgr_E_CannotDeleteDir'] = 'Ошибка удаления директории';
		$lang['fmgr_E_DeleteDirInvalidPath'] = 'Ошибка удаления директории - пути не существует';
		$lang['fmgr_E_DeletеFile'] = 'Ошибка удаления файлов';
		$lang['fmgr_E_DeleteFileInvalidPath'] = 'Ошибка удаления файла - пути не существует';
		$lang['fmgr_E_DeleteNonEmpty'] = 'Удаление невозможно - директория не пуста';
		$lang['fmgr_E_CannotMoveDirToChild'] = 'Не удается переместить директорию в подкаталоге';
		$lang['fmgr_E_DirAlreadyExists'] = 'Директория с этим именем уже существует в файловой системе';
		$lang['fmgr_E_MoveDir'] = 'Ошибка перемещения директории';
		$lang['fmgr_E_MoveDirInvalisPath'] = 'Ошибка перемещения директории - пути не существует';
		$lang['fmgr_E_MoveFile'] = 'Ошибка перемещения файла';
		$lang['fmgr_E_MoveFileInvalisPath'] = 'Ошибка перемещения файла - файла не существует';
		$lang['fmgr_E_MoveFileAlreadyExists'] = 'Файл с этим именем уже существует в файловой системе';
		$lang['fmgr_E_RenameDir'] = 'Ошибка переименования директории';
		$lang['fmgr_E_RenameDirInvalidPath'] = 'Ошибка переименования директории - пути не существует';
		$lang['fmgr_E_RenameFile'] = 'Ошибка переименования файла';
		$lang['fmgr_E_RenameFileInvalidPath'] = 'Ошибка переименования файла - файла не существует';
		$lang['fmgr_E_UploadNotAll'] = 'Существуют ошибки загрузки некоторых файлов. ';
		$lang['fmgr_E_UploadNoFiles'] = 'Нет файлов для загрузки или объем слишком велик.';
		$lang['fmgr_E_UploadInvalidPath'] = 'Невозможно загрузить файл - директории не существует';
		$lang['fmgr_E_FileExtensionForbidden'] = 'Недопустимое расширение файла ';
		$lang['fmgr_DownloadFile'] = 'Скачать';
		$lang['fmgr_T_DownloadFile'] = 'Скачать файл';
		$lang['fmgr_E_CannotDeleteRoot'] = 'Невозможно удалить корневую директорию';
		$lang['fmgr_file'] = 'файл';
		$lang['fmgr_files'] = 'файлов';
		$lang['fmgr_Cut'] = 'Вырезать';
		$lang['fmgr_Copy'] = 'Копировать';
		$lang['fmgr_Paste'] = 'Вставить';
		$lang['fmgr_E_CopyFile'] = 'Ошибка копирования файла';
		$lang['fmgr_E_CopyFileInvalisPath'] = 'Невозможно скопировать файл - пути не существует';
		$lang['fmgr_E_CopyDirInvalidPath'] = 'Невозможно скопировать директорию - пути не существует';
		$lang['fmgr_E_CreateArchive'] = 'Ошибка создания zip архива';
		$lang['fmgr_E_UploadingFile'] = 'ошибка';
		return isset($lang['fmgr_'.$key]) ? $lang['fmgr_'.$key] : $key;
	}

	/**
	 * Проверяем, чтобы путь операции не "вышел" за пределы FILES_ROOT
	*/
	static private function verifyPath($path){
		if(!mb_strpos($path.'/', FILES_ROOT) === 0){
			self::_write_log("Access to $path is denied");
			die(self::gerResultStr("Access to $path is denied", false).' '.$path);
		}
	}

	/**
	 * Создаем абсолютный путь
	*/
	static private function rootPath($path){
		$path = $_SERVER['DOCUMENT_ROOT'].'/'.$path;
		$path = str_replace('\\', '/', $path);
		$path = self::cleanPath($path);
		return $path;
	}

	/**
	 * Очистка пути от мусора, экранов и дублей
	*/
	static private function cleanPath($path)
	{
		return preg_replace('~[\\\/]+~i', '/', $path);
	}

	/**
	 * Возвращает строку и статус сообщения в формате json
	*/
	static private function gerResultStr($str = '', $type = true)
	{
		$type = $type ? 'ok' : 'error';
		return '{"res":"'.$type.'","msg":"'.addslashes($str).'"}';
	}

	static private function ZipAddDir($path, $zip, $zipPath)
	{
		$d = opendir($path);
		$zipPath = str_replace('//', '/', $zipPath);
		if($zipPath && $zipPath != '/'){$zip->addEmptyDir($zipPath);}
		while(($f = readdir($d)) !== false){
			if($f == '.' || $f == '..'){continue;}
			$filePath = $path.'/'.$f;
			if(is_file($filePath)){
				$zip->addFile($filePath, ($zipPath?$zipPath.'/':'').$f);
			}elseif(is_dir($filePath)){
				self::ZipAddDir($filePath, $zip, ($zipPath?$zipPath.'/':'').$f);
			}
		}
		closedir($d);
	}

	static private function ZipDir($path, $zipFile, $zipPath = ''){
		$zip = new ZipArchive();
		$zip->open($zipFile, ZIPARCHIVE::CREATE);
		self::ZipAddDir($path, $zip, $zipPath);
		$zip->close();
	}

	static private function listDirectory($path){
		$ret = @scandir($path);
		if($ret === false){
			$ret = [];
			$d = opendir($path);
			if($d){
				while(($f = readdir($d)) !== false){
					$ret[] = $f;
				}
				closedir($d);
			}
		}
		return $ret;
	}

	static private function _write_log($msg)
	{
		//write_log($msg);
	}


	/** IMAGES PROCESS **/

	public static function GetImage($path)
	{
		$img = null;
		$ext = self::getExtension(basename($path));
		switch($ext){
			case 'png':
				$img = imagecreatefrompng($path);
			break;
			case 'gif':
				$img = imagecreatefromgif($path);
			break;
			default:
				$img = imagecreatefromjpeg($path);
			break;
		}
		return $img;
	}

	public static function OutputImage($img, $type, $destination = '', $quality = 90)
	{
		if(is_string($img)){
			$img = self::GetImage ($img);
		}
		switch(strtolower($type)){
			case 'png':
				imagepng($img, $destination);
			break;
			case 'gif':
				imagegif($img, $destination);
			break;
			default:
				imagejpeg($img, $destination, $quality);
		}
	}

	public static function SetAlpha($img, $path)
	{
		$ext = self::getExtension(basename($path));
		if($ext == "gif" || $ext == "png"){
			imagecolortransparent($img, imagecolorallocatealpha($img, 0, 0, 0, 127));
			imagealphablending($img, false);
			imagesavealpha($img, true);
		}
		return $img;
	}

	public static function Resize($source, $destination, $width = '150',$height = 0, $quality = 90)
	{
		$tmp = getimagesize($source);
		$w = $tmp[0];$h = $tmp[1];$r = $w / $h;
		if($w <= ($width + 1) && (($h <= ($height + 1)) || (!$height && !$width))){
			if($source != $destination){
				self::OutputImage($source, self::getExtension(basename($source)), $destination, $quality);
			}
			return;
		}
		$newWidth = $width;
		$newHeight = floor($newWidth / $r);
		if(($height > 0 && $newHeight > $height) || !$width){
			$newHeight = $height;
			$newWidth = intval($newHeight * $r);
		}
		$thumbImg = imagecreatetruecolor($newWidth, $newHeight);
		$img = self::GetImage($source);
		$thumbImg = self::SetAlpha($thumbImg, $source);
		imagecopyresampled($thumbImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $w, $h);
		self::OutputImage($thumbImg, self::getExtension(basename($source)), $destination, $quality);
	}

	public static function CropCenter($source, $destination, $width, $height, $quality = 90)
	{
		$tmp = getimagesize($source);
		$w = $tmp[0];$h = $tmp[1];
		if(($w <= $width) && (!$height || ($h <= $height))){
			self::OutputImage(self::GetImage($source), self::getExtension(basename($source)), $destination, $quality);
		}
		$ratio = $width / $height;
		$top = $left = 0;
		$cropWidth = floor($h * $ratio);
		$cropHeight = floor($cropWidth / $ratio);
		if($cropWidth > $w){
			$cropWidth = $w;
			$cropHeight = $w / $ratio;
		}
		if($cropHeight > $h){
			$cropHeight = $h;
			$cropWidth = $h * $ratio;
		}
		if($cropWidth < $w){
			$left = floor(($w - $cropWidth) / 2);
		}
		if($cropHeight < $h){
			$top = floor(($h- $cropHeight) / 2);
		}
		self::Crop($source, $destination, $left, $top, $cropWidth, $cropHeight, $width, $height, $quality);
	}

	public static function Crop($source, $destination, $x, $y, $cropWidth, $cropHeight, $width, $height, $quality = 90)
	{
		$thumbImg = imagecreatetruecolor($width, $height);
		$img = self::GetImage($source);
		$thumbImg = self::SetAlpha($thumbImg, $source);
		imagecopyresampled($thumbImg, $img, 0, 0, $x, $y, $width, $height, $cropWidth, $cropHeight);
		self::OutputImage($thumbImg, self::getExtension(basename($source)), $destination, $quality);
	}
}