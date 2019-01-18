<?php

namespace Badba\CMSsy;

use Badba\CMSsy\Utils\StringHelper;

class TemplatedPageContext implements IPageContext {
	
	/** @var FrontController $owner */
	private $owner;
	
	/** @var array $vars */
	private $vars;
	
	/** @var string $docRoot*/
	private $docRoot;
	
	public function __construct($data, $owner) {
		$this->owner = $owner;
		$this->vars = $data;
		$this->docRoot = $this->owner->getRequest()->getServerParams()['DOCUMENT_ROOT'];
	}
	
	public function __call($name, $arguments) {
		if (\substr($name, -3, 3) === 'Esc') {
			$rawResult = self::{\substr($name, 0, -3)}(... $arguments);
			return $this->strEsc($rawResult);
		}
	}
	
	private function getValueContainer($name) {
		$curContainer = &$this->vars;
		$splName = \explode('.', $name);
		$iLastPart = \count($splName) - 1;
		foreach ($splName as $iPart => $curNamePart) {
			if ($iPart < $iLastPart) {
				if (\is_array($curContainer[$curNamePart] ?? null)) {
					$curContainer = &$curContainer[$curNamePart];
				} else {
					return [&$curContainer, $splName[$iPart + 1]];
				}
			} else {
				return [&$curContainer, $curNamePart];
			}
		}
	}
	
	public function varGet(string $name) {
		$vc = $this->getValueContainer($name);
		return $vc[0][$vc[1]] ?? null;
	}
	
	public function varSet(string $name, $value, $mode = null) {
		$vc = $this->getValueContainer($name);
		if ($mode === true) {
			$vc[0][$vc[1]] = $value($vc[0][$vc[1]]);
		} else {
			$vc[0][$vc[1]] = $value;
		}
	}
	
	public function varIsSet(string $name) {
		$vc = $this->getValueContainer($name);
		return isset($vc[0][$vc[1]]);
	}
	
	public function valueCapture(callable $handler) {
		\ob_start();
		$handler();
		return \ob_get_clean();
	}
	
	private function evaluateLocally(/* filePath */) {
		include \func_get_arg(0);
	}
	
	public function callBlock(string $blockName, ?array $blockArgs = []) {
		$blockPageData = \array_merge($this->vars, [
			'arguments' => $blockArgs
		]);
		$fileProtocolPrefix = 'file://';
		if (\substr($blockName, 0, \strlen($fileProtocolPrefix)) === $fileProtocolPrefix) {
			$straightFilePath = \substr($blockName, \strlen($fileProtocolPrefix));
			/*$docRoot = $this->getOwner()->getRequest()->getServerParams()['DOCUMENT_ROOT'];
			$curPath = $straightFilePath;
			while (\strlen($curPath) >= $docRoot) {
				$splCurPath = \explode('/', $curPath);
				\array_pop($splCurPath);
				
			}
			$probePaths = [$straightFilePath];*/
			$blockFilePath = $this->owner->findExistingFilePath([$straightFilePath]);
		} else {
			$blockFilePath = $this->owner->findExistingFilePath([
				$this->docRoot . '/.cms/templates/' . $blockName . '/block.php',
				$this->docRoot . '/.custom/templates/' . $blockName . '/block.php'
			]);
		}
		$blockEvalResult = null;
		$result = $this->valueCapture(function() use ($blockPageData, $blockFilePath, &$blockEvalResult) {
			$page = new static($blockPageData, $this->owner);
			$page->evaluateLocally($blockFilePath);
			$blockEvalResult = $page->getVars();
		});
		$template = $blockEvalResult['template'] ?? null;
		unset($blockEvalResult['template']);
		unset($blockEvalResult['arguments']);
		$this->vars = \array_merge($this->vars, $blockEvalResult);
		if ($template !== null) {
			$result = self::callBlock($template, [
				'content' => $result
			]);
		}
		return $result;
	}
	
	public function strEsc($value) {
		if (\is_string($value)) {
			return StringHelper::strEsc($value);
		} else
		if (\is_array($value)) {
			return \array_map(function($value) {
				return $this->strEsc($value);
			}, $value);
		} else 
		if ($value instanceof \Traversable) {
			$result = [];
			foreach ($value as $k => $v) {
				$result[$k] = $this->strEsc($v);
			}
			return $result;
		} else {
			return $value;
		}
	}
	
	public function getVars() {
		return $this->vars;
	}
	
	public function getOwner() {
		return $this->owner;
	}
	
}
