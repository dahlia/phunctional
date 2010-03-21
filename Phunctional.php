<?php
/*
Title: Phunctional
	이 파일은 모든 Phunctional 패키지들을 포함한다.
*/

/*
	Constant: PHUNCTIONAL_PATH
		Phunctional 프레임워크 폴더의 절대 경로.
*/
define('PHUNCTIONAL_PATH', dirname(__FILE__) . '/Phunctional/');

include_once PHUNCTIONAL_PATH . 'Functor.php';
include_once PHUNCTIONAL_PATH . 'PartialApplication.php';
include_once PHUNCTIONAL_PATH . 'Lambda.php';

include_once PHUNCTIONAL_PATH . 'Iterator.php';
include_once PHUNCTIONAL_PATH . 'Mappable.php';
include_once PHUNCTIONAL_PATH . 'Transformation.php';
include_once PHUNCTIONAL_PATH . 'Filterable.php';
include_once PHUNCTIONAL_PATH . 'Filter.php';
include_once PHUNCTIONAL_PATH . 'Combination.php';
include_once PHUNCTIONAL_PATH . 'Range.php';
include_once PHUNCTIONAL_PATH . 'StringIterator.php';
include_once PHUNCTIONAL_PATH . 'Set.php';
include_once PHUNCTIONAL_PATH . 'Zip.php';
include_once PHUNCTIONAL_PATH . 'Injectable.php';
include_once PHUNCTIONAL_PATH . 'KeyIterator.php';
include_once PHUNCTIONAL_PATH . 'Combination.php';
include_once PHUNCTIONAL_PATH . 'iterator-utils.php';

include_once PHUNCTIONAL_PATH . 'types.php';
include PHUNCTIONAL_PATH . 'shortcuts.php';

spl_autoload_register(create_function('$class', '
	static $classes = array(
		"CompositeFunction", "LanguageConstruct",
		"EventHandler", "RawLambda"
	);

	if(in_array($class, $classes)) {
		include_once PHUNCTIONAL_PATH . "$class.php";
		return true;
	}

	return false;
'));

