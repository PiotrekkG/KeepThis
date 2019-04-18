<?php
if (isset($_GET['LetMeIn'])) {
	$info = '';
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        if (strlen($username)>=6 && strlen($password)>=7) {
			if(strpos($username,'@') != -1)
				$conditional = "`email` LIKE '$username' or";
			else
				$conditional = '';

            $query = $conn->query("SELECT `userId`,`username`,`password`,`lang`,`deleted` FROM `users` WHERE $conditional `username` LIKE '$username';");
            if ($query) {
				if ($query->num_rows == 1) {
					$query = mysqli_fetch_assoc($query);
					if (password_verify($password, $query['password'])) {
                        if ($query['deleted'] == 1) {
                            $info = $lang['modules']['login']['notActiveAccount'];
                        } else {
                            $_SESSION['user'] = [
                                'logged' => true,
                                'username' => $query['username'],
                                'lang' => $query['lang'],
                                'userId' => $query['userId']
                            ];
                            header("Location: ".createLink('main','loggedIn'));
                        }
					} else {
						$info = $lang['modules']['login']['wrongPassword'];
					}
				} else {
					$info = $lang['modules']['login']['wrongUsername'];
				}
            } else {
				$info = $lang['modules']['login']['checkingUsernameFail'];
            }
        } else {
            $info = $lang['modules']['login']['tooShort'];
        }
    } else {
        $info = $lang['modules']['login']['missingData'];
    }
}

if (isset($_GET['new']) || $module == 'register') {
    if (isset($_GET['LetMeRegister'])) {
        $info = '';
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            if (strlen($username)>=6 && strlen($password)>=7) {
                if ($conn->real_escape_string($username) == $username) {
					$query = $conn->query("SELECT 1 FROM `users` WHERE `username` LIKE '$username';");
					if ($query) {
						if ($query->num_rows == 0) {
                            $password = password_hash($password, PASSWORD_BCRYPT, ["cost" => 9]);
                            $currentLanguage = $lang['currentLangShort'];
							$query = $conn->query("INSERT INTO `users` (`username`,`password`,`lang`) VALUES ('$username','$password','$currentLanguage');");
							if ($query) {
								$info = $lang['modules']['register']['registerSuccessful'];
								$registerSuccess = true;
							} else {
								$info = $lang['modules']['register']['registerFail'];
							}
						} else {
							$info = $lang['modules']['register']['usernameTaken'];
						}
					} else {
						$info = $lang['modules']['register']['checkingUsernameFail'];
					}
                } else {
                    $info = $lang['modules']['register']['invalidUsername'];
                }
            } else {
                $info = $lang['modules']['register']['tooShort'];
            }
        } else {
            $info = $lang['modules']['register']['missingData'];
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="<?=$lang['currentLangShort'];?>">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	
	<title><?=$lang['globalWebsiteTitle'];?></title>
	
	<link rel="stylesheet" type="text/css" href="/semantic/dist/semantic.min.css">
	
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/reset.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/site.css">

	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/container.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/grid.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/header.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/image.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/menu.css">

	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/divider.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/segment.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/list.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/dropdown.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/icon.css">

	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/form.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/input.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/button.css">
    <link rel="stylesheet" type="text/css" href="/semantic/dist/components/message.css">
    
    <link rel="shortcut icon" href="/images/logo.png" type="image/x-icon">
    
	<style type="text/css">
		body {
		background-color: #DADADA;
		}
		body > .grid {
		height: 100%;
		}
		.image {
		margin-top: -100px;
		border-radius: 3px;
		}
		.column {
		max-width: 500px;
		}
		/*
		a.image.flag {
			display: inline-block;

			width: 80px;
			height: 50px;
			margin: 5px 0;

			border-radius: 50%;
			background-position: center center;
			background-size: 100% 100%;
			background-repeat: no-repeat;
		}
		*/
		.image.flag {
			border-radius: 0;
			width: 80px;
			margin: 5px 15px;
		}
	</style>
</head>
<body>
	
	<div class="ui middle aligned center aligned grid">
		<div class="eight wide column">
			<h2 class="ui teal image header">
			<img src="/images/logo.png" class="image">
			<div class="content">
				<span style="color: #0077ff !important;"><?=$lang['modules']['nav']['title'];?></span> <?=(isset($_GET['new']) || $module == 'register')?$lang['modules']['register']['registerHeader']:$lang['modules']['login']['loginHeader'];?>
			</div>
			</h2>
			<?=isset($info)?'<div class="ui '.(isset($registerSuccess) && $registerSuccess == true?'success':'error').' message">'.$info.'</div>':'';?>

			<form method="post" action="<?=(isset($_GET['new']) || $module == 'register')?'/register/?LetMeRegister':'/login/?LetMeIn';?>" class="ui large form">
			<div class="ui stacked segment">
				<div class="field">
				<div class="ui left icon input">
					<i class="user icon"></i>
					<input type="text" name="username" placeholder="<?=(isset($_GET['new']) || $module == 'register')?$lang['modules']['register']['usernameField']:$lang['modules']['login']['usernameField'];?>">
				</div>
				</div>
				<div class="field">
				<div class="ui left icon input">
					<i class="lock icon"></i>
                    <input type="password" name="password" placeholder="<?=(isset($_GET['new']) || $module == 'register')?$lang['modules']['register']['passwordField']:$lang['modules']['login']['passwordField'];?>">
				</div>
				</div>
				<div class="ui fluid large teal submit button"><?=(isset($_GET['new']) || $module == 'register')?$lang['modules']['register']['registerButton']:$lang['modules']['login']['loginButton'];?></div>
			</div>

			<div class="ui error message"></div>

			</form>

			<div class="ui message">
			<?=(isset($_GET['new']) || $module == 'register')?$lang['modules']['login']['loginText'].' <a href="'.createLink('login').'">'.$lang['modules']['login']['loginHeader'].'</a>':$lang['modules']['register']['registerText'].' <a href="'.createLink('register').'">'.$lang['modules']['register']['registerHeader'].'</a>';?>
			</div>
		</div>
		<div class="column" style="width:130px!important;">
			<a href="<?=createLink('language','change',['name'=>'en']);?>"><img src="/images/flags/gb.png" class="image flag" alt="English" title="English"></a>
        <!--
            <a href="<?=createLink('language','change',['name'=>'fr']);?>"><img src="/images/flags/fr.png" class="image flag" alt="Français" title="Français"></a>
        -->
            <a href="<?=createLink('language','change',['name'=>'pl']);?>"><img src="/images/flags/pl.png" class="image flag" alt="Polish" title="Polish"></a>
        <!--
            <a href="<?=createLink('language','change',['name'=>'es']);?>"><img src="/images/flags/es.png" class="image flag" alt="Español" title="Español"></a>
            <a href="<?=createLink('language','change',['name'=>'de']);?>"><img src="/images/flags/de.png" class="image flag" alt="Deutsch" title="Deutsch"></a>
        -->
		</div>

	</div>
	

	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	<script src="/semantic/dist/components/form.js"></script>
	<script src="/semantic/dist/components/transition.js"></script>
	<script>
	$(document)
	.ready(function(){$('.ui.form').form({fields:{email:{identifier:'username',rules:[
	//dodać spolszczenie - i inne języki!!!
				{
					type   : 'empty',
					prompt : 'Please enter your username or e-mail'
				},
				{
					type   : 'length[6]',
					prompt : 'Your username must be at least 6 characters'
				}
				]
			},
			password: {
				identifier  : 'password',
				rules: [
				{
					type   : 'empty',
					prompt : 'Please enter your password'
				},
				{
					type   : 'length[7]',
					prompt : 'Your password must be at least 7 characters'
				}
				]
			}
			}
		})
		;
	});
	</script>

</body>
</html>