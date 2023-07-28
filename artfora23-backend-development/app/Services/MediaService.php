<?php

namespace App\Services;

use App\Repositories\MediaRepository;
use Artel\Support\Services\EntityService;
use Artel\Support\Traits\FilesUploadTrait;

/**
 * @property MediaRepository $repository
 */
class MediaService extends EntityService
{
    use FilesUploadTrait;

    public function __construct()
    {
        $this->setRepository(MediaRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['name'])
            ->getSearchResults();
    }

    public function create($content, $fileName, $data = [])
    {
        $data['link'] = $this->saveFile($fileName, $content);

        return $this->repository->create($data);
    }
}
