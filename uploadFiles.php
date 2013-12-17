<?php header('Content-type: text/html; charset="UTF-8";');

$a_extensions_authorized = array('.zip', '.pdf', '.txt', '.xml', '.csv', '.rss', '.doc', '.docx', '.jpg', '.jpeg', '.png', '.gif', '.tiff', '.mp3');

require_once 'simpleUploader.php';

$o_uploader = new simpleUploader;

foreach($_FILES as $s_html_file_name => $a_file):

	$m_return = $o_uploader->upload($a_file, getcwd().DIRECTORY_SEPARATOR.'upload-dir', $a_extensions_authorized);

	if(is_bool($m_return))
	{
		echo '<br/><font color="blue">Upload has succeeded!</font>';

	}else echo '<br/><font color="red">'.$m_return.'</font>';

endforeach;