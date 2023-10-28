<?php

namespace Flarum\DiscussionSeoSlugRedirect\Middleware;

use Flarum\Discussion\Discussion;
use Flarum\Http\Exception\RouteNotFoundException;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Flarum\User\User;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Redirect implements MiddlewareInterface
{
    public function __construct(protected UrlGenerator $url)
    {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (RouteNotFoundException $e) {
            // Identify a discussion exists that is visible to this actor
            // that has the path configured as seo slug
            $discussion = $this->identifyDiscussionFromPath(
                $request->getUri()->getPath(),
                RequestUtil::getActor($request)
            );

            // If none are found, pass through the not found exception
            if (! $discussion) {
                throw $e;
            }

            $url = $this->url
                ->to('forum')
                ->route('discussion', [
                'id' => $discussion->id
            ]);

            // Initiate a redirect to the discussion url.
            return new RedirectResponse($url);
        }
    }

    protected function identifyDiscussionFromPath(string $path, User $actor): ?Discussion
    {
        return Discussion::query()
            ->select('id')
            ->whereVisibleTo($actor)
            ->where('seo_slug', $path)
            ->first();
    }
}
