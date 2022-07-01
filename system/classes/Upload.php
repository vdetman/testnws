<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Upload extends Core
{
	public $max_size = 0;
	public $max_width = 0;
	public $max_height = 0;
	public $min_width = 0;
	public $min_height = 0;
	public $max_filename = 0;
	public $max_filename_increment = 100;
	public $allowed_types = '';
	public $file_temp = '';
	public $file_name = '';
	public $orig_name = '';
	public $file_type = '';
	public $file_size = null;
	public $file_ext = '';
	public $file_ext_tolower = false;
	public $upload_path = '';
	public $overwrite = false;
	public $encrypt_name = false;
	public $is_image = false;
	public $image_width = null;
	public $image_height = null;
	public $image_type = '';
	public $image_size_str = '';
	public $error_msg = [];
	public $remove_spaces = true;
	public $detect_mime = true;
	public $xss_clean = false;
	public $mod_mime_fix = true;
	public $temp_prefix = 'temp_file_';
	public $client_name = '';

	protected $_file_name_override = '';
	protected $_mimes = [];

	public function __construct($config = [])
	{
		empty($config) || $this->initialize($config, false);
		$this->_mimes = [
			'hqx'	=> 'application/mac-binhex40',
			'cpt'	=> 'application/mac-compactpro',
			'csv'	=> ['text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'],
			'bin'	=> 'application/macbinary',
			'dms'	=> 'application/octet-stream',
			'lha'	=> 'application/octet-stream',
			'lzh'	=> 'application/octet-stream',
			'exe'	=> ['application/octet-stream', 'application/x-msdownload'],
			'class'	=> 'application/octet-stream',
			'psd'	=> 'application/x-photoshop',
			'so'	=> 'application/octet-stream',
			'sea'	=> 'application/octet-stream',
			'dll'	=> 'application/octet-stream',
			'oda'	=> 'application/oda',
			'pdf'	=> ['application/pdf', 'application/x-download'],
			'ai'	=> 'application/postscript',
			'eps'	=> 'application/postscript',
			'ps'	=> 'application/postscript',
			'smi'	=> 'application/smil',
			'smil'	=> 'application/smil',
			'mif'	=> 'application/vnd.mif',
			'xls'	=> ['application/excel', 'application/msexcel', 'application/vnd.ms-excel', 'application/vnd.ms-office'],
			'ppt'	=> ['application/powerpoint', 'application/vnd.ms-powerpoint'],
			'wbxml'	=> 'application/wbxml',
			'wmlc'	=> 'application/wmlc',
			'dcr'	=> 'application/x-director',
			'dir'	=> 'application/x-director',
			'dxr'	=> 'application/x-director',
			'dvi'	=> 'application/x-dvi',
			'gtar'	=> 'application/x-gtar',
			'gz'	=> 'application/x-gzip',
			'php'	=> 'application/x-httpd-php',
			'php4'	=> 'application/x-httpd-php',
			'php3'	=> 'application/x-httpd-php',
			'phtml'	=> 'application/x-httpd-php',
			'phps'	=> 'application/x-httpd-php-source',
			'js'	=> 'application/x-javascript',
			'swf'	=> 'application/x-shockwave-flash',
			'sit'	=> 'application/x-stuffit',
			'tar'	=> 'application/x-tar',
			'tgz'	=> ['application/x-tar', 'application/x-gzip-compressed'],
			'xhtml'	=> 'application/xhtml+xml',
			'xht'	=> 'application/xhtml+xml',
			'zip'	=> ['application/x-zip', 'application/zip', 'application/x-zip-compressed'],
			'mid'	=> 'audio/midi',
			'midi'	=> 'audio/midi',
			'mpga'	=> 'audio/mpeg',
			'mp2'	=> 'audio/mpeg',
			'mp3'	=> ['audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'],
			'aif'	=> 'audio/x-aiff',
			'aiff'	=> 'audio/x-aiff',
			'aifc'	=> 'audio/x-aiff',
			'ram'	=> 'audio/x-pn-realaudio',
			'rm'	=> 'audio/x-pn-realaudio',
			'rpm'	=> 'audio/x-pn-realaudio-plugin',
			'ra'	=> 'audio/x-realaudio',
			'rv'	=> 'video/vnd.rn-realvideo',
			'wav'	=> ['audio/x-wav', 'audio/wave', 'audio/wav'],
			'bmp'	=> ['image/bmp', 'image/x-ms-bmp', 'image/x-windows-bmp'],
			'gif'	=> 'image/gif',
			'jpeg'	=> ['image/jpeg', 'image/pjpeg'],
			'jpg'	=> ['image/jpeg', 'image/pjpeg'],
			'jpe'	=> ['image/jpeg', 'image/pjpeg'],
			'png'	=> ['image/png', 'image/x-png'],
			'tiff'	=> 'image/tiff',
			'tif'	=> 'image/tiff',
			'css'	=> 'text/css',
			'html'	=> 'text/html',
			'htm'	=> 'text/html',
			'shtml'	=> 'text/html',
			'txt'	=> 'text/plain',
			'text'	=> 'text/plain',
			'log'	=> ['text/plain', 'text/x-log'],
			'rtx'	=> 'text/richtext',
			'rtf'	=> 'text/rtf',
			'xml'	=> ['text/xml', 'application/xml'],
			'xsl'	=> 'text/xml',
			'mpeg'	=> 'video/mpeg',
			'mpg'	=> 'video/mpeg',
			'mpe'	=> 'video/mpeg',
			'qt'	=> 'video/quicktime',
			'mov'	=> 'video/quicktime',
			'avi'	=> 'video/x-msvideo',
			'movie'	=> 'video/x-sgi-movie',
			'doc'	=> 'application/msword',
			'docx'	=> ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
			'xlsx'	=> ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
			'word'	=> ['application/msword', 'application/octet-stream'],
			'xl'	=> 'application/excel',
			'eml'	=> 'message/rfc822',
			'json'	=> ['application/json', 'text/json']
		];
	}

	//Initialize preferences
	public function initialize(array $config = [], $reset = true)
	{
		$reflection = new ReflectionClass($this);
		if ($reset === true){
			$defaults = $reflection->getDefaultProperties();
			foreach (array_keys($defaults) as $key){
				if ($key[0] === '_') continue;

				if (isset($config[$key])){
					if ($reflection->hasMethod('set_'.$key))
						$this->{'set_'.$key}($config[$key]);
					else
						$this->$key = $config[$key];
				}else{
					$this->$key = $defaults[$key];
				}
			}
		}else{
			foreach ($config as $key => &$value){
				if ($key[0] !== '_' && $reflection->hasProperty($key)){
					if ($reflection->hasMethod('set_'.$key))
						$this->{'set_'.$key}($value);
					else
						$this->$key = $value;
				}
			}
		}

		// if a file_name was provided in the config, use it instead of the user input
		// supplied file name for all uploads until initialized again
		$this->_file_name_override = $this->file_name;
		return $this;
	}

	//Perform the file upload
	public function do_upload($field = 'userfile')
	{
		// Is $_FILES[$field] set? If not, no reason to continue.
		if(isset($_FILES[$field]))
			$_file = $_FILES[$field];
		// Does the field name contain array notation?
		elseif (($c = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $field, $matches)) > 1){
			$_file = $_FILES;
			for ($i = 0; $i < $c; $i++){
				// We can't track numeric iterations, only full field names are accepted
				if (($field = trim($matches[0][$i], '[]')) === '' || ! isset($_file[$field])){
					$_file = null;
					break;
				}
				$_file = $_file[$field];
			}
		}

		if(!isset($_file)){
			return false;
		}

		// Is the upload path valid?
		if (!$this->validate_upload_path()) return false;

		// Если передан массив файлов, то берем только первый
		$_file = $this->_get_first_file($_file);

		// Was the file able to be uploaded? If not, determine the reason why.
		if ( ! is_uploaded_file($_file['tmp_name'])){
			$error = isset($_file['error']) ? $_file['error'] : 4;

			switch ($error)
			{
				case UPLOAD_ERR_INI_SIZE:
					$this->set_error('upload_file_exceeds_limit', 'info');
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$this->set_error('upload_file_exceeds_form_limit', 'info');
					break;
				case UPLOAD_ERR_PARTIAL:
					$this->set_error('upload_file_partial', 'debug');
					break;
				case UPLOAD_ERR_NO_FILE:
					//$this->set_error('upload_no_file_selected', 'debug');
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->set_error('upload_no_temp_directory', 'error');
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$this->set_error('upload_unable_to_write_file', 'error');
					break;
				case UPLOAD_ERR_EXTENSION:
					$this->set_error('upload_stopped_by_extension', 'debug');
					break;
				default:
					//$this->set_error('upload_no_file_selected', 'debug');
					break;
			}

			return false;
		}

		// Set the uploaded data as class variables
		$this->file_temp = $_file['tmp_name'];
		$this->file_size = $_file['size'];

		// Skip MIME type detection?
		if ($this->detect_mime !== false)
			$this->_file_mime_type($_file);

		$this->file_type = preg_replace('/^(.+?);.*$/', '\\1', $this->file_type);
		$this->file_type = strtolower(trim(stripslashes($this->file_type), '"'));
		$this->file_name = $this->_prep_filename($_file['name']);
		$this->file_ext 	= $this->get_extension($this->file_name);
		$this->client_name = $this->file_name;

		// Is the file type allowed to be uploaded?
		if ( ! $this->is_allowed_filetype()){
			$this->set_error('upload_invalid_filetype', 'debug');
			return false;
		}

		// if we're overriding, let's now make sure the new name and type is allowed
		if ($this->_file_name_override !== ''){
			$this->file_name = $this->_prep_filename($this->_file_name_override);

			// If no extension was provided in the file_name config item, use the uploaded one
			if (strpos($this->_file_name_override, '.') === false)
				$this->file_name .= $this->file_ext;
			else
				$this->file_ext	= $this->get_extension($this->_file_name_override);

			if ( ! $this->is_allowed_filetype(true)){
				$this->set_error('upload_invalid_filetype', 'debug');
				return false;
			}
		}

		// Convert the file size to kilobytes
		if ($this->file_size > 0)
			$this->file_size = round($this->file_size/1024, 2);

		// Is the file size within the allowed maximum?
		if ( ! $this->is_allowed_filesize()){
			$this->set_error('upload_invalid_filesize', 'info');
			return false;
		}

		// Are the image dimensions within the allowed size?
		// Note: This can fail if the server has an open_basedir restriction.
		if ( ! $this->is_allowed_dimensions()){
			$this->set_error('upload_invalid_dimensions', 'info');
			return false;
		}

		// Sanitize the file name for security
		$this->file_name = $this->_sanitize_filename($this->file_name);

		// Truncate the file name if it's too long
		if ($this->max_filename > 0)
			$this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);

		// Remove white spaces in the name
		if ($this->remove_spaces === true)
			$this->file_name = preg_replace('/\s+/', '_', $this->file_name);

		if ($this->file_ext_tolower && ($ext_length = strlen($this->file_ext)))
			$this->file_name = substr($this->file_name, 0, -$ext_length).$this->file_ext;

		/*
		 * Validate the file name
		 * This function appends an number onto the end of
		 * the file if one with the same name already exists.
		 * If it returns false there was a problem.
		 */
		$this->orig_name = $this->file_name;
		if (false === ($this->file_name = $this->set_filename($this->upload_path, $this->file_name)))
			return false;

		/*
		 * Run the file through the XSS hacking filter
		 * This helps prevent malicious code from being
		 * embedded within a file. Scripts can easily
		 * be disguised as images or other file types.
		 */
		if ($this->xss_clean && $this->do_xss_clean() === false){
			$this->set_error('upload_unable_to_write_file', 'error');
			return false;
		}

		/*
		 * Move the file to the final destination
		 * To deal with different server configurations
		 * we'll attempt to use copy() first. If that fails
		 * we'll use move_uploaded_file(). One of the two should
		 * reliably work in most environments
		 */
		if ( ! @copy($this->file_temp, $this->upload_path.$this->file_name)){
			if ( ! @move_uploaded_file($this->file_temp, $this->upload_path.$this->file_name)){
				$this->set_error('upload_destination_error', 'error');
				return false;
			}
		}

		/*
		 * Set the finalized image dimensions
		 * This sets the image width/height (assuming the
		 * file was an image). We use this information
		 * in the "data" function.
		 */
		$this->set_image_properties($this->upload_path.$this->file_name);

		return true;
	}

	private function _get_first_file($_file)
	{
		if (is_array($_file['tmp_name'])) {
			$_file = [
				'name' => $_file['name'][0],
				'type' => $_file['type'][0],
				'tmp_name' => $_file['tmp_name'][0],
				'error' => $_file['error'][0],
				'size' => $_file['size'][0]
			];
		}
		return $_file;
	}

	//Finalized Data Array
	public function data($index = null)
	{
		$data = array(
			'file_name'		=> $this->file_name,
			'file_type'		=> $this->file_type,
			'file_path'		=> $this->upload_path,
			'full_path'		=> $this->upload_path.$this->file_name,
			'raw_name'		=> substr($this->file_name, 0, -strlen($this->file_ext)),
			'orig_name'		=> $this->orig_name,
			'client_name'	=> $this->client_name,
			'file_ext'		=> $this->file_ext,
			'file_size'		=> $this->file_size,
			'is_image'		=> $this->is_image(),
			'image_width'	=> $this->image_width,
			'image_height'	=> $this->image_height,
			'image_type'	=> $this->image_type,
			'image_size_str'=> $this->image_size_str,
		);

		if ( ! empty($index))
			return isset($data[$index]) ? $data[$index] : null;

		return $data;
	}

	//Set Upload Path
	public function set_upload_path($path)
	{
		// Make sure it has a trailing slash
		$this->upload_path = rtrim($path, '/').'/';
		return $this;
	}

	//Set the file name
	public function set_filename($path, $filename)
	{
		if ($this->encrypt_name === true)
			$filename = md5(uniqid(mt_rand())).$this->file_ext;

		if ($this->overwrite === true || ! file_exists($path.$filename))
			return $filename;

		$filename = str_replace($this->file_ext, '', $filename);

		$new_filename = '';
		for ($i = 1; $i < $this->max_filename_increment; $i++){
			if ( ! file_exists($path.$filename.$i.$this->file_ext)){
				$new_filename = $filename.$i.$this->file_ext;
				break;
			}
		}

		if ($new_filename === ''){
			$this->set_error('upload_bad_filename', 'debug');
			return false;
		}else{
			return $new_filename;
		}
	}

	//Set Maximum File Size
	public function set_max_filesize($n)
	{
		$this->max_size = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	//Set Maximum File Size
	protected function set_max_size($n)
	{
		return $this->set_max_filesize($n);
	}

	//Set Maximum File Name Length
	public function set_max_filename($n)
	{
		$this->max_filename = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	//Set Maximum Image Width
	public function set_max_width($n)
	{
		$this->max_width = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	//Set Maximum Image Height
	public function set_max_height($n)
	{
		$this->max_height = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	//Set minimum image width
	public function set_min_width($n)
	{
		$this->min_width = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	//Set minimum image height
	public function set_min_height($n)
	{
		$this->min_height = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	//Set Allowed File Types
	public function set_allowed_types($types)
	{
		$this->allowed_types = (is_array($types) || $types === '*')
			? $types
			: explode('|', $types);
		return $this;
	}

	//Set Image Properties
	public function set_image_properties($path = '')
	{
		if ($this->is_image() && function_exists('getimagesize')){
			if (false !== ($D = @getimagesize($path))){
				$types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');

				$this->image_width	= $D[0];
				$this->image_height	= $D[1];
				$this->image_type	= isset($types[$D[2]]) ? $types[$D[2]] : 'unknown';
				$this->image_size_str	= $D[3]; // string containing height and width
			}
		}

		return $this;
	}

	//Set XSS Clean
	public function set_xss_clean($flag = false)
	{
		$this->xss_clean = ($flag === true);
		return $this;
	}

	//Validate the image
	public function is_image()
	{
		// IE will sometimes return odd mime-types during upload, so here we just standardize all
		// jpegs or pngs to the same file type.

		$png_mimes  = array('image/x-png');
		$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');

		if (in_array($this->file_type, $png_mimes))
			$this->file_type = 'image/png';
		elseif (in_array($this->file_type, $jpeg_mimes))
			$this->file_type = 'image/jpeg';

		$img_mimes = array('image/gif',	'image/jpeg', 'image/png');

		return in_array($this->file_type, $img_mimes, true);
	}

	//Verify that the filetype is allowed
	public function is_allowed_filetype($ignore_mime = false)
	{
		if($this->allowed_types === '*')
			return true;

		if(empty($this->allowed_types) || ! is_array($this->allowed_types)){
			$this->set_error('upload_no_file_types', 'debug');
			return false;
		}

		$ext = strtolower(ltrim($this->file_ext, '.'));

		if(!in_array($ext, $this->allowed_types, true))
			return false;

		// Images get some additional checks
		if(in_array($ext, array('gif', 'jpg', 'jpeg', 'jpe', 'png'), true) && @getimagesize($this->file_temp) === false)
			return false;

		if ($ignore_mime === true)
			return true;

		if (isset($this->_mimes[$ext]))
			return is_array($this->_mimes[$ext]) ? in_array($this->file_type, $this->_mimes[$ext], true) : ($this->_mimes[$ext] === $this->file_type);

		return false;
	}

	//Verify that the file is within the allowed size
	public function is_allowed_filesize()
	{
		return ($this->max_size === 0 || $this->max_size > $this->file_size);
	}

	//Verify that the image is within the allowed width/height
	public function is_allowed_dimensions()
	{
		if (!$this->is_image()) return true;

		if (function_exists('getimagesize')){
			$D = @getimagesize($this->file_temp);
			if ($this->max_width > 0 && $D[0] > $this->max_width)   return false;
			if ($this->max_height > 0 && $D[1] > $this->max_height) return false;
			if ($this->min_width > 0 && $D[0] < $this->min_width)   return false;
			if ($this->min_height > 0 && $D[1] < $this->min_height) return false;
		}

		return true;
	}

	//Validate Upload Path
	public function validate_upload_path()
	{
		if ($this->upload_path === ''){
			$this->set_error('upload_no_filepath', 'error');
			return false;
		}

		if (realpath($this->upload_path) !== false)
			$this->upload_path = str_replace('\\', '/', realpath($this->upload_path));

		if ( ! is_dir($this->upload_path)){
			$this->set_error('upload_no_filepath', 'error');
			return false;
		}

		if(!$this->_is_really_writable($this->upload_path)){
			$this->set_error('upload_not_writable', 'error');
			return false;
		}

		$this->upload_path = preg_replace('/(.+?)\/*$/', '\\1/',  $this->upload_path);
		return true;
	}

	//Extract the file extension
	public function get_extension($filename)
	{
		$x = explode('.', $filename);

		if (count($x) === 1)
			return '';

		$ext = ($this->file_ext_tolower) ? strtolower(end($x)) : end($x);
		return '.'.$ext;
	}

	//Limit the File Name Length
	public function limit_filename_length($filename, $length)
	{
		if (strlen($filename) < $length)
			return $filename;

		$ext = '';
		if (strpos($filename, '.') !== false){
			$parts 	   = explode('.', $filename);
			$ext 	   = '.'.array_pop($parts);
			$filename	= implode('.', $parts);
		}

		return substr($filename, 0, ($length - strlen($ext))).$ext;
	}

	//Runs the file through the XSS clean function
	public function do_xss_clean()
	{
		$file = $this->file_temp;

		if (filesize($file) == 0)
			return false;

		if (memory_get_usage() && ($memory_limit = ini_get('memory_limit')) > 0){
			$memory_limit = str_split($memory_limit, strspn($memory_limit, '1234567890'));
			if ( ! empty($memory_limit[1])){
				switch ($memory_limit[1][0])
				{
					case 'g':
					case 'G':
						$memory_limit[0] *= 1024 * 1024 * 1024;
						break;
					case 'm':
					case 'M':
						$memory_limit[0] *= 1024 * 1024;
						break;
					default:
						break;
				}
			}

			$memory_limit = (int) ceil(filesize($file) + $memory_limit[0]);
			ini_set('memory_limit', $memory_limit); // When an integer is used, the value is measured in bytes. - PHP.net
		}

		if (function_exists('getimagesize') && @getimagesize($file) !== false)
		{
			if (($file = @fopen($file, 'rb')) === false) // "b" to force binary
				return false; // Couldn't open the file, return FALSE

			$opening_bytes = fread($file, 256);
			fclose($file);

			// These are known to throw IE into mime-type detection chaos
			// <a, <body, <head, <html, <img, <plaintext, <pre, <script, <table, <title
			// title is basically just in SVG, but we filter it anyhow

			// if it's an image or no "triggers" detected in the first 256 bytes - we're good
			return ! preg_match('/<(a|body|head|html|img|plaintext|pre|script|table|title)[\s>]/i', $opening_bytes);
		}

		if (($data = @file_get_contents($file)) === false)
			return false;

		return $this->_CI->security->xss_clean($data, true);
	}

	//Set an error message
	public function set_error($msg, $logLevel = 'error')
	{
		$lang = [];
		$lang['upload_userfile_not_set'] = 'Unable to find a post variable called userfile.';
		$lang['upload_file_exceeds_limit'] = 'The uploaded file exceeds the maximum allowed size in your PHP configuration file.';
		$lang['upload_file_exceeds_form_limit'] = 'The uploaded file exceeds the maximum size allowed by the submission form.';
		$lang['upload_file_partial'] = 'The file was only partially uploaded.';
		$lang['upload_no_temp_directory'] = 'The temporary folder is missing.';
		$lang['upload_unable_to_write_file'] = 'The file could not be written to disk.';
		$lang['upload_stopped_by_extension'] = 'The file upload was stopped by extension.';
		$lang['upload_no_file_selected'] = 'You did not select a file to upload.';
		$lang['upload_invalid_filetype'] = 'The filetype you are attempting to upload is not allowed.';
		$lang['upload_invalid_filesize'] = 'The file you are attempting to upload is larger than the permitted size.';
		$lang['upload_invalid_dimensions'] = 'The image you are attempting to upload doesn\'t fit into the allowed dimensions.';
		$lang['upload_destination_error'] = 'A problem was encountered while attempting to move the uploaded file to the final destination.';
		$lang['upload_no_filepath'] = 'The upload path does not appear to be valid.';
		$lang['upload_no_file_types'] = 'You have not specified any allowed file types.';
		$lang['upload_bad_filename'] = 'The file name you submitted already exists on the server.';
		$lang['upload_not_writable'] = 'The upload destination folder does not appear to be writable.';

		is_array($msg) || $msg = array($msg);
		foreach ($msg as $val){
			$msg = isset($lang[$val]) ? $lang[$val] : $val;
			$this->error_msg[] = $msg;
			//trigger_error($msg, E_USER_ERROR);
		}

		return $this;
	}

	//Display the error message
	public function display_errors($open = '<p>', $close = '</p>')
	{
		return (count($this->error_msg) > 0) ? $open.implode($close.$open, $this->error_msg).$close : '';
	}

	//Prep Filename
	protected function _prep_filename($filename)
	{
		if ($this->mod_mime_fix === false || $this->allowed_types === '*' || ($ext_pos = strrpos($filename, '.')) === false)
			return $filename;

		$ext = substr($filename, $ext_pos);
		$filename = substr($filename, 0, $ext_pos);
		return str_replace('.', '_', $filename).$ext;
	}

	//File MIME type
	protected function _file_mime_type($file)
	{
		// We'll need this to validate the MIME info string (e.g. text/plain; charset=us-ascii)
		$regexp = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/';

		/**
		 * Fileinfo extension - most reliable method
		 *
		 * Apparently XAMPP, CentOS, cPanel and who knows what
		 * other PHP distribution channels EXPLICITLY DISABLE
		 * ext/fileinfo, which is otherwise enabled by default
		 * since PHP 5.3 ...
		 */
		if (function_exists('finfo_file')){
			$finfo = @finfo_open(FILEINFO_MIME);
			// It is possible that a FALSE value is returned, if there is no magic MIME database file found on the system
			if (is_resource($finfo)){
				$mime = @finfo_file($finfo, $file['tmp_name']);
				finfo_close($finfo);
				if (is_string($mime) && preg_match($regexp, $mime, $matches)){
					$this->file_type = $matches[1];
					return;
				}
			}
		}

		if (DIRECTORY_SEPARATOR !== '\\'){
			$cmd = 'file --brief --mime '.escapeshellarg($file['tmp_name']).' 2>&1';

			if (function_exists('exec')){
				$mime = @exec($cmd, $mime, $return_status);
				if ($return_status === 0 && is_string($mime) && preg_match($regexp, $mime, $matches)){
					$this->file_type = $matches[1];
					return;
				}
			}

			if (function_exists('shell_exec')){
				$mime = @shell_exec($cmd);
				if (strlen($mime) > 0){
					$mime = explode("\n", trim($mime));
					if (preg_match($regexp, $mime[(count($mime) - 1)], $matches)){
						$this->file_type = $matches[1];
						return;
					}
				}
			}

			if (function_exists('popen')){
				$proc = @popen($cmd, 'r');
				if (is_resource($proc)){
					$mime = @fread($proc, 512);
					@pclose($proc);
					if ($mime !== false){
						$mime = explode("\n", trim($mime));
						if (preg_match($regexp, $mime[(count($mime) - 1)], $matches)){
							$this->file_type = $matches[1];
							return;
						}
					}
				}
			}
		}

		// Fall back to mime_content_type(), if available (still better than $_FILES[$field]['type'])
		if (function_exists('mime_content_type')){
			$this->file_type = @mime_content_type($file['tmp_name']);
			if (strlen($this->file_type) > 0) // It's possible that mime_content_type() returns FALSE or an empty string
				return;
		}

		$this->file_type = $file['type'];
	}

	//Tests for file writability
	protected function _is_really_writable($file)
	{
		// If we're on a UNIX-like server, just is_writable()
		if (DIRECTORY_SEPARATOR === '/')
			return is_writable($file);

		/* For Windows servers and safe_mode "on" installations we'll actually
		 * write a file then read it. Bah...
		 */
		if (is_dir($file)){
			$file = rtrim($file, '/').'/'.md5(mt_rand());
			if (($fp = @fopen($file, 'ab')) === false)
				return false;

			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);
			return true;
		}
		elseif ( ! is_file($file) || ($fp = @fopen($file, 'ab')) === false)
		{
			return false;
		}

		fclose($fp);
		return true;
	}

	//Filename Security
	protected function _sanitize_filename($str, $relative_path = false)
	{
		$bad = array(
			"../",
			"<!--",
			"-->",
			"<",
			">",
			"'",
			'"',
			'&',
			'$',
			'#',
			'{',
			'}',
			'[',
			']',
			'=',
			';',
			'?',
			"%20",
			"%22",
			"%3c",		// <
			"%253c",	// <
			"%3e",		// >
			"%0e",		// >
			"%28",		// (
			"%29",		// )
			"%2528",	// (
			"%26",		// &
			"%24",		// $
			"%3f",		// ?
			"%3b",		// ;
			"%3d"		// =
		);

		if ( ! $relative_path)
		{
			$bad[] = './';
			$bad[] = '/';
		}

		$str = $this->_remove_invisible_characters($str, false);
		return stripslashes(str_replace($bad, '', $str));
	}

	protected function _remove_invisible_characters($str, $url_encoded = true)
	{
		$non_displayables = [];

		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)

		if ($url_encoded){
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}