<?php

namespace App\Http\Services;


class MediaService
{
    public function createMedia($model, $file, $collection = "images")
    {
        return $model->addMedia($file)->toMediaCollection($collection);
    }
    //edit
    public function editMedia($model, $file, $collection = "images")
    {
        if ($model->getMedia($collection)->isNotEmpty()) {
            $model->clearMediaCollection($collection);
        }
        return $model->addMedia($file)->toMediaCollection($collection);
    }
    //delete
    public function deleteMedia($model, $collection = "images")
    {
        if ($model->getMedia($collection)->isNotEmpty()) {
            $model->clearMediaCollection($collection);
        }
        return true;
    }
}
