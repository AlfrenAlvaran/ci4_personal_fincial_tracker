<?php

namespace App\Cells;

class Modal
{
    public function render(
        string $id,
        string $title,
        string $view,
        array $data = [],
        string $size = 'modal-lg'
    ): string {
        return view('components/modal', [
            'id'      => $id,
            'title'   => $title,
            'size'    => $size,
            'action'  => $data['action'] ?? '',
            'content' => view($view, $data),
        ]);
    }
}