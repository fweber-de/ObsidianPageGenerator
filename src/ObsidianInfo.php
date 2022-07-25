<?php

namespace ObsPager;

/**
 * ObsidianInfo
 *
 * @author Florian Weber <git@fweber.info>
 */
class ObsidianInfo
{
    /**
     * @var string
     */
    public string $workspaceName;

    /**
     * @var string|null
     */
    public ?string $attachmentFolderPath = null;

    public function __construct()
    {
    }
}
