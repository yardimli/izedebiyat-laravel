<?php

namespace App\Writer\Services;

use Illuminate\Http\Request;

class DefaultModel
{
    public function initialize(Request $request, ModelCatalog $catalog): void
    {
        $models = $catalog->get();
        if ($request->session()->pull('refresh_model_catalog', false) || empty($models['data'])) {
            $models = $catalog->refresh();
        }
        $user = $request->user();
        if (!$user->model_selected_at || !in_array($user->selected_model, array_column($models['data'], 'id'), true)) {
            $user->selected_model = $models['default_model'];
            $user->model_selected_at = null;
            $user->save();
        }
    }
}
