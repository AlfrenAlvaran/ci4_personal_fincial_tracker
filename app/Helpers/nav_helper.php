<?php

if (!function_exists('navActive')) {
    function navActive($route)
    {
        $segment = service('uri')->getSegment(1);

        return $segment === $route
            ? 'bg-success-subtle text-success fw-semibold'
            : 'text-dark';
    }
}