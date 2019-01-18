<?php

namespace Badba\CMSsy;

use Badba\CMSsy\PluginSystem\IPlugin;
use Badba\CMSsy\PluginSystem\IPluginFetchMiddlewareHandlers;
use Badba\CMSsy\PluginSystem\IPluginRewriteServerRequest;
use Badba\CMSsy\PluginSystem\MainPlugin;
use Badba\CMSsy\PluginSystem\IPluginFindExistingFilePath;
use Badba\CMSsy\Exceptions\ResourceNotFoundException;
use Badba\CMSsy\Exceptions\InternalServerErrorException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;

class ServerRequestHandler implements RequestHandlerInterface, MiddlewareInterface {
	
	/** @var ContainerInterface $config */
	private $config;
	
	/** @var ContainerInterface $factoryLocator */
	private $factoryLocator;
	
	/** @var IPlugin[] $plugins */
	private $plugins;
	
	/** @var IPlugin[] $pluginsByInterface */
	private $pluginsByInterface;
	
	/** @var ServerRequestInterface $request */
	private $request;
	
	/** @var ResponseInterface $response */
	private $response;
	
	/** @var MiddlewareInterface[] $middlewareHandlers */
	private $middlewareHandlers;
	
	/** @var int $iCurMiddlewareHandler */
	private $iCurMiddlewareHandler;
	
	public function __construct(ContainerInterface $config, ContainerInterface $factoryLocator) {
		$this->config = $config;
		$this->factoryLocator = $factoryLocator;
		$this->request = null;
		$this->response = $this->factoryLocator->get(ResponseInterface::class)->createResponse();
		$this->plugins = [];
		$this->pluginsByInterface = [];
		foreach ($this->config->get('plugins') ?? [] as $curPluginClass) {
			$this->registerPluginClass($curPluginClass);
		}
		$this->registerPluginClass(MainPlugin::class);
		$this->middlewareHandlers = \array_merge($this->fetchMiddlewareHandlers(), [$this]);
		$this->iCurMiddlewareHandler = 0;
	}
	
	public function __destruct() {
		foreach ($this->plugins as $plugin) {
			$plugin->finalize();
		}
	}
	
	private function registerPluginClass(string $pluginClass): void {
		/** @var IPlugin $plugin */
		$plugin = new $pluginClass();
		$this->plugins[] = $plugin;
		foreach (\class_implements($pluginClass, true) as $curPluginInterface) {
			if (! isset($this->pluginsByInterface[$curPluginInterface])) {
				$this->pluginsByInterface[$curPluginInterface] = [];
			}
			$this->pluginsByInterface[$curPluginInterface][] = $plugin;
		}
		$plugin->initialize($this);
	}
	
	private function callPluginDrivenMethod($methodName, $methodNameInterface, $args) {
		$curResult = null;
		$newArgs = $args;
		\array_unshift($newArgs, null);
		foreach ($this->pluginsByInterface[$methodNameInterface] as $plugin) {
			$newArgs[0] = $curResult;
			$curResult = $plugin->{$methodName}(... $newArgs);
		}
		return $curResult;
	}
	
	public function callPage(ServerRequestInterface $request): ResponseInterface {
		$rewrittenRequest = $this->rewriteServerRequest($request);
		$curFilePath = $rewrittenRequest->getServerParams()['DOCUMENT_ROOT'] . $rewrittenRequest->getUri()->getPath();
		if ($this->findExistingFilePath([$curFilePath]) === null) {
			throw new ResourceNotFoundException($request->getUri()->__toString());
		}
		$pageContext = new TemplatedPageContext([
			'System' => [
				'request' => $this->getRequest(),
				'response' => $this->getResponse()
			]
		], $this);
		$responseContent = $pageContext->callBlock('file://' . $curFilePath, []);
		$response = $pageContext->varGet('System.response');
		if (! ($response instanceof ResponseInterface)) {
			throw new InternalServerErrorException();
		}
		if (empty($response->getHeader('Content-Type'))) {
			$response = $response->withHeader('Content-Type', 'text/plain; charset=utf-8');
		}
		$response = $response->withBody($this->factoryLocator->get(StreamInterface::class)->createStream($responseContent));
		return $response;
	}
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		$this->request = $request;
		return $this->response = $this->callPage($request);
	}
	
	public function handle(ServerRequestInterface $request): ResponseInterface {
		if ($curMiddlewareHandler = ($this->middlewareHandlers[$this->iCurMiddlewareHandler] ?? null)) {
			$this->iCurMiddlewareHandler ++;
			return $curMiddlewareHandler->process($request, $this);
		} else {
			throw new InternalServerErrorException();
		}
	}
	
	public function findExistingFilePath(array $probeFilePaths): ?string {
		return $this->callPluginDrivenMethod('findExistingFilePath', IPluginFindExistingFilePath::class, [$probeFilePaths]);
	}
	
	public function rewriteServerRequest(ServerRequestInterface $request): ServerRequestInterface {
		return $this->callPluginDrivenMethod('rewriteServerRequest', IPluginRewriteServerRequest::class, [$request]);
	}
	
	public function fetchMiddlewareHandlers(): array {
		return $this->callPluginDrivenMethod('fetchMiddlewareHandlers', IPluginFetchMiddlewareHandlers::class, []);
	}
	
	public function getConfig(): ContainerInterface {
		return $this->config;
	}
	
	public function getResponse(): ResponseInterface {
		return $this->response;
	}
	
	public function getRequest(): ServerRequestInterface {
		return $this->request;
	}
	
	public function getFactoryLocator(): ContainerInterface {
		return $this->factoryLocator;
	}
	
}
