<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>simpleUploader</title>
  <style type="text/css">

body{
    background-color: black;
    color: white;
	font-family: verdana;
    font-size: 12px;
    text-align: center;
}

a{ text-decoration: none; color: white; }

  </style>
<script>
function uploadFiles()
{
    document.getElementById('return_from_uploadFiles').innerHTML = 'Wait while server upload your file(s) please.';

    var oData = new FormData(document.forms.namedItem('upload'));
    var oReq = new XMLHttpRequest();

    oReq.open('POST', 'uploadFiles.php', true);

    oReq.onload = function(oEvent)
    {
        if (oReq.status == 200)
        {
            document.getElementById('return_from_uploadFiles').innerHTML = oReq.responseText;
        
        }else 
        {
            document.getElementById('return_from_uploadFiles').innerHTML = 'Error ' + oReq.status + ' occurred uploading your file.';
        }
    };
    
    oReq.send(oData);
}

function addInputFile()
{
    var o_input_elemnts = document.getElementsByTagName('input');

    var i_num_file_input = 0;

    for(var i = 0; i < o_input_elemnts.length; i++)
    {
        if(o_input_elemnts[i].type === 'file') i_num_file_input += 1;
    }

    document.getElementById('upload').insertAdjacentHTML('beforeend', '<input type="file" name="file_'+i_num_file_input+'" /><br/>');
}
</script>
</head>
<body>
<h1>simpleUploader</h1>
<p><b>Total of your files size &lt; <?php echo ini_get('upload_max_filesize') ?> </b>
<br/> ZipArchives will be automatically extracted (not recursively)
<br/>
<br/> <a href="#" onclick="addInputFile();">Add an input file</a>
</p>

<form name="upload" id="upload" enctype="multipart/form-data">
    <input type="file" name="file_0" /><br/>
</form>
    <br/>
    <a href="#" onclick="uploadFiles();" >Upload</a><br/>
    <div id="return_from_uploadFiles"></div>
</body>
</html>