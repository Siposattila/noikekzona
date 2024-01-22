<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SitemapController extends AbstractController
{
    public function __construct(
        private readonly RouterInterface $routerInterface,
        private readonly CacheInterface $cacheInterface
    ) {
    }

    #[Route('/sitemap.xml', name: 'sitemap_xml')]
    public function sitemap(): Response
    {
        $routes = $this->cacheInterface->get('app.sitemap', function (ItemInterface $item) {
            $routes = array_keys(array_filter(
                $this->routerInterface->getRouteCollection()->all(),
                function ($routeName) {
                    if (str_contains($routeName, 'app')) {
                        return $routeName;
                    }
                },
                ARRAY_FILTER_USE_KEY
            ));

            foreach ($routes as $key => $route) {
                $routes[$key] = $this->routerInterface->generate($route, [], $this->routerInterface::ABSOLUTE_URL);
            }

            $item->expiresAfter(2592000);

            return $routes;
        });

        $response = new Response(
            $this->renderView('sitemap/sitemap.html.twig', ['routes' => $routes]),
            Response::HTTP_OK
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
