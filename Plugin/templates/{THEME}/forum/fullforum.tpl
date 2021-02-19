<!DOCTYPE html>
<html><head>
{headers}
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


	<link href="{THEME}/css/engine.css" type="text/css" rel="stylesheet">
	<link href="{THEME}/forum/css/fullforum.css" type="text/css" rel="stylesheet">
</head>
<body>
<div class="wrap"> 
	 <div class="total">
		<div class="header">
			<div class="header-bot">
				<div class="logo">
					<a href="/forum"><img src="https://i.imgur.com/0ubpY2v.png" alt="" height="100"></a>
				</div>
				<div class="f-right">
				</div>
				<div class="clear"></div> 
			</div>
		</div>
       <ul id="nav-bar">
        <li><a href="{site-link}">Portal</a></li>
        <li><a href="/forum">Strona główna</a></li>
         {forum-login}		
       </ul>

    <div class="mian">
		<div class="content-top">
           {info}{content}
		</div>
    </div>
   </div>
</div>
{AJAX}
</body>
</html>