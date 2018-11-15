<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repository namespace
    |--------------------------------------------------------------------------
    |
    | The namespace for the repository classes.
    |
    */
    'repository_namespace' => env('REPOSITORY_NAMESPACE', 'App\Repositories'),

    /*
    |--------------------------------------------------------------------------
    | Repository path
    |--------------------------------------------------------------------------
    |
    | The path to the repository folder.
    |
    */
    'repository_path' => env('REPOSITORY_PATH', 'app' . DIRECTORY_SEPARATOR . 'Repositories'),

    /*
    |--------------------------------------------------------------------------
    | Criteria namespace
    |--------------------------------------------------------------------------
    |
    | The namespace for the criteria classes.
    |
    */
    'criteria_namespace' => env('CRITERIA_NAMESPACE', 'App\Repositories\Criteria'),

    /*
    |--------------------------------------------------------------------------
    | Criteria path
    |--------------------------------------------------------------------------
    |
    | The path to the criteria folder.
    |
    */
    'criteria_path' => env('CRITERIA_PATH', 'app' . DIRECTORY_SEPARATOR . 'Repositories' . DIRECTORY_SEPARATOR . 'Criteria'),

    /*
    |--------------------------------------------------------------------------
    | Model namespace
    |--------------------------------------------------------------------------
    |
    | The model namespace.
    |
    */
    'model_namespace' => env('MODEL_NAMESPACE', 'App'),

    /*
    |--------------------------------------------------------------------------
    | Criteria Config
    |--------------------------------------------------------------------------
    |
    | Settings of request parameters names that will be used by Criteria
    |
    */
    'criteria' => [
        /*
        |--------------------------------------------------------------------------
        | Accepted Conditions
        |--------------------------------------------------------------------------
        |
        | Conditions accepted in consultations where the Criteria
        |
        | Ex:
        |
        | 'acceptedConditions'=>['=','like']
        |
        | $query->where('foo','=','bar')
        | $query->where('foo','like','bar')
        |
        */
        'acceptedConditions' => [
            '=',
            'like'
        ],
        /*
        |--------------------------------------------------------------------------
        | Request Params
        |--------------------------------------------------------------------------
        |
        | Request parameters that will be used to filter the query in the repository
        |
        | Params :
        |
        | - search : Searched value
        |   Ex: http://prettus.local/?search=lorem
        |
        | - searchFields : Fields in which research should be carried out
        |   Ex:
        |    http://prettus.local/?search=lorem&searchFields=name;email
        |    http://prettus.local/?search=lorem&searchFields=name:like;email
        |    http://prettus.local/?search=lorem&searchFields=name:like
        |
        | - filter : Fields that must be returned to the response object
        |   Ex:
        |   http://prettus.local/?search=lorem&filter=id,name
        |
        | - orderBy : Order By
        |   Ex:
        |   http://prettus.local/?search=lorem&orderBy=id
        |
        | - sortedBy : Sort
        |   Ex:
        |   http://prettus.local/?search=lorem&orderBy=id&sortedBy=asc
        |   http://prettus.local/?search=lorem&orderBy=id&sortedBy=desc
        |
        | - searchJoin: Specifies the search method (AND / OR), by default the
        |               application searches each parameter with OR
        |   EX:
        |   http://prettus.local/?search=lorem&searchJoin=and
        |   http://prettus.local/?search=lorem&searchJoin=or
        |
        */
        'params' => [
            'search' => 'search',
            'searchFields' => 'searchFields',
            'filter' => 'filter',
            'orderBy' => 'orderBy',
            'sortedBy' => 'sortedBy',
            'with' => 'with',
            'searchJoin' => 'searchJoin'
        ]
    ],
];
