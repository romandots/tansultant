<?php

namespace App\Providers;

use NextApps\SwaggerUi\SwaggerUiServiceProvider as SwaggerUiServiceProviderOriginal;

class SwaggerUiServiceProvider extends SwaggerUiServiceProviderOriginal
{
    public function boot() : void
    {
        //Gate::define('viewSwaggerUI', function ($user = null) {
        //    return in_array(optional($user)->email, [
        //        //
        //    ]);
        //});
    }
}
