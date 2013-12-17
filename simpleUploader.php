<?php

/**
@param multidim $_FILES array, string dest dir (will be created if not exists), array of file extensions authorized
@return bool true on succes or string error msg on failure
*/

class simpleUploader
{
	const DS = DIRECTORY_SEPARATOR;

	public $a_upload_error_const, $s_error_msgs;

	private $s_regexp_pattern;

	public	function __construct()
	{
		$this->s_regexp_pattern = '#[^\w.]#i';

		$this->a_upload_error_const = array(UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize size configured in the php.ini.',
											UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the size of MAX_FILE_SIZE, which was specified in the HTML form.',
											UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded.',
											UPLOAD_ERR_NO_FILE => 'Select a file.',
											UPLOAD_ERR_NO_TMP_DIR => 'A temporary folder is missing. Introduced in PHP 5.0.3.',
											UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk. Introduced in PHP 5.1.0.',
											UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload. PHP offers no way to determine which extension is involved.');
		
	}

	public function upload(array $p1_a_files, $p2_s_upload_dest_dir, array $p3_a_extensions)
	{
		if(!in_array($p1_a_files['error'], array_keys($this->a_upload_error_const)))
		{

				if(!is_dir($p2_s_upload_dest_dir))
				{

					if(!@mkdir($p2_s_upload_dest_dir, 0777, TRUE)) return 'Impossible to create destination upload dir, check write permisions!';
				}

				$s_ext_uploaded_file = strtolower(strrchr($p1_a_files['name'], '.'));

				if(in_array($s_ext_uploaded_file, $p3_a_extensions))
				{
					if($s_ext_uploaded_file === '.zip')
					{

							$s_clean_zipname = preg_replace($this->s_regexp_pattern,'_', $p1_a_files['name']);

							$s_zip_abs_path = $p2_s_upload_dest_dir.self::DS.$s_clean_zipname;

							if(move_uploaded_file($p1_a_files['tmp_name'], $s_zip_abs_path))
							{
			 					$o_zip = new ZipArchive;

			 					$o_zip->open($s_zip_abs_path);

			 					$i_num_good_files = 0;

			 					for($i = 0; $i < $o_zip->numFiles; $i++):

			 						$a_infos_extracted_files = $o_zip->statIndex($i);

			 						if($a_infos_extracted_files['size'] > 0)
			 						{
			 							if(!preg_match('#__MACOSX|.DS_Store|Thumbs.db#i', $a_infos_extracted_files['name'])) //OS hidden files
			 							{
			 								
				 							if(preg_match('#'.self::DS.'#i', $a_infos_extracted_files['name']))
				 							{
				 								$s_file_without_ds = substr(strrchr($a_infos_extracted_files['name'], self::DS), 1);

				 							}else $s_file_without_ds = $a_infos_extracted_files['name'];

				 							$s_ext_of_extracted_file =  strtolower(strrchr($s_file_without_ds,'.')); // pdf on darwin

				 							if(in_array($s_ext_of_extracted_file, $p3_a_extensions))
				 							{

				 								$s_final_file_name = preg_replace($this->s_regexp_pattern, '_', $s_file_without_ds);

				 								$o_zip->renameName($a_infos_extracted_files['name'], $s_final_file_name);

				 								$o_zip->extractTo($p2_s_upload_dest_dir, $s_final_file_name);
				 							}

			 							}
			 						}

			 					endfor;

			 					$o_zip->close();

			 					if(@unlink($s_zip_abs_path))
			 					{
			 						return TRUE;

			 					}else $this->s_error_msgs = 'Unable to remove uploaded zip! (But all authorized files were uploaded and extracted)';

							}else $this->s_error_msgs = 'Server failed to upload your file! (move_uploaded_file function)';

					}else
					{
						$s_clean_uploaded_filename = preg_replace($this->s_regexp_pattern,'_', $p1_a_files['name']);

						if(move_uploaded_file($p1_a_files['tmp_name'], $p2_s_upload_dest_dir.self::DS.$s_clean_uploaded_filename))
						{
							return TRUE;

						}else $this->s_error_msgs = 'Server failed to upload your file! (move_uploaded_file function)';

					}

				}else $this->s_error_msgs = 'File extension ('.$s_ext_uploaded_file.') is not allowed on the server!';

		}else $this->s_error_msgs = $this->a_upload_error_const[$p1_a_files['error']];

		if($this->s_error_msgs !== NULL && !empty($this->s_error_msgs))
		{
			return $this->s_error_msgs;

		}else return 'Something not expected happened...';

	}
}