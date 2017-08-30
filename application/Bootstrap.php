<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initConfig() {
        $config = $this->getOptions();
        Zend_Registry::set('config', new Core_Config($config, true));
        return Core::config();
    }

    protected function _initDbAdapter() {
        $dbOption = $this->getOption('resources');
        $dbOption = $dbOption['db'];
        // Setup database
        $db = Zend_Db::factory($dbOption['adapter'], $dbOption['params']);
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        //        $db->query("SET NAMES 'utf8'");
        //        $db->query("SET CHARACTER SET 'utf8'");
        //Khi thiet lap che do nay model moi co the su dung duoc
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
        return Core::db();
    }

    protected function _initLog() {
        $cacheDir = APPLICATION_PATH . '/../var/logs';
        $log = new Zend_Log();
        $log->setEventItem('domain', 'error');

        // Non-production
        if (APPLICATION_ENV !== 'production') {
            $log->addWriter(new Zend_Log_Writer_Firebug());
        }

        // Get log config

        $logAdapter = 'file';
        // Set up log
        switch ($logAdapter) {
            //	      case 'database': {
            //	        try {
            //	          $log->addWriter(new Zend_Log_Writer_Db($db, 'engine4_core_log'));
            //	        } catch( Exception $e ) {
            //	          // Make sure logging doesn't cause exceptions
            //	          $log->addWriter(new Zend_Log_Writer_Null());
            //	        }
            //	        break;
            //	      }
            default:
            case 'file': {
                    try {
                        $log->addWriter(new Zend_Log_Writer_Stream($cacheDir . '/' . date('Y-m-d') . '_main.log'));
                    } catch (Exception $e) {
                        // Check directory
                        if (!@is_dir($cacheDir) &&
                                @mkdir($cacheDir, 0777, true)) {
                            $log->addWriter(new Zend_Log_Writer_Stream($cacheDir . '/' . date('Y-m-d') . '_main.log'));
                        } else {
                            // Silence ...
                            if (APPLICATION_ENV !== 'production') {
                                $log->log($e->__toString(), Zend_Log::CRIT);
                            } else {
                                // Make sure logging doesn't cause exceptions
                                $log->addWriter(new Zend_Log_Writer_Null());
                            }
                        }
                    }
                    break;
                }
            case 'none': {
                    $log->addWriter(new Zend_Log_Writer_Null());
                    break;
                }
        }

        // Save to registry
        Zend_Registry::set('log', $log);
        return Core::log();
    }

    protected function _initSession() {
        $cacheDir = APPLICATION_PATH . '/../var/sessions';
        if (!is_readable($cacheDir)) {
            mkdir($cacheDir, 0777);
        } elseif (!is_writable($cacheDir)) {
            chmod($cacheDir, 0777);
        }

//        dòng code này là để tạo file session
//        Zend_Session::setOptions(array(
//            'cookie_lifetime' => 864000, // 10 days
//            'name' => 'onegateid',
//            'strict' => 'off',
//            'save_path' => $cacheDir,
//            'cookie_httponly' => true
//        ));

        try {
            Zend_Session::rememberMe(60*60*24); //timeout session là dòng code này
            Zend_Session::start();
        } catch (Zend_Session_Exception $e) {
            Zend_Session::destroy(); // ZF doesn't allow to start session after destroying
            if (!headers_sent()) {
                $host = $_SERVER['HTTP_HOST'];
                $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                header("Location: http://$host$uri/");
            }
            exit();
        }
        return Core::session();
    }

//     protected function _initAcl(){
//     	$acl = new Core_Auth_Acl();    	
//     	Zend_Registry::set('acl', $acl);
//     	return Core::acl();
//     }

    protected function _initCache() {
        $this->bootstrap('DbAdapter');
        //create default cache
//        $cache = Core_Model_Cache::getCache();
        //create database metacache
        //Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        return Core::cache();
    }

    /**
     * Initialize Doctrine
     * @return Doctrine_Manager
     */
// 	public function _initDoctrine() {
//         // include and register Doctrine's class loader
//         require_once('Doctrine/Common/ClassLoader.php');
//         $classLoader = new \Doctrine\Common\ClassLoader(
//             'Doctrine',
//             APPLICATION_PATH . '/../library/'
//         );
//         $classLoader->register();
//         // create the Doctrine configuration
//         $config = new \Doctrine\ORM\Configuration();
//         // setting the cache ( to ArrayCache. Take a look at
//         // the Doctrine manual for different options ! )
//         $cache = new \Doctrine\Common\Cache\ArrayCache;
//         $config->setMetadataCacheImpl($cache);
//         $config->setQueryCacheImpl($cache);
//         // choosing the driver for our database schema
//         // we'll use annotations
//         $driver = $config->newDefaultAnnotationDriver(
//             APPLICATION_PATH . '/modules/core/models'
//         );
//         $config->setMetadataDriverImpl($driver);
//         // set the proxy dir and set some options
//         $config->setProxyDir(APPLICATION_PATH . '/modules/core/models/Proxies');
//         $config->setAutoGenerateProxyClasses(true);
//         $config->setProxyNamespace('App\Proxies');
//         // now create the entity manager and use the connection
//         // settings we defined in our application.ini
//         $connectionSettings = $this->getOption('doctrine');
//         $conn = array(
//             'driver'    => $connectionSettings['conn']['driv'],
//             'user'      => $connectionSettings['conn']['user'],
//             'password'  => $connectionSettings['conn']['pass'],
//             'dbname'    => $connectionSettings['conn']['dbname'],
//             'host'      => $connectionSettings['conn']['host'],
//         	'charset' => 'utf8'
//         );
//         $entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);
//         // push the entity manager into our registry for later use
//         $registry = Zend_Registry::getInstance();
//         $registry->entitymanager = $entityManager;
//         return $entityManager;
//     }
}
