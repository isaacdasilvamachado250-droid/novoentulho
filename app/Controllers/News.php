<?php

namespace App\Controllers;

use App\Models\NewsModel;
use CodeIgniter\Exceptions\pageNotFoundException;

class News extends BaseController
{
    public function index()
    {
        $model = model(NewsModel::class);

        $data = [
            'news_list' => $model->getNews(),
            'title'     => 'News archive',
        ];

        return view(name: 'news/index', data: $data);
    }

    public function show(?string $slug = null)
    {
        $model = model(NewsModel::class);

        $data['news'] = $model->getNews($slug);

        if (empty($data['news'])){
            throw new PageNotFoundException('News item not found: ' . $slug);
        }
        $data['title'] = $data['news']['title'];
        return view ('news/view' , $data);
    }
}