<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Jodit WYSIWYG editor</title>
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0" />
</head>
<body>
	<h1>AWESOME WYSIWYG Editor Jodit</h1>
	<p>Meet a great WYSIWYG editor Jodit. Edit tables, images , built-in web browser and more. <a href="http://xdsoft.net/jodit/doc/">Documentation</a></p>
	<textarea name="jodit" id="jodit" cols="30" rows="10"></textarea>
</body>
<link rel="stylesheet" href="jodit.min.css">
<script src="jodit.min.js"></script>
<script>
	new Jodit('#jodit', {
        uploader: {
            url: 'jodit-connectors/index.php?action=upload'
        },
        filebrowser: {
            ajax: {
                url: 'jodit-connectors/index.php'
            },
        }
    });
</script>
<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,100italic,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
</html>