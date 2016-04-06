<?php
$baseDir = dirname(__FILE__) . '/..';

$langDir = realpath($baseDir . '/application/language');
if ($langDir === false) {
	die('Non-existing language dir');
}
$langs = array();
foreach (scandir($langDir) as $langFile) {
	if (!preg_match('/^([a-z0-9])+_lang.php$/', $langFile, $matches)) {
		continue;
	}
	$langPath = realpath($langDir . '/' . $langFile);
	$langs[$matches[1]] = include($langPath);
}

$directories = array(
	realpath($baseDir . '/application')
);

$tokens = array();

function register($path, $line, $text, $isCode = false) {
	global $tokens;
	$tokens []= compact('path', 'line', 'text', 'isCode');
}


function parse($path) {
	global $langs;

	$contents = file_get_contents($path);
	$allTokens = token_get_all($contents);
	for ($i = 0; $i < count($allTokens); ) {
		//sprawdź pod kątem ->
		if ($allTokens[$i][0] != T_OBJECT_OPERATOR) {
			++ $i;
			continue;
		}
		++ $i;
		while ($allTokens[$i][0] == T_WHITESPACE) {
			++ $i;
		}

		//sprawdź pod kątem "translate"
		$baseToken = $allTokens[$i];
		$lineNumber = $baseToken[2];
		if ($baseToken[0] != T_STRING or $baseToken[1] != 'translate') {
			++ $i;
			continue;
		}
		++ $i;
		while ($allTokens[$i][0] == T_WHITESPACE) {
			++ $i;
		}

		//sprawdź pod kątem (
		if ($allTokens[$i][0] != '(') {
			 ++ $i;
			continue;
		}
		++ $i;

		$openBraces = 0;
		$tokens = array();
		while ($openBraces != -1) {
			$token = $allTokens[$i];
			++ $i;
			if ($token == '(') {
				++ $openBraces;
			} elseif ($token == ')') {
				-- $openBraces;
			}
			if ($openBraces == 0 and $token == ',') {
				break;
			}
			if ($openBraces >= 0) {
				$tokens []= $token;
			}
		}


		if (count($tokens) == 1 and $tokens[0][0] == T_CONSTANT_ENCAPSED_STRING) {
			$text = substr($tokens[0][1], 1, -1);
			register($path, $lineNumber, $text, false);
		} else {
			$code = '';
			foreach ($tokens as $token) {
				if (is_array($token)) {
					$code .= $token[1];
				} else {
					$code .= $token;
				}
			}
			register($path, $lineNumber, $code, true);
		}
	}
}

//parse('/home/maszynista/test.php');die();

foreach ($directories as $directory) {
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
	while ($it->valid()) {
		if (!$it->isDot()) {
			$path = $it->key();
			$ext = substr($path, strrpos($path, '.'));
			if (in_array($ext, array('.php', '.phtml'))) {
				parse($path);
			}
		}
		$it->next();
	}
}

$notPresent = array();
foreach ($langs as $lang => $keywords) {
	foreach (array_keys($keywords) as $text) {
		$present = false;
		foreach ($tokens as $token) {
			if ($token['text'] == $text) {
				$present = true;
				break;
			}
		}
		if (!$present) {
			$notPresent []= $text;
		}
	}
}
$notPresent = array_unique($notPresent);
foreach ($notPresent as $text) {
	echo 'rem:: ';
	echo $text;
	echo PHP_EOL;
}

foreach ($tokens as $token) {
	extract($token);
	$present = true;
	foreach ($langs as $lang => $keywords) {
		if (!isset($keywords[$text])) {
			$present = false;
		}
	}
	if ($token['isCode']) {
		echo 'dyn:: ';
	} elseif ($present) {
		echo '   :: ';
	} elseif (!$present) {
		echo 'add:: ';
	}
	echo $path . ':' . $line . ': ';
	echo $text;
	echo PHP_EOL;
}

?>
