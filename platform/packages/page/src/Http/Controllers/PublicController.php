<?php

namespace Botble\Page\Http\Controllers;

use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Illuminate\Routing\Controller;
use Response;
use SlugHelper;
use Theme;

class PublicController extends Controller
{
    /**
     * @var SlugInterface
     */
    protected $slugRepository;

    /**
     * PublicController constructor.
     * @param SlugInterface $slugRepository
     */
    public function __construct(SlugInterface $slugRepository)
    {
        $this->slugRepository = $slugRepository;
    }

    /**
     * @param string $slug
     * @param PageService $pageService
     * @return Response
     */
    public function getPage($slug, PageService $pageService)
    {
        $slug = $this->slugRepository->getFirstBy([
            'key'    => $slug,
            'prefix' => SlugHelper::getPrefix(Page::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $data = $pageService->handleFrontRoutes($slug);

        return Theme::scope($data['view'], $data['data'], $data['default_view'])->render();
    }
}
