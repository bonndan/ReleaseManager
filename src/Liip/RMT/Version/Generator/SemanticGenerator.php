<?php

namespace Liip\RMT\Version\Generator;

/**
 * Generator based on the Semantic Versioning defined by Tom Preston-Werner
 * Description available here: http://semver.org/
 */
class SemanticGenerator
{
    /**
     * {@inheritDoc}
     * @throws \InvalidArgumentException
     */
    public function generateNextVersion($currentVersion, $increment)
    {
        // Type validation
        $validTypes = array('patch', 'minor', 'major');
        if (!in_array($increment, $validTypes)){
            throw new \InvalidArgumentException(
                "The option [type] must be one of: {".implode($validTypes, ', ')."}, \"$increment\" given"
            );
        }

        if (!preg_match('#^'.$this->getValidationRegex().'$#', $currentVersion) ){
            throw new \Exception('Current version format is invalid (' . $currentVersion . '). It should be major.minor.patch');
        }

        // Increment
        list($major, $minor, $patch) = explode('.', $currentVersion);
        if ($increment === 'major') {
            $major += 1;
            $patch = $minor = 0;
        }
        else if ($increment === 'minor') {
            $minor += 1;
            $patch = 0;
        }
        else {
            $patch += 1;
        }

        return implode(array($major, $minor, $patch), '.');
    }

    public function getInformationRequests()
    {
        return array('type');
    }

    protected function getValidationRegex()
    {
        return '\d+\.\d+\.\d+';
    }

    public function getInitialVersion()
    {
        return '0.0.0';
    }

    public function compareTwoVersions($a, $b) {
        list($majorA, $minorA, $patchA) = explode('.', $a);
        list($majorB, $minorB, $patchB) = explode('.', $b);
        if ($majorA !== $majorB) {
            return $majorA < $majorB ? -1 : 1 ;
        }
        if ($minorA !== $minorB) {
            return $minorA < $minorB ? -1 : 1 ;
        }
        if ($patchA !== $patchB) {
            return $patchA < $patchB ? -1 : 1 ;
        }
        return 0;
    }
}
