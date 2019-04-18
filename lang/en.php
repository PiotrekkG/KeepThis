<?php
$lang = [];
$lang['currentLang'] = 'English';
$lang['currentLangShort'] = 'en';
$lang['globalWebsiteTitle'] = 'KeepThis! - Keep your links for later!';
$lang['informations'] = [
	'backText'=>'Return to previous page',
	'errorTitle'=>'Error',
	'infoTitle'=>'Information',
	'wrongSiteTitle'=>'Ups, 4O4...',
	'wrongSiteText'=>'Page not found. :O<br>You were redirected to main page.'
];
$lang['nav'] = [
    'title'=>'KeepThis!',
    'titleSmall'=>'Keep your links for later!',
    'addLink'=>'Add new',
    'list'=>'My links',
    'bookmarks'=>'Bookmarks',
    'account'=>'Account',
    'logout'=>'Logout'
];
$lang['modules'] = [
	'bookmark' => [
		'addingSuccess'=>'Added!',
		'addingError'=>'Error whilst adding!',
		'unloggedInfo'=>'To add links by this bookmark, you need to be logged in!<br>You can change this in bookmarks manager.',
		'invalidKeyOrInactive'=>'Invalid bookmark key or bookmark is not active anymore!',
		'invalidKey'=>'Invalid bookmark key!',
		'invalidUrl'=>'Invalid url!',
		'missingData'=>'Missing data!'
	],
	'bookmarks' => [
		'generatingSuccess'=>'New bookmark key generated, look at it below!',
		'generatingFail'=>'Bookmark key can\'t be added, please try again.',
		'addBookmarkButton'=>'Add new bookmark',
		'noBookmarks'=>'You don\'t have any bookmarks added.',
		'tableTitle'=>'Your active bookmarks',
		'tableCreated'=>'Created:',
		'tableUsedTimes'=>'Used times:',
		'tableOptions'=>'Options:',
		'bookmarkDefaultTitle'=>'KeepThis! - keep link with one click!'
	],
	'list' => [
		'addedLinks'=>'Your links:',
		'noLinks'=>'You have no links added yet.'
	],
	'add' => [
		'addingSuccess'=>'Added successfully!',
		'addingFail'=>'Error occurred while adding link!',
		'notValidUrl'=>'"%s" is not a valid URL!',
		'TextRate'=>'Rate',
		'TextFavorite'=>'Favorite',
		'TextDescription'=>'Description',
		'TextDescriptionPlaceholder'=>'(optional - if empty page title will be displayed)',
		'TextSubmit'=>'Add link'
	],
	'qrcode' => [
		'errorOccurs'=>'Error occurred while displaying QR code:(',
		'noPermission'=>'You don\'t have permissions to access this link.'
	],
	'main' => [
		'lastAdded'=>'Your 5 newest links:',
		'lastFavorite'=>'Your "TOP 3" favored links:',
		'lastTopRated'=>'Your "TOP 3" top rated links:',
		'noLinks'=>'You have no links added yet.'
	],
	'visit' => [
		'noPermission'=>'Error occurred or you do not have permissions to access this link.'
	],
	'language' => [
		'langChanged'=>'Lang changed successfully!',
		'langNotChanged'=>'Error occurred while changing language:('
	],
	'login' => [
		'loggedIn'=>'Logged in!',
        'alreadyLogged'=>'You are already logged.',
        'wrongPassword'=>'Wrong password.',
        'wrongUsername'=>'There is no user with this name or e-mail.',
        'checkingUsernameFail'=>'There is no user with this name or e-mail.',
        'tooShort'=>'Username or password is too short (username at least 6 chars, password at least 7)!',
        'missingData'=>'Missing data!',
        'loginText'=>'Have an account?',
        'loginHeader'=>'Log-in',
        'notActiveAccount'=>'This account is dezactivated or deleted!',
        'usernameField'=>'Username or e-mail address',
        'passwordField'=>'Password',
        'loginButton'=>'Login'
	],
	'register' => [
        'registerSuccessful'=>'Successfully registered! You can log-in now.',
        'registerFail'=>'Error occurs while trying to register your account...',
        'usernameTaken'=>'This username is already taken! Please choose another one.',
        'checkingUsernameFail'=>'This username is already taken! Please choose another one.',
        'invalidUsername'=>'Invalid username!',
        'tooShort'=>'Username or password is too short (username at least 6 chars, password at least 7)!',
        'missingData'=>'Missing data!',
        'registerText'=>'New to us?',
        'registerHeader'=>'Sign Up',
        'usernameField'=>'Username',
        'passwordField'=>'Password',
        'registerButton'=>'Register'
	]
];
?>