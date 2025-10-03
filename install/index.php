<?php
$itemName = 'mayaoflagos';
error_reporting(0);
$action = isset($_GET['action']) ? $_GET['action'] : '';
function appUrl()
{
	$current = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$exp = explode('?action', $current);
	$url = str_replace('index.php', '', $exp[0]);
	$url = substr($url, 0, -8);
	return  $url;
}
function checkSecurePassword($password)
{
	$passwordError = false;
	$capital = "/[ABCDEFGHIJKLMNOPQRSTUVWXYZ]/";
	$lower = "/[abcdefghijklmnopqrstuvwxyz]/";
	$number = "/[1234567890]/";
	$special = '/[`!@$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?~]/';
	$hash = '/[#]/';
	if (!preg_match($capital, $password)) {
		$passwordError = true;
	} elseif (!preg_match($lower, $password)) {
		$passwordError = true;
	} elseif (!preg_match($number, $password)) {
		$passwordError = true;
	} elseif (!preg_match($special, $password)) {
		$passwordError = true;
	} elseif (strlen($password) < 6) {
		$passwordError = true;
	} elseif (preg_match($hash, $password)) {
		$passwordError = true;
	}
	if ($passwordError) throw new Exception("Weak password detected.");
}
if ($action == 'requirements') {
	$passed = [];
	$failed = [];
	$requiredPHP = 8.3;
	$currentPHP = explode('.', PHP_VERSION)[0] . '.' . explode('.', PHP_VERSION)[1];
	if ($requiredPHP ==  $currentPHP) {
		$passed[] = "PHP version $requiredPHP is required";
	} else {
		$failed[] = "PHP version $requiredPHP is required. Your current PHP version is $currentPHP";
	}
	$extensions = ['BCMath', 'Ctype', 'cURL', 'DOM', 'Fileinfo', 'GD', 'JSON', 'Mbstring', 'OpenSSL', 'PCRE', 'PDO', 'pdo_mysql', 'Tokenizer', 'XML','Filter','Hash','Session','zip'];
	foreach ($extensions as $extension) {
		if (extension_loaded($extension)) {
			$passed[] = strtoupper($extension) . ' PHP Extension is required';
		} else {
			$failed[] = strtoupper($extension) . ' PHP Extension is required';
		}
	}
	if (function_exists('curl_version')) {
		$passed[] = 'Curl via PHP is required';
	} else {
		$failed[] = 'Curl via PHP is required';
	}
	if (file_get_contents(__FILE__)) {
		$passed[] = 'file_get_contents() is required';
	} else {
		$failed[] = 'file_get_contents() is required';
	}
	if (ini_get('allow_url_fopen')) {
		$passed[] = 'allow_url_fopen() is required';
	} else {
		$failed[] = 'allow_url_fopen() is required';
	}
	$dirs = ['../core/bootstrap/cache/', '../core/storage/', '../core/storage/app/', '../core/storage/framework/', '../core/storage/logs/'];
	foreach ($dirs as $dir) {
		$perm = substr(sprintf('%o', fileperms($dir)), -4);
		if ($perm >= '0775') {
			$passed[] = str_replace("../", "", $dir) . ' is required 0775 permission';
		} else {
			$failed[] = str_replace("../", "", $dir) . ' is required 0775 permission. Current Permisiion is ' . $perm;
		}
	}
	if (file_exists('database.sql')) {
		$passed[] = 'database.sql should be available';
	} else {
		$failed[] = 'database.sql should be available';
	}
	if (file_exists('../.htaccess')) {
		$passed[] = '".htaccess" should be available in root directory';
	} else {
		$failed[] = '".htaccess" should be available in root directory';
	}
}


if ($_POST['db_type'] == 'create-new-database') {
	$_POST['db_name'] = $_POST['cp_user'] . '_' . $_POST['db_name'];
	$_POST['db_user'] = $_POST['cp_user'] . '_' . $_POST['db_user'];
}

if ($action == 'result') {
	// Initialize response - no license verification required
	$response = array('error' => 'ok', 'message' => '');
	
	if (@$response['error'] == 'ok' && $_POST['db_type'] == 'create-new-database') {
		try {

			$cpanelusername = $_POST['cp_user'];
			$cpanelpassword = $_POST['cp_password'];
			$domain         = $_SERVER['HTTP_HOST'];
			$authHeader[0] = "Authorization: Basic " . base64_encode($cpanelusername . ":" . $cpanelpassword) . "\n\r";

			$dbname   = $_POST['db_name'];
			$username = $_POST['db_user'];
			$password = $_POST['db_pass'];

			//check secure password
			checkSecurePassword($password);


			// Create the database
			$cpError = "cPanel not detected.";
			$createDbQuery = "https://$domain:2083/json-api/cpanel?cpanel_jsonapi_module=Mysql&cpanel_jsonapi_func=adddb&cpanel_jsonapi_apiversion=1&arg-0=$dbname";

			$createDbCurl = curl_init();
			curl_setopt($createDbCurl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($createDbCurl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($createDbCurl, CURLOPT_HEADER, 0);
			curl_setopt($createDbCurl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($createDbCurl, CURLOPT_HTTPHEADER, $authHeader);
			curl_setopt($createDbCurl, CURLOPT_URL, $createDbQuery);
			$createDbResult = curl_exec($createDbCurl);
			$createDbResult = json_decode($createDbResult);
			echo "</pre>";
			$createDbError = @$createDbResult->data->error ?? @$createDbResult->data->reason ?? @$createDbResult->error;
			if ($createDbResult == false) {
				throw new Exception($cpError);
			} elseif ($createDbError) {
				$cpError = $createDbError ?? $cpError;
				$cpError = @$createDbResult->data->reason ? "Error from cPanel: " . $cpError : $cpError;
				throw new Exception($cpError);
			}
			curl_close($createDbCurl);


			// Create the user and assign privileges
			$cpError = "cPanel not detected.";
			$createUserCurl = curl_init();
			curl_setopt($createUserCurl, CURLOPT_URL, "https://$domain:2083/json-api/cpanel");
			curl_setopt($createUserCurl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($createUserCurl, CURLOPT_ENCODING, '');
			curl_setopt($createUserCurl, CURLOPT_MAXREDIRS, 10);
			curl_setopt($createUserCurl, CURLOPT_TIMEOUT, 0);
			curl_setopt($createUserCurl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($createUserCurl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($createUserCurl, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($createUserCurl, CURLOPT_HTTPHEADER, $authHeader);
			curl_setopt(
				$createUserCurl,
				CURLOPT_POSTFIELDS,
				array(
					'cpanel_jsonapi_module'     => 'Mysql',
					'cpanel_jsonapi_func'       => 'adduser',
					'cpanel_jsonapi_apiversion' => '1',
					'arg-0'                     => $username,
					'arg-1'                     => $password
				)
			);
			$createUserResult = curl_exec($createUserCurl);

			$createUserResult = json_decode($createUserResult);
			$createUserError = @$createUserResult->data->error ?? @$createUserResult->data->reason ?? @$createUserResult->error;
			if ($createUserResult == false) {
				throw new Exception($cpError);
			} elseif ($createUserError) {
				$cpError =  $createUserError ?? $cpError;
				$cpError = @$createUserResult->data->reason ? "Error from cPanel: " . $cpError : $cpError;
				throw new Exception($cpError);
			}
			curl_close($createUserCurl);

			// Assign the database to the user
			$cpError = "cPanel not detected.";
			$createDbUserQuery = "https://$domain:2083/json-api/cpanel?cpanel_jsonapi_module=Mysql&cpanel_jsonapi_func=adduserdb&cpanel_jsonapi_apiversion=1&arg-0=$dbname&arg-1=$username&arg-2=ALL";

			$assignCurl = curl_init();
			curl_setopt($assignCurl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($assignCurl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($assignCurl, CURLOPT_HEADER, 0);
			curl_setopt($assignCurl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($assignCurl, CURLOPT_HTTPHEADER, $authHeader);
			curl_setopt($assignCurl, CURLOPT_URL, $createDbUserQuery);
			$assignDbResult = curl_exec($assignCurl);

			$assignDbResult = json_decode($assignDbResult);
			$assignError = @$assignDbResult->data->error ?? @$assignDbResult->data->reason ?? @$assignDbResult->error;
			if ($assignDbResult == false) {
				throw new Exception($cpError);
			} elseif ($assignError) {
				throw new Exception("There is an issue with assigning the user to the database.");
			}
			curl_close($assignCurl);
		} catch (Exception $e) {
			$response['error'] = 'error';
			$response['message'] = $e->getMessage();
		}
	}

	if (@$response['error'] == 'ok') {
		try {
			$db = new PDO("mysql:host=$_POST[db_host];dbname={$_POST['db_name']}", $_POST['db_user'], $_POST['db_pass']);
			$dbinfo = $db->query('SELECT VERSION()')->fetchColumn();

			$engine =  @explode('-', $dbinfo)[1];
			$version =  @explode('.', $dbinfo)[0] . '.' . @explode('.', $dbinfo)[1];

			if (strtolower($engine) == 'mariadb') {
				if (!version_compare($version, '10.6','>=')) {
					$response['error'] = 'error';
					$response['message'] = 'MariaDB 10.6+ Or MySQL 8.0+ Required. <br> Your current version is MariaDB ' . $version;
				}
			} else {
				if (!version_compare($version, '8.0','>=')) {
					$response['error'] = 'error';
					$response['message'] = 'MariaDB 10.6+ Or MySQL 8.0+ Required. <br> Your current version is MySQL ' . $version;
				}
			}
		} catch (Exception $e) {
			$response['error'] = 'error';
			$response['message'] = $_POST['db_type'] == 'create-new-database' ? 'There is a problem with creating the database.' : 'Database Credential is Not Valid';
		}
	}

	if (@$response['error'] == 'ok') {
		try {
			$query = file_get_contents("database.sql");
			$stmt = $db->prepare($query);
			$stmt->execute();
			$stmt->closeCursor();
		} catch (Exception $e) {
			$response['error'] = 'error';
			$response['message'] = 'Problem Occurred When Importing Database!<br>Please Make Sure The Database is Empty.';
		}
	}

	if (@$response['error'] == 'ok') {
		try {
				$db_name = $_POST['db_name'];
			$db_host = $_POST['db_host'];
			$db_user = $_POST['db_user'];
			$db_pass = $_POST['db_pass'];
			$email = $_POST['email'];
			$siteurl = appUrl();
			$app_key = base64_encode(random_bytes(32));
			$envcontent = "APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:$app_key
APP_DEBUG=true
APP_URL=$siteurl

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=$db_host
DB_PORT=3306
DB_DATABASE=$db_name
DB_USERNAME=$db_user
DB_PASSWORD=$db_pass

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME='{APP_NAME}'

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY='{PUSHER_APP_KEY}'
MIX_PUSHER_APP_CLUSTER='{PUSHER_APP_CLUSTER}'";
			$envpath = dirname(__DIR__, 1) . '/core/.env';
			file_put_contents($envpath, $envcontent);
		} catch (Exception $e) {
			$response['error'] = 'error';
			$response['message'] = 'Problem Occurred When Writing Environment File.';
		}
	}

	if (@$response['error'] == 'ok') {
		try {
			$db->query("UPDATE admins SET email='" . $_POST['email'] . "', username='" . $_POST['admin_user'] . "', password='" . password_hash($_POST['admin_pass'], PASSWORD_DEFAULT) . "' WHERE username='admin'");
		} catch (Exception $e) {
			$response['message'] = 'EasyInstaller was unable to set the credentials of admin.';
		}
	}
}
$sectionTitle =  empty($action) ? 'Terms of Use' : $action;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Installation Wizard - MayaOfLagos</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		@keyframes slideIn {
			from { opacity: 0; transform: translateY(20px); }
			to { opacity: 1; transform: translateY(0); }
		}
		.animate-slide-in { animation: slideIn 0.3s ease-out; }
		.step-indicator { transition: all 0.3s ease; }
		.step-indicator.active { transform: scale(1.1); }
		.progress-bar { transition: width 0.5s ease; }
	</style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
	<!-- Header -->
	<header class="bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg sticky top-0 z-50">
		<div class="container mx-auto px-4 py-4">
			<div class="flex items-center justify-between">
				<div class="flex items-center space-x-3">
					<div class="bg-white rounded-lg p-2">
						<i class="fas fa-database text-indigo-600 text-2xl"></i>
					</div>
					<div>
						<h1 class="text-white text-xl font-bold">MayaOfLagos</h1>
						<p class="text-indigo-200 text-sm">Installation Wizard</p>
					</div>
				</div>
				<div class="hidden md:flex items-center space-x-2 bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2">
					<i class="fas fa-shield-alt text-white"></i>
					<span class="text-white text-sm font-medium">Secure Setup</span>
				</div>
			</div>
		</div>
	</header>

	<!-- Main Container -->
	<div class="container mx-auto px-4 py-8 max-w-5xl">
		<!-- Progress Steps (only show if not on result page) -->
		<?php if ($action != 'result') : ?>
		<div class="mb-8 animate-slide-in">
			<div class="bg-white rounded-xl shadow-lg p-6">
				<div class="flex items-center justify-between mb-4">
					<div class="flex-1 relative">
						<div class="flex items-center justify-between relative">
							<!-- Step 1 -->
							<div class="step-indicator <?php echo empty($action) ? 'active' : ''; ?> flex flex-col items-center relative z-10">
								<div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg <?php echo empty($action) ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-300 text-gray-600'; ?> transition-all">
									<i class="fas fa-file-contract"></i>
								</div>
								<span class="text-xs mt-2 font-medium <?php echo empty($action) ? 'text-indigo-600' : 'text-gray-500'; ?>">Terms</span>
							</div>
							
							<!-- Line 1 -->
							<div class="flex-1 h-1 mx-2 <?php echo $action ? 'bg-indigo-600' : 'bg-gray-300'; ?> rounded-full transition-all"></div>
							
							<!-- Step 2 -->
							<div class="step-indicator <?php echo $action == 'requirements' ? 'active' : ''; ?> flex flex-col items-center relative z-10">
								<div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg <?php echo $action == 'requirements' ? 'bg-indigo-600 text-white shadow-lg' : ($action && $action != 'requirements' ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600'); ?> transition-all">
									<i class="fas <?php echo ($action && $action != 'requirements') ? 'fa-check' : 'fa-cogs'; ?>"></i>
								</div>
								<span class="text-xs mt-2 font-medium <?php echo $action == 'requirements' ? 'text-indigo-600' : 'text-gray-500'; ?>">Requirements</span>
							</div>
							
							<!-- Line 2 -->
							<div class="flex-1 h-1 mx-2 <?php echo $action == 'information' ? 'bg-indigo-600' : 'bg-gray-300'; ?> rounded-full transition-all"></div>
							
							<!-- Step 3 -->
							<div class="step-indicator <?php echo $action == 'information' ? 'active' : ''; ?> flex flex-col items-center relative z-10">
								<div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg <?php echo $action == 'information' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-300 text-gray-600'; ?> transition-all">
									<i class="fas fa-database"></i>
								</div>
								<span class="text-xs mt-2 font-medium <?php echo $action == 'information' ? 'text-indigo-600' : 'text-gray-500'; ?>">Configuration</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<!-- Content Card -->
		<div class="bg-white rounded-2xl shadow-2xl overflow-hidden animate-slide-in">
			<div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
				<h2 class="text-2xl md:text-3xl font-bold text-white text-center">
					<?php 
					if ($action == 'result') {
						echo '<i class="fas fa-rocket mr-2"></i>Installation Status';
					} elseif ($action == 'information') {
						echo '<i class="fas fa-database mr-2"></i>Database Configuration';
					} elseif ($action == 'requirements') {
						echo '<i class="fas fa-check-circle mr-2"></i>System Requirements';
					} else {
						echo '<i class="fas fa-file-contract mr-2"></i>Terms & Conditions';
					}
					?>
				</h2>
			</div>
			
			<div class="p-8 md:p-12">
							<?php
							if ($action == 'result') {
								if (@$response['error'] == 'ok') {
									echo '<div class="text-center space-y-6">';
									echo '<div class="flex justify-center"><div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center animate-bounce"><i class="fas fa-check text-green-600 text-5xl"></i></div></div>';
									echo '<h2 class="text-3xl font-bold text-gray-800">Installation Successful!</h2>';
									echo '<p class="text-gray-600 text-lg">Your system has been installed and configured successfully.</p>';
									
									if (@$response['message']) {
										echo '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg"><div class="flex items-center"><i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i><p class="text-yellow-800">' . $response['message'] . '</p></div></div>';
									}
									
									echo '<div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg my-6">';
									echo '<div class="flex items-center mb-2"><i class="fas fa-shield-alt text-red-600 text-xl mr-3"></i><h3 class="text-lg font-bold text-red-800">Important Security Step</h3></div>';
									echo '<p class="text-red-700 font-medium">Please delete the "install" folder from your server immediately for security reasons.</p>';
									echo '</div>';
									
									echo '<a href="' . appUrl() . '" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">';
									echo '<i class="fas fa-home mr-2"></i> Go to Website <i class="fas fa-arrow-right ml-2"></i>';
									echo '</a>';
									echo '</div>';
								} else {
									echo '<div class="text-center space-y-6">';
									echo '<div class="flex justify-center"><div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-times text-red-600 text-5xl"></i></div></div>';
									echo '<h2 class="text-3xl font-bold text-gray-800">Installation Failed</h2>';
									
									if (@$response['message']) {
										echo '<div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg"><p class="text-red-700 text-lg font-medium">' . $response['message'] . '</p></div>';
									} else {
										echo '<p class="text-red-600 text-lg">Your server is not capable of handling the request.</p>';
									}
									
									echo '<p class="text-gray-600">Please try again or contact support if the issue persists.</p>';
									echo '<div class="flex flex-col sm:flex-row gap-4 justify-center">';
									echo '<a href="?action=information" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all"><i class="fas fa-redo mr-2"></i> Try Again</a>';
									echo '<a href="https://viserlab.com/support" target="_blank" class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all"><i class="fas fa-life-ring mr-2"></i> Get Support</a>';
									echo '</div></div>';
								}
							} elseif ($action == 'information') {
							?>
								<form action="?action=result" method="post" class="space-y-8">
									<!-- Website URL -->
									<div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
										<div class="flex items-center mb-4">
											<i class="fas fa-globe text-indigo-600 text-xl mr-3"></i>
											<h3 class="text-lg font-bold text-gray-800">Website URL</h3>
										</div>
										<div>
											<label class="block text-sm font-medium text-gray-700 mb-2">Application URL</label>
											<input name="url" value="<?php echo appUrl(); ?>" type="text" required 
												class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
												placeholder="https://yourdomain.com">
										</div>
									</div>

									<!-- Database Configuration -->
									<div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
										<div class="flex items-center mb-4">
											<i class="fas fa-database text-purple-600 text-xl mr-3"></i>
											<h3 class="text-lg font-bold text-gray-800">Database Configuration</h3>
										</div>
										
										<!-- Database Type Selection -->
										<div class="mb-6 space-y-3">
											<label class="flex items-center p-4 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition-all">
												<input class="form-radio h-5 w-5 text-indigo-600" type="radio" name="db_type" value="existing-database" id="existing-database" checked>
												<div class="ml-3">
													<span class="font-semibold text-gray-800">Existing Database</span>
													<p class="text-sm text-gray-600">Use an already created database</p>
												</div>
											</label>
											<label class="flex items-center p-4 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition-all">
												<input class="form-radio h-5 w-5 text-indigo-600" type="radio" name="db_type" value="create-new-database" id="create-new-database">
												<div class="ml-3">
													<span class="font-semibold text-gray-800">Create New Database</span>
													<p class="text-sm text-gray-600">Automatically create via cPanel (cPanel users only)</p>
												</div>
											</label>
										</div>

										<!-- cPanel Credentials (Hidden by default) -->
										<div class="cpanel-credentials hidden mb-6 space-y-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
											<div class="flex items-center mb-2">
												<i class="fas fa-server text-yellow-600 mr-2"></i>
												<span class="font-semibold text-gray-800">cPanel Credentials</span>
											</div>
											<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
												<div>
													<label class="block text-sm font-medium text-gray-700 mb-2">cPanel Username</label>
													<input type="text" name="cp_user" placeholder="Enter cPanel username"
														class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all">
												</div>
												<div>
													<label class="block text-sm font-medium text-gray-700 mb-2">cPanel Password</label>
													<input type="password" name="cp_password" placeholder="Enter cPanel password"
														class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all">
												</div>
											</div>
										</div>

										<!-- Database Details -->
										<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
											<div>
												<label class="block text-sm font-medium text-gray-700 mb-2">Database Name <span class="text-red-500">*</span></label>
												<input type="text" name="db_name" placeholder="your_database_name" required
													class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
											</div>
											<div>
												<label class="block text-sm font-medium text-gray-700 mb-2">Database Host <span class="text-red-500">*</span></label>
												<input type="text" name="db_host" placeholder="localhost" required value="localhost"
													class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
											</div>
											<div>
												<label class="block text-sm font-medium text-gray-700 mb-2">Database Username <span class="text-red-500">*</span></label>
												<input type="text" name="db_user" placeholder="Database username" required
													class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
											</div>
											<div class="relative">
												<label class="block text-sm font-medium text-gray-700 mb-2">Database Password</label>
												<input class="secure-password w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" 
													type="password" name="db_pass" placeholder="Database password">
												<small class="hidden text-red-600 text-sm mt-1 weak-password-error">
													<i class="fas fa-exclamation-circle mr-1"></i> Weak password detected
												</small>
											</div>
										</div>
									</div>

									<!-- Admin Credentials -->
									<div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
										<div class="flex items-center mb-4">
											<i class="fas fa-user-shield text-green-600 text-xl mr-3"></i>
											<h3 class="text-lg font-bold text-gray-800">Admin Credentials</h3>
										</div>
										<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
											<div>
												<label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
												<input name="admin_user" type="text" placeholder="admin" required
													class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
											</div>
											<div>
												<label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
												<input name="admin_pass" type="password" placeholder="Secure password" required
													class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
											</div>
											<div>
												<label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
												<input name="email" placeholder="admin@example.com" type="email" required
													class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
											</div>
										</div>
									</div>

									<!-- Submit Button -->
									<div class="flex justify-center">
										<button type="submit" 
											class="inline-flex items-center px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
											<i class="fas fa-rocket mr-3"></i>
											Install Now
											<i class="fas fa-arrow-right ml-3"></i>
										</button>
									</div>
								</form>
								<script>
									"use strict";
									document.addEventListener('DOMContentLoaded', () => {
										const radioButtons = document.querySelectorAll('input[name="db_type"]');
										const cpanelCredentials = document.querySelectorAll('.cpanel-credentials');
										const inputFields = document.querySelectorAll('.cpanel-credentials input');
										const passwordInput = document.querySelector('input.secure-password');
										
										// Toggle cPanel credentials visibility
										radioButtons.forEach((radio) => {
											radio.addEventListener('change', (event) => {
												const isExistingDatabase = event.target.value === 'existing-database';
												cpanelCredentials.forEach((element) => {
													element.classList.toggle('hidden', isExistingDatabase);
												});
												inputFields.forEach((input) => {
													input.required = !isExistingDatabase;
												});
												
												if (isExistingDatabase) {
													document.querySelector('.weak-password-error')?.classList.add('hidden');
													document.querySelector('form [type="submit"]')?.removeAttribute('disabled');
												} else {
													securePassword(passwordInput);
												}
											});
										});

										// Password validation
										if (passwordInput) {
											passwordInput.addEventListener('input', function() {
												const dbTypeInput = document.querySelector('input[name="db_type"]:checked');
												if (dbTypeInput.value === 'create-new-database') {
													securePassword(this);
												}
											});
										}

										function securePassword(input) {
											const weakPasswordErrorElement = document.querySelector('.weak-password-error');
											const password = input.value;
											
											if (!password) {
												weakPasswordErrorElement?.classList.add('hidden');
												return false;
											}

											const capital = /[ABCDEFGHIJKLMNOPQRSTUVWXYZ]/;
											const lower = /[abcdefghijklmnopqrstuvwxyz]/;
											const number = /[1234567890]/;
											const special = /[`!@$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
											const hash = /[#]/;
											
											const capitalMatch = capital.test(password);
											const lowerMatch = lower.test(password);
											const numberMatch = number.test(password);
											const specialMatch = special.test(password);
											const hashMatch = hash.test(password);
											const lengthMatch = password.length >= 6;
											
											const submitButton = document.querySelector('form [type="submit"]');
											
											if (!capitalMatch || !lowerMatch || !numberMatch || !specialMatch || !lengthMatch || hashMatch) {
												submitButton?.setAttribute('disabled', 'true');
												submitButton?.classList.add('opacity-50', 'cursor-not-allowed');
												weakPasswordErrorElement?.classList.remove('hidden');
											} else {
												submitButton?.removeAttribute('disabled');
												submitButton?.classList.remove('opacity-50', 'cursor-not-allowed');
												weakPasswordErrorElement?.classList.add('hidden');
											}
										}
									});
								</script>
							<?php
							} elseif ($action == 'requirements') {
								if (count($failed)) {
									echo '<div class="mb-6">';
									echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">';
									echo '<div class="flex items-center mb-2"><i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i><h3 class="text-lg font-bold text-red-800">Requirements Not Met</h3></div>';
									echo '<p class="text-red-700">The following requirements need to be fixed before installation:</p>';
									echo '</div>';
									echo '<div class="space-y-2">';
									foreach ($failed as $fail) {
										echo '<div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">';
										echo '<div class="flex items-center"><i class="fas fa-times-circle text-red-600 mr-3"></i><span class="text-gray-800">' . $fail . '</span></div>';
										echo '<span class="px-3 py-1 bg-red-200 text-red-800 text-xs font-semibold rounded-full">Failed</span>';
										echo '</div>';
									}
									echo '</div></div>';
								}
								
								if (!count($failed)) {
									echo '<div class="text-center mb-8">';
									echo '<div class="flex justify-center mb-4"><div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center animate-bounce"><i class="fas fa-check text-green-600 text-4xl"></i></div></div>';
									echo '<h3 class="text-2xl font-bold text-gray-800 mb-2">All Requirements Met!</h3>';
									echo '<p class="text-gray-600">Your server meets all the requirements for installation.</p>';
									echo '</div>';
								}
								
								if (count($passed)) {
									echo '<details class="mb-6 bg-green-50 rounded-lg border border-green-200">';
									echo '<summary class="cursor-pointer p-4 font-semibold text-green-800 hover:bg-green-100 transition-all rounded-lg">';
									echo '<i class="fas fa-list mr-2"></i>' . (count($failed) ? 'View Passed Checks' : 'View All Checks') . ' (' . count($passed) . ')';
									echo '</summary>';
									echo '<div class="p-4 space-y-2 border-t border-green-200">';
									foreach ($passed as $pass) {
										echo '<div class="flex items-center justify-between p-3 bg-white rounded-lg">';
										echo '<div class="flex items-center"><i class="fas fa-check-circle text-green-600 mr-3"></i><span class="text-gray-700 text-sm">' . $pass . '</span></div>';
										echo '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Passed</span>';
										echo '</div>';
									}
									echo '</div></details>';
								}
								
								echo '<div class="flex justify-center gap-4">';
								if (count($failed)) {
									echo '<a class="inline-flex items-center px-8 py-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all" href="?action=requirements">';
									echo '<i class="fas fa-sync-alt mr-2"></i> Recheck Requirements';
									echo '</a>';
								} else {
									echo '<a class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all" href="?action=information">';
									echo 'Continue to Configuration <i class="fas fa-arrow-right ml-2"></i>';
									echo '</a>';
								}
								echo '</div>';
							} else {
							?>
								<div class="space-y-6">
									<!-- Welcome Message -->
									<div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-xl border border-indigo-200">
										<div class="flex items-start">
											<i class="fas fa-info-circle text-indigo-600 text-2xl mr-4 mt-1"></i>
											<div>
												<h3 class="text-xl font-bold text-gray-800 mb-2">Welcome to TRB Banking Installation</h3>
												<p class="text-gray-700 leading-relaxed">Before proceeding with the installation, please read and accept the terms and conditions. This installation wizard will guide you through the setup process step by step.</p>
											</div>
										</div>
									</div>

									<!-- Terms Content -->
									<div class="bg-white border border-gray-200 rounded-xl p-6 space-y-6">
										<!-- What You Can Do -->
										<div>
											<div class="flex items-center mb-3">
												<i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
												<h4 class="text-lg font-bold text-gray-800">What You Can Do</h4>
											</div>
											<ul class="space-y-2 ml-9">
												<li class="flex items-start">
													<i class="fas fa-check text-green-500 mr-2 mt-1"></i>
													<span class="text-gray-700">Install and use this software on your domain</span>
												</li>
												<li class="flex items-start">
													<i class="fas fa-check text-green-500 mr-2 mt-1"></i>
													<span class="text-gray-700">Customize and modify according to your needs</span>
												</li>
												<li class="flex items-start">
													<i class="fas fa-check text-green-500 mr-2 mt-1"></i>
													<span class="text-gray-700">Translate to your preferred language</span>
												</li>
												<li class="flex items-start">
													<i class="fas fa-check text-green-500 mr-2 mt-1"></i>
													<span class="text-gray-700">Use for commercial purposes on your licensed domain</span>
												</li>
											</ul>
										</div>

										<!-- What You Cannot Do -->
										<div>
											<div class="flex items-center mb-3">
												<i class="fas fa-times-circle text-red-600 text-xl mr-3"></i>
												<h4 class="text-lg font-bold text-gray-800">What You Cannot Do</h4>
											</div>
											<ul class="space-y-2 ml-9">
												<li class="flex items-start">
													<i class="fas fa-times text-red-500 mr-2 mt-1"></i>
													<span class="text-gray-700">Redistribute, resell, or share this software</span>
												</li>
												<li class="flex items-start">
													<i class="fas fa-times text-red-500 mr-2 mt-1"></i>
													<span class="text-gray-700">Use on multiple domains without additional licenses</span>
												</li>
												<li class="flex items-start">
													<i class="fas fa-times text-red-500 mr-2 mt-1"></i>
													<span class="text-gray-700">Remove or modify copyright notices</span>
												</li>
											</ul>
										</div>

										<!-- Important Notice -->
										<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
											<div class="flex items-start">
												<i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-1"></i>
												<div>
													<h5 class="font-bold text-yellow-800 mb-1">Important Notice</h5>
													<p class="text-yellow-700 text-sm">Any modifications to the core code or database structure may affect future updates and support. Please backup your files before making changes.</p>
												</div>
											</div>
										</div>
									</div>

									<!-- Agreement Checkbox -->
									<div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
										<label class="flex items-start cursor-pointer">
											<input type="checkbox" id="agree-terms" class="mt-1 h-5 w-5 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500">
											<span class="ml-3 text-gray-700">
												I have read and agree to the terms and conditions. I understand that this software is licensed for use on a single domain and cannot be redistributed.
											</span>
										</label>
									</div>

									<!-- Action Buttons -->
									<div class="flex justify-center">
										<button id="proceed-btn" disabled class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
											I Agree, Continue
											<i class="fas fa-arrow-right ml-2"></i>
										</button>
									</div>
								</div>

								<script>
									document.addEventListener('DOMContentLoaded', () => {
										const agreeCheckbox = document.getElementById('agree-terms');
										const proceedBtn = document.getElementById('proceed-btn');
										
										agreeCheckbox?.addEventListener('change', (e) => {
											if (e.target.checked) {
												proceedBtn.removeAttribute('disabled');
												proceedBtn.onclick = () => window.location.href = '?action=requirements';
											} else {
												proceedBtn.setAttribute('disabled', 'true');
											}
										});
									});
								</script>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Footer -->
	<footer class="bg-gray-900 border-t border-gray-800 py-6 mt-12">
		<div class="container mx-auto px-4 text-center">
			<p class="text-gray-400">
				&copy; <?php echo Date('Y'); ?> MayaOfLagos. All rights reserved. 
				<span class="text-gray-600">|</span>
				Powered by <a href="https://wa.me/2348123326360" target="_blank" class="text-indigo-400 hover:text-indigo-300 transition-colors">MayaOfLagos</a>
			</p>
		</div>
	</footer>
</body>
</html>