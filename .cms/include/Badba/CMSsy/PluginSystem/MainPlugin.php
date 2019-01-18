<?php

namespace Badba\CMSsy\PluginSystem;

use Badba\CMSsy\Exceptions\InternalServerErrorException;
use Badba\CMSsy\Exceptions\MustRedirectException;
use Badba\CMSsy\Exceptions\ResourceNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Badba\CMSsy\Exceptions\ResourceForbiddenException;

class MainPlugin extends BasePlugin 
	implements 
		IPluginFindExistingFilePath, 
		IPluginRewriteServerRequest,
		IPluginFetchMiddlewareHandlers,
		MiddlewareInterface {
	
	public function findExistingFilePath($prevResult, array $probeFilePaths): ?string {
		if ($prevResult) {
			return $prevResult;
		} else {
			foreach ($probeFilePaths as $curProbeFilePath) {
				if (\file_exists($curProbeFilePath)) {
					return $curProbeFilePath;
				}
			}
			return null;
		}
	}
	
	public function rewriteServerRequest($prevResult, ServerRequestInterface $request): ServerRequestInterface {
		$curRequest = ($prevResult && ($prevResult !== $request)) ? $prevResult : $request;
		$requestURI = $curRequest->getUri();
		if (\substr($requestURIPath = $requestURI->getPath(), -1, 1) === '/') {
			$requestURI = $requestURI->withPath($requestURIPath . 'index.php');
		} else {
			$requestURI = $requestURI->withPath($requestURIPath . '.php');
		}
		return $curRequest->withUri($requestURI);
	}
	
	public function fetchMiddlewareHandlers($prevResult): array {
		return \array_merge([$this, new SessionMiddleware()], $prevResult ?? []);
	}
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		$factoryLocator = $this->getOwner()->getFactoryLocator();
		$response = null;
		try {
			try {
				$response = $handler->handle($request);
			} catch (\Throwable $t) {
				$config = $this->getOwner()->getConfig();
				if ($config->has('errorPages')) {
					$errorPagePath = $config->get('errorPages')[\get_class($t)] ?? null;
					if ($errorPagePath !== null) {
						$response = $this->getOwner()->callPage($factoryLocator->get(ServerRequestInterface::class)->createServerRequest(
							'GET',
							$factoryLocator->get(UriInterface::class)->createUri($errorPagePath),
							$request->getServerParams()));
					}
				}
				throw $t;
			}
		} catch (ResourceNotFoundException $e) {
			$response = $response ?? $factoryLocator->get(ResponseInterface::class)->createResponse();
			$response = $response->withStatus(404);
		} catch (ResourceForbiddenException $e) {
			$response = $response ?? $factoryLocator->get(ResponseInterface::class)->createResponse();
			$response = $response->withStatus(403);
		} catch (MustRedirectException $e) {
			$response = $response ?? $factoryLocator->get(ResponseInterface::class)->createResponse();
			$response = $response->withStatus($e->getRedirectCode());
			$response = $response->withHeader('Location', $e->getRedirectLocation());
		} catch (InternalServerErrorException $e) {
			$response = $response ?? $factoryLocator->get(ResponseInterface::class)->createResponse();
			$response = $response->withStatus(500);
		} catch (\Throwable $t) {
			$response = $response ?? $factoryLocator->get(ResponseInterface::class)->createResponse();
			$response = $response->withBody($factoryLocator->get(StreamInterface::class)->createStream($t->getMessage() . "\r\n" . $t->getTraceAsString()));
			$response = $response->withStatus(500);
		}
		return $response;
	}	

}
