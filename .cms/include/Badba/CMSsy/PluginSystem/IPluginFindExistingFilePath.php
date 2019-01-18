<?php

namespace Badba\CMSsy\PluginSystem;

interface IPluginFindExistingFilePath {
	
	public function findExistingFilePath($prevResult, array $probeFilePaths): ?string;
	
}
