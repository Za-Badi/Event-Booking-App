<?php

namespace App\Service;

class ImageService
{
    public function createFile($model, $files, $collection, $replace = false)
    {
        if ($replace) {
            $model->clearMediaCollection($collection);
        }

        if (is_array($files)) {
            foreach ($files as $file) {
                $model->addMedia($file)->toMediaCollection($collection);
            }
        } else {
            $model->addMedia($files)->toMediaCollection($collection);
        }
    }

    public function updateImages($model, $files, $collection)
    {
        $this->createFile($model, $files, $collection, true);
    }


    public function deleteImages($model, $collection)
    {
        if ($model->getMedia($collection)->isNotEmpty()) {
            $model->clearMediaCollection($collection);
        }
    }
}
