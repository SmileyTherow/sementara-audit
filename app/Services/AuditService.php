<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AuditService
{
    public static function record(string $action, Model $model, array $old = []): void
    {
        $new = $model->getAttributes();

        unset(
            $new['password'],
            $new['remember_token'],
            $old['password'],
            $old['remember_token']
        );

        DB::table('audit_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => json_encode($old),
            'new_values' => json_encode($new),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
