<?php     
defined('C5_EXECUTE') or die(_("Access Denied."));
class RemoCachePackage extends Package {

	protected $pkgHandle = 'remo_cache';
	protected $appVersionRequired = '5.3.3';
	protected $pkgVersion = '1.0';
	
	public function getPackageDescription() {
		return t("Page cache for Concrete5.");
	}
	
	public function getPackageName() {
		return t("Page Cache");
	}
	
	public function install() {	
		$pkg = parent::install();
		
      $pageCacheDir = DIR_BASE . '/files/page_cache';
      if (!is_dir($pageCacheDir)) {
         mkdir($pageCacheDir);
      }
	}
	
	public function on_start() {  

		Events::extend('on_page_version_approve',
			'RemoCache',
			'updatePageCache',
			'packages/'.$this->pkgHandle.'/models/remocache.php'
			);

		Events::extend('on_start',
			'RemoCache',
			'getPageCache',
			'packages/'.$this->pkgHandle.'/models/remocache.php'
			);	
			
		// TODO: check for delete to clean up the cache..
	}
}