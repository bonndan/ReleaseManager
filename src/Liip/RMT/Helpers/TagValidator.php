<?php

namespace Liip\RMT\Helpers;

use Liip\RMT\Version;
use vierbergenlars\SemVer\SemVerException;

/**
 * Validates a given tag against a regex (from version generator).
 * 
 * 
 */
class TagValidator
{
    /**
     * Check if a tag is valid.
     * 
     * @param string $tag
     * @return boolean
     */
    public function isValid($tag)
    {
        try {
            new Version($tag);
        } catch (SemVerException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Remove all invalid tags from a list
     */
    public function filtrateList($tags)
    {
        $validTags = array();
        foreach ($tags as $tag) {
            if ($this->isValid($tag)) {
                $validTags[] = $tag;
            }
        }
        return $validTags;
    }

}
