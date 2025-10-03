<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/bookings' => [[['_route' => 'booking_create', '_controller' => 'App\\Controller\\BookingCreateController'], null, ['POST' => 0], null, false, false, null]],
        '/events' => [
            [['_route' => 'event_create', '_controller' => 'App\\Controller\\EventCreateController'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'event_filter_list', '_controller' => 'App\\Controller\\EventFilterController'], null, ['GET' => 0], null, false, false, null],
        ],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api(?'
                    .'|/(?'
                        .'|\\.well\\-known/genid/([^/]++)(*:46)'
                        .'|errors(?:/(\\d+))?(*:70)'
                        .'|validation_errors/([^/]++)(*:103)'
                    .')'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:140)'
                    .'|/(?'
                        .'|docs(?:\\.([^/]++))?(*:171)'
                        .'|contexts/([^.]+)(?:\\.(jsonld))?(*:210)'
                    .')'
                .')'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:248)'
                .'|/bookings/([^/]++)(?'
                    .'|/cancel(*:284)'
                    .'|(*:292)'
                .')'
                .'|/events/([^/]++)(*:317)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        46 => [[['_route' => 'api_genid', '_controller' => 'api_platform.action.not_exposed', '_api_respond' => 'true'], ['id'], null, null, false, true, null]],
        70 => [[['_route' => 'api_errors', '_controller' => 'api_platform.action.not_exposed', 'status' => '500'], ['status'], null, null, false, true, null]],
        103 => [[['_route' => 'api_validation_errors', '_controller' => 'api_platform.action.not_exposed'], ['id'], null, null, false, true, null]],
        140 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], null, null, false, true, null]],
        171 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], null, null, false, true, null]],
        210 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], null, null, false, true, null]],
        248 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        284 => [[['_route' => 'booking_cancel', '_controller' => 'App\\Controller\\BookingCancelController'], ['id'], ['DELETE' => 0], null, false, false, null]],
        292 => [[['_route' => 'booking_list', '_controller' => 'App\\Controller\\BookingListingController'], ['id'], ['GET' => 0], null, false, true, null]],
        317 => [
            [['_route' => 'event_display', '_controller' => 'App\\Controller\\EventDisplayController'], ['id'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
