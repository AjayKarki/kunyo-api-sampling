<?php

return [

    # Usage : @hasaccess('create|read') @endhasaccess
    'hasaccess' => function ($arguments) {
        list($accesses, $guard) = explode(',', $arguments.',');

        $accesses = explode('|', str_replace('\'', '', $accesses));

        $expression = "<?php if(auth({$guard})->check() && ( false ";
        foreach ($accesses as $access) {
            $expression .= " || auth({$guard})->user()->can('{$access}')";
        }

        return $expression . ")): ?>";
    },

    'endhasaccess' => function ($arguments) {
        return '<?php endif; ?>';
    },

    'isurl' => function ($arguments) {
        list($urls, $guard) = explode(',', $arguments.',');

        $urls = explode('|', str_replace('\'', '', $urls));

        $urls = "'" . implode ( "', '", $urls ) . "'";

        return "<?php if(request()->is({$urls})) : ?>";
    },

    'endisurl' => function ($arguments) {
        return '<?php endif; ?>';
    },

    'encodedJs' => function ($argument) {
        return "<?php echo htmlspecialchars(json_encode($argument), ENT_QUOTES, 'UTF-8'); ?>";
    },

    # Usage : @hasrole('super-admin|admin') @endhasrole
    'hasrole' => function ($arguments) {
        list($roles, $guard) = explode(',', $arguments.',');

        $roles = explode('|', str_replace('\'', '', $roles));
        $expression = "<?php if(auth({$guard})->check() && ( ";
        $loopIndex = 0;
        foreach ($roles as $role) {
            $expression .= $loopIndex > 0 ? ' || ' : '' . "auth({$guard})->user()->hasRole('{$role}') {$loopIndex}";
            $loopIndex = $loopIndex++;
        }

        return $expression . ")): ?>";
    },

    'endhasrole' => function ($arguments) {
        return '<?php endif; ?>';
    },

];
