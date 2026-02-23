<?php

use App\Models\User;
use App\Services\CommunicationAuthorizationService;

it('denies self communication', function () {
    $service = new CommunicationAuthorizationService;
    $user = new User(['id' => 1, 'role' => 'company', 'company_id' => 10]);

    expect($service->canUsersCommunicate($user, $user))->toBeFalse();
});

it('allows admin to communicate with anyone', function () {
    $service = new CommunicationAuthorizationService;
    $admin = new User(['id' => 1, 'role' => 'admin']);
    $freelancer = new User(['id' => 2, 'role' => 'freelancer']);

    expect($service->canUsersCommunicate($admin, $freelancer))->toBeTrue();
});
