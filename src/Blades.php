<?php

namespace Nahid\Permit;

use Illuminate\Support\Facades\Blade;

class Blades
{
    public function runCompiles()
    {

        Blade::directive('userCan', function ($expression) {
            return "<?php if (app(\\Nahid\\Permit\\Permission::class)->userCan({$expression})): ?>";
        });

        Blade::directive('elseDo', function ($expression) {
            return "<?php else: ?>";
        });

        Blade::directive('endUserCan', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('roleCan', function ($expression) {
            return "<?php if (app(\\Nahid\\Permit\\Permission::class)->roleCan({$expression})): ?>";
        });

        Blade::directive('endRoleCan', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('allows', function ($expression) {
            return "<?php if (app(\\Nahid\\Permit\\Permission::class)->can({$expression})): ?>";
        });

        Blade::directive('endAllows', function () {
            return "<?php endif; ?>";
        });
    }
}