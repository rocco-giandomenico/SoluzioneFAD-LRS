<?php

namespace Trax\XapiStore\Permissions;

use Trax\Auth\Permissions\Permission;

class AllReadPermission extends Permission
{
    /**
     * The permission lang key: you MUST override this property.
     *
     * @var string
     */
    protected $langKey = 'trax-xapi-store::permissions.all_read';

    /**
     * The permission capabilities: you MUST override this property.
     *
     * @var array
     */
    protected $capabilities = [
        'statement.read.entity',
        'state.read.owner',
        'activity_profile.read.owner',
        'agent_profile.read.owner',
        'activity.read.owner',
        'agent.read.owner',
    ];

    /**
     * Is the permission for users: you SHOULD override this property.
     *
     * @var array
     */
    protected $supportedConsumerTypes = ['app'];
}
