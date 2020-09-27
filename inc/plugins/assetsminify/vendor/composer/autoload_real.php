<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit483d9d52a4a17a75b62a938ee2d5f177 {
	private static $loader;

	public static function loadClassLoader($class) {
		if('Composer\Autoload\ClassLoader' === $class) {
			require __DIR__ . '/ClassLoader.php';
		}
	}

	public static function getLoader() {
		if(null !== self::$loader) {
			return self::$loader;
		}

		spl_autoload_register(array('ComposerAutoloaderInit483d9d52a4a17a75b62a938ee2d5f177', 'loadClassLoader'), true, true);
		self::$loader = $loader = new \Composer\Autoload\ClassLoader();
		spl_autoload_unregister(array('ComposerAutoloaderInit483d9d52a4a17a75b62a938ee2d5f177', 'loadClassLoader'));

		$includePaths = require __DIR__ . '/include_paths.php';
		array_push($includePaths, get_include_path());
		set_include_path(join(PATH_SEPARATOR, $includePaths));

		$map = require __DIR__ . '/autoload_namespaces.php';
		foreach($map as $namespace => $path) {
			$loader->set($namespace, $path);
		}

		$map = require __DIR__ . '/autoload_psr4.php';
		foreach($map as $namespace => $path) {
			$loader->setPsr4($namespace, $path);
		}

		$classMap = require __DIR__ . '/autoload_classmap.php';
		if($classMap) {
			$loader->addClassMap($classMap);
		}

		$loader->register(true);

		$includeFiles = require __DIR__ . '/autoload_files.php';
		foreach($includeFiles as $file) {
			composerRequire483d9d52a4a17a75b62a938ee2d5f177($file);
		}

		return $loader;
	}
}

function composerRequire483d9d52a4a17a75b62a938ee2d5f177($file) {
	require $file;
}
