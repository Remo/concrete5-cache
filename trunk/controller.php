<?php     
defined('C5_EXECUTE') or die(_("Access Denied."));
class RemoCachePackage extends Package {

	protected $pkgHandle = 'remo_cache';
	protected $appVersionRequired = '5.4.0';
	protected $pkgVersion = '1.2';
	
	public function getPackageDescription() {
		return t("Page cache for Concrete5.");
	}
	
	public function getPackageName() {
		return t("Page Cache");
	}
	
	public function install() {	
		$pkg = parent::install();
		
	}
	
	public function on_start() {  

		Events::extend('on_page_version_approve',
			'RemoCache',
			'deletePageCache',
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
