<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
	
class RemoCache 
{
   public static function getPageCache($view) {
		// @TODO: This code is partially redundnat because on_start
		// gets fired a bit too early. If you have lots of pages that
		// can't be cached this might slow down the rendering process
		// a tiny little bit
		$c = Loader::controller($view);
		$c->setupAndRun();

      $cacheFileName = DIR_BASE . '/files/page_cache/' . $view->controller->getCollectionObject()->getCollectionID() . '.html';
      if (file_exists($cacheFileName)) {
         $u = new User();
         if(!$u->IsLoggedIn()) {
         
            // @TODO: At some point it might be better to use the Concrete5 Cache 
            // due to the flexibility to use memcache, sqlite, apc etc.
            $fp = fopen($cacheFileName, 'r');
            fpassthru($fp);
            fclose($fp);
            exit();
         }
      }
   }
   
	public static function updatePageCache($c) {
      $fh = Loader::helper('file');
      $nh = Loader::helper('navigation'); 
      
		$cachable = array('content','image','autonav','person','slideshow','file','google_map','page_list','music','remo_expand','remo_equation','pageear','zoom_image');
		$writeCacheFile = true;
		$blocks = $c->getBlocks();
		foreach($blocks as $b) {
			$blockTypeHandle = $b->getBlockTypeHandle();
			
			if (!in_array($blockTypeHandle, $cachable)) {
				$writeCacheFile = false;
				break;
			}			

		}

		if ($writeCacheFile) {		 
         $content = $fh->getContents($nh->getCollectionURL($c));
         RemoCache::writePageCache($c->getCollectionID(), $content);
		}	
	}
	
	public static function writePageCache($cID, $buffer) {
		$fp = fopen(DIR_BASE . '/files/page_cache/' . $cID . '.html','w');
		fwrite($fp, $buffer);
		fclose($fp);
		
		return null;
	}
	
}
?>