<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
	
class RemoCache 
{
	/*
	 * Get page content from cache, if it exists. Otherwise, get and cache the
	 * content, if we can. We are passed a view object, but we are interested
	 * in the global collection object (i.e., the page). If it is not a page,
	 * we are done. If the user POSTed information, we are done, because the
	 * controller needs to process the information. If the user is logged in
	 * we are done, because we don't want to leak private information into the
	 * cache, where it might be accessed by unregistered users. If all of the
	 * checks pass, look in the cache for the page. If we find a cache entry
	 * and the information is page content, send the content to the output and
	 * exit, because this request has been processed.
	 *
	 * If we find a cache entry but it is marked that no cache information is
	 * available, we continue, as normal. This allows us to negatively cache
	 * pages so that we don't keep checking uncacheable pages to see if they
	 * are cachable. As a result, there is hardly any speed penalty to caching
	 * all pages.
	 *
	 * If we don't find anything in the cache at all, we haven't seen this
	 * page before. We first create a cache entry for this page and flag it
	 * that no cache information is available and then check if we can cache
	 * this page. We get all of the blocks on the page and check if each is
	 * cacheable. If any block is not cacheable, then the page is not cachable,
	 * and we continue, leaving the page marked in cache as not available. The
	 * next time this page is requested, we will check the cache and see right
	 * away that no informaton is available.
	 *
	 * If we checked all of the blocks on the page and all of them are cacheable, we
	 * fetch the page with a web request to ourself and put the content in cache. The
	 * reason that we can get the web page while we are processing the web page is
	 * because we just flagged it in cache as not available from cache. The next request
	 * for the page will bypass cache and continue normally. When we receive the content,
	 * we update the cache entry with the content. The next request for the page will be
	 * served from cache.
	 *
	 * Note that the cache id is based on both the host and the page, in case a page
	 * returns different information for different domain names.
	 *
	*/
	public static function getPageCache($vobj) {
		global $c;
		if ($c instanceof page && $_SERVER["REQUEST_METHOD"] != "POST" && !isset($_SERVER["HTTPS"])) {
			$u = new User();
			if (!$u->IsLoggedIn()) {				// Only public information
				$pkgHandle = "remo_cache";
				$noCache = "nocache";					// Marker, no cache info available

				$ca = new Cache();
				$host = strtolower($_SERVER["HTTP_HOST"]);
				$cID = $c->getCollectionID() ;
				$id = $host . ":" . $cID;			// $id is based on the host and cID
				if ($p = $ca->get($pkgHandle, $id)) {		// Have a cache entry?
					if (strcmp($p, $noCache)) {		// Information is available?
						echo $p;			// Ship it to the output
						exit();				// and we are out of here.
					}
				} else {
					$ca->set($pkgHandle, $id, $noCache);	// No cache info, yet
					$cachable = array('flash' => true, 'search' => true, 'youtube' => true, 'survey' => true, 'content' => true, 'image' => true, 'autonav' => true, 'person' => true, 'slideshow' => true, 'file' => true, 'google_map' => true, 'page_list' => true, 'music' => true, 'remo_expand' => true, 'remo_equation' => true, 'pageear' => true, 'zoom_image' => true);
					$writeCache = true;			// Assume all blocks are cacheable
					$blocks = $c->getBlocks();
					while ($writeCache && (list(, $b) = each($blocks))) {
						$blockTypeHandle = $b->getBlockTypeHandle();
						$writeCache = $cachable[$blockTypeHandle];
					}

					if ($writeCache) {			// We can cache this page?
						$fh = Loader::helper('file');
						$nh = Loader::helper('navigation'); 
						$content = $fh->getContents($nh->getCollectionURL($c));
						$result = $ca->set($pkgHandle, $id, $content);
					}
				}
			}
		}
	}

	/*
	 * This function is called to invalidate cached page content, presumeably after a
	 * page has been edited/approved. We simply remove the cached information for this
	 * page. On the next request to this page, the information will be cached, again,
	 * if possible.
	*/
	public static function deletePageCache($c) {
		$pkgHandle = "remo_cache";
		$ca = new Cache();
		$host = strtolower($_SERVER["HTTP_HOST"]);
		$cID = $c->getCollectionID();
		$id = $host . ":" . $cID;
		$ca->delete($pkgHandle, $id);
	}
}
?>
