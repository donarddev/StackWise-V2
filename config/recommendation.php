<?php

return [
    /*
    |--------------------------------------------------------------------------
    | StackWise AI Recommendation Rules
    |--------------------------------------------------------------------------
    |
    | This file is intentionally verbose and data-driven. The recommendation
    | engine reads these rules to compute scores, generate explanations, and
    | keep outputs consistent (language ↔ framework ↔ SDLC).
    |
    */

    'inputs' => [
        // Used for confidence scoring (completeness ratio).
        'required' => [
            'project_name',
            'project_type',
            'selected_features',
            'team_size',
            'complexity',
            'preferred_platform',
            'development_experience',
            'timeline',
            'project_goal',
            'scalability_needs',
            'security_requirements',
            'budget_constraints',
            'maintenance_expectations',
            'deployment_preference',
        ],
    ],

    'catalog' => [
        'languages' => [
            'PHP' => [
                'family' => 'web',
                'learning_curve' => 'low',
                'strengths' => ['web_crud', 'productivity', 'hosting_cost'],
                'weaknesses' => ['high_performance_compute'],
                'frameworks' => ['Laravel', 'Symfony'],
                'typical_use' => 'Web applications, portals, dashboards, CRUD-heavy systems',
            ],
            'Python' => [
                'family' => 'general',
                'learning_curve' => 'low',
                'strengths' => ['ai_ml', 'rapid_prototyping', 'data_processing'],
                'weaknesses' => ['high_throughput_realtime'],
                'frameworks' => ['Django', 'FastAPI', 'Flask'],
                'typical_use' => 'AI/ML features, APIs, data-driven applications',
            ],
            'TypeScript' => [
                'family' => 'javascript',
                'learning_curve' => 'medium',
                'strengths' => ['realtime', 'frontend_backend_unified', 'developer_experience'],
                'weaknesses' => ['cpu_bound_workloads'],
                'frameworks' => ['NestJS', 'Express'],
                'typical_use' => 'Real-time apps, APIs, full-stack JavaScript ecosystems',
            ],
            'Java' => [
                'family' => 'enterprise',
                'learning_curve' => 'high',
                'strengths' => ['enterprise', 'scalability', 'long_term_maintainability'],
                'weaknesses' => ['speed_of_delivery_for_beginners'],
                'frameworks' => ['Spring Boot'],
                'typical_use' => 'Enterprise systems, high scale services, regulated domains',
            ],
            'Go' => [
                'family' => 'systems',
                'learning_curve' => 'medium',
                'strengths' => ['performance', 'concurrency', 'deploy_simplicity'],
                'weaknesses' => ['beginner_ecosystem_depth'],
                'frameworks' => ['Gin'],
                'typical_use' => 'High performance APIs, concurrent services, scalable backends',
            ],
            'Dart' => [
                'family' => 'mobile',
                'learning_curve' => 'medium',
                'strengths' => ['cross_platform_mobile', 'ui_productivity'],
                'weaknesses' => ['backend_ecosystem'],
                'frameworks' => ['Flutter'],
                'typical_use' => 'Cross-platform mobile apps with one codebase',
            ],
        ],

        'frameworks' => [
            'Laravel' => [
                'language' => 'PHP',
                'learning_curve' => 'low',
                'speed' => 'high',
                'maintainability' => 'high',
                'strengths' => ['mvc', 'validation', 'auth', 'rapid_crud'],
            ],
            'Symfony' => [
                'language' => 'PHP',
                'learning_curve' => 'medium',
                'speed' => 'medium',
                'maintainability' => 'high',
                'strengths' => ['enterprise', 'components', 'architecture'],
            ],
            'Django' => [
                'language' => 'Python',
                'learning_curve' => 'medium',
                'speed' => 'high',
                'maintainability' => 'high',
                'strengths' => ['full_stack', 'admin', 'auth', 'rapid_crud'],
            ],
            'FastAPI' => [
                'language' => 'Python',
                'learning_curve' => 'low',
                'speed' => 'high',
                'maintainability' => 'medium',
                'strengths' => ['api', 'performance', 'ai_integration'],
            ],
            'Flask' => [
                'language' => 'Python',
                'learning_curve' => 'low',
                'speed' => 'medium',
                'maintainability' => 'medium',
                'strengths' => ['micro', 'prototype'],
            ],
            'NestJS' => [
                'language' => 'TypeScript',
                'learning_curve' => 'medium',
                'speed' => 'medium',
                'maintainability' => 'high',
                'strengths' => ['architecture', 'api', 'scalable_node'],
            ],
            'Express' => [
                'language' => 'TypeScript',
                'learning_curve' => 'low',
                'speed' => 'high',
                'maintainability' => 'medium',
                'strengths' => ['api', 'simple', 'realtime'],
            ],
            'Spring Boot' => [
                'language' => 'Java',
                'learning_curve' => 'high',
                'speed' => 'medium',
                'maintainability' => 'high',
                'strengths' => ['enterprise', 'security', 'scalability'],
            ],
            'Gin' => [
                'language' => 'Go',
                'learning_curve' => 'medium',
                'speed' => 'medium',
                'maintainability' => 'medium',
                'strengths' => ['performance', 'api'],
            ],
            'Flutter' => [
                'language' => 'Dart',
                'learning_curve' => 'medium',
                'speed' => 'high',
                'maintainability' => 'high',
                'strengths' => ['cross_platform_mobile', 'ui'],
            ],
        ],

        'sdlc_models' => [
            'Agile' => [
                'best_for' => ['changing_requirements', 'iterative_delivery', 'mvp'],
            ],
            'Waterfall' => [
                'best_for' => ['stable_requirements', 'fixed_scope'],
            ],
            'RAD' => [
                'best_for' => ['fast_mvp', 'ui_heavy', 'short_timeline'],
            ],
            'Spiral' => [
                'best_for' => ['high_risk', 'complex', 'security_critical'],
            ],
            'Iterative' => [
                'best_for' => ['moderate_risk', 'progressive_refinement'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Weighted scoring rules
    |--------------------------------------------------------------------------
    |
    | Scores are additive. Each rule can apply to multiple candidates.
    | The engine also collects "evidence" strings used for explanations.
    |
    */
    'weights' => [
        'language' => [
            'project_type' => 28,
            'platform' => 16,
            'features' => 24,
            'experience' => 18,
            'timeline' => 10,
            'scalability' => 22,
            'security' => 16,
            'budget' => 10,
            'maintenance' => 14,
            'deployment' => 8,
        ],
        'framework' => [
            'language_match' => 40,
            'features' => 18,
            'speed' => 14,
            'maintainability' => 14,
            'experience' => 14,
        ],
        'sdlc' => [
            'team_size' => 18,
            'requirements_stability' => 26,
            'timeline' => 18,
            'risk' => 22,
            'complexity' => 16,
        ],
    ],

    'rules' => [
        'project_type' => [
            'web application' => [
                'language' => ['PHP' => 20, 'Python' => 14, 'TypeScript' => 12, 'Java' => 10],
                'feature_hints' => ['crud', 'auth', 'dashboard'],
            ],
            'api system' => [
                'language' => ['TypeScript' => 18, 'Python' => 18, 'Go' => 16, 'Java' => 14, 'PHP' => 10],
                'feature_hints' => ['api', 'integration'],
            ],
            'ai system' => [
                'language' => ['Python' => 26, 'TypeScript' => 10, 'Java' => 10],
                'feature_hints' => ['ai_ml', 'data_processing'],
            ],
            'mobile application' => [
                'language' => ['Dart' => 26, 'TypeScript' => 10],
                'feature_hints' => ['mobile', 'push_notifications'],
            ],
            'desktop application' => [
                'language' => ['TypeScript' => 14, 'Python' => 14, 'Java' => 12],
                'feature_hints' => ['desktop'],
            ],
        ],

        // Values are normalized in the engine, see ProjectContext normalization.
        'scalability_needs' => [
            'low' => ['PHP' => 10, 'Python' => 10],
            'medium' => ['TypeScript' => 12, 'Python' => 10, 'PHP' => 8, 'Java' => 10],
            'high' => ['Java' => 22, 'Go' => 20, 'TypeScript' => 12],
        ],

        'security_requirements' => [
            'basic' => ['PHP' => 6, 'Python' => 6, 'TypeScript' => 6],
            'standard' => ['PHP' => 10, 'Python' => 10, 'TypeScript' => 10, 'Java' => 12],
            'high' => ['Java' => 18, 'Go' => 12, 'PHP' => 10],
        ],

        'budget_constraints' => [
            'low' => ['PHP' => 10, 'Python' => 8, 'TypeScript' => 8],
            'medium' => ['PHP' => 8, 'Python' => 8, 'TypeScript' => 8],
            'high' => ['Java' => 6, 'Go' => 6, 'TypeScript' => 6],
        ],

        'maintenance_expectations' => [
            'low' => ['PHP' => 6, 'Python' => 6],
            'medium' => ['PHP' => 10, 'Python' => 10, 'TypeScript' => 10],
            'high' => ['Java' => 16, 'TypeScript' => 12, 'PHP' => 12],
        ],

        'deployment_preference' => [
            'shared_hosting' => ['PHP' => 14],
            'vps' => ['PHP' => 6, 'Python' => 8, 'TypeScript' => 8, 'Go' => 10],
            'container' => ['Java' => 10, 'Go' => 12, 'TypeScript' => 10, 'Python' => 10],
            'serverless' => ['TypeScript' => 12, 'Python' => 10],
        ],
    ],

    'feature_map' => [
        // Feature keywords -> score adjustments (language + framework).
        'real-time' => ['language' => ['TypeScript' => 22, 'Go' => 12], 'framework' => ['NestJS' => 10, 'Express' => 12]],
        'chat' => ['language' => ['TypeScript' => 16, 'Python' => 10], 'framework' => ['NestJS' => 8, 'FastAPI' => 8]],
        'ai/ml' => ['language' => ['Python' => 26, 'Java' => 8], 'framework' => ['FastAPI' => 14, 'Django' => 10]],
        'analytics' => ['language' => ['Python' => 14, 'PHP' => 6], 'framework' => ['Django' => 6]],
        'authentication' => ['language' => ['PHP' => 10, 'Python' => 8], 'framework' => ['Laravel' => 10, 'Django' => 8, 'Spring Boot' => 8]],
        'payments' => ['language' => ['PHP' => 10, 'TypeScript' => 8, 'Java' => 10], 'framework' => ['Laravel' => 6, 'NestJS' => 6, 'Spring Boot' => 8]],
        'offline-first' => ['language' => ['Dart' => 16], 'framework' => ['Flutter' => 12]],
        'push notifications' => ['language' => ['Dart' => 10, 'TypeScript' => 6], 'framework' => ['Flutter' => 10]],
        'api' => ['language' => ['TypeScript' => 12, 'Python' => 12, 'Go' => 12, 'Java' => 10], 'framework' => ['NestJS' => 10, 'Express' => 10, 'FastAPI' => 12, 'Gin' => 10, 'Spring Boot' => 10]],
    ],
];
