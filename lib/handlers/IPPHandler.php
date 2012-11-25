<?php
interface IPPHandler {
	/**
	 * 
	 * @param IPPHttpConfig $httpConfig
	 */
	public function handle($httpConfig);
}