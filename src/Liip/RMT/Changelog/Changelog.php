<?php

namespace Liip\RMT\Changelog;

use Liip\RMT\Version;

/**
 * Changelog file representation.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Changelog
{
    /**
     * changelog xml file path
     * 
     * @var string
     */
    private $file;
    
    /**
     * Constructor
     * 
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->file = $filename;
        if (!file_exists($filename)) {
            $this->createEmptyFile();
        }
    }
    
    /**
     * Returns all version nodes.
     * 
     * @return \DomNodeList
     */
    public function getVersions()
    {
        $doc = $this->getDomDocument();
        $versions = $doc->getElementsByTagName('version');
        return $versions;
    }
    
    /**
     * Add a new version.
     * 
     * The commits must be an associative array (hash => message)
     * 
     * @param string $version
     * @param string $title
     * @param array $commits
     */
    public function addVersion($version, $title, array $commits)
    {
        $doc = $this->getDomDocument();
        $versionElement = $doc->createElement('version');
        $versionElement->setAttribute('version', $version);
        $time = new \DateTime();
        $versionElement->setAttribute('date', $time->format(\DateTime::W3C));
        $versionElement->appendChild($doc->createElement('title', $title));
        
        foreach ($commits as $hash => $message) {
            $commit = $doc->createElement('commit', $message);
            $commit->setAttribute('hash', $hash);
            $versionElement->appendChild($commit);
        }

        $doc->firstChild->appendChild($versionElement);
        $doc->formatOutput = true;
        $doc->save($this->file);
    }
    
    /**
     * Returns the current version.
     * 
     * @return Version|null
     */
    public function getCurrentVersion()
    {
        $nodes = $this->getVersions();
        $versions = array();
        foreach ($nodes as $node) {
            $versions[] = self::getVersionNumberFromVersion($node);
        }
        \usort($versions, '\\vierbergenlars\\SemVer\\version::compare');
        
        $versionNumber = array_pop($versions);
        if (empty($versionNumber)) {
            $versionNumber = Version::INITIAL;
        }
        
        return new Version($versionNumber);
    }
    
    /**
     * Returns the version number from a version dom node.
     * 
     * @param \DOMNode $version
     * @return string
     */
    public static function getVersionNumberFromVersion(\DOMNode $version)
    {
        return $version->attributes->getNamedItem('version')->nodeValue;
    }
    
    /**
     * Returns the version number from a version dom node.
     * 
     * @param \DOMNode $version
     * @return string
     */
    public static function getTitleFromVersion(\DOMNode $version)
    {
        return $version->getElementsByTagName('title')->item(0)->nodeValue;
    }
    
    /**
     * Returns the version number from a version dom node.
     * 
     * @param \DOMNode $version
     * @return array
     */
    public static function getCommitsFromVersion(\DOMNode $version)
    {
        $list = $version->getElementsByTagName('commit');
        $commits = array();
        foreach ($list as $node) {
            /* @var $node \DomNode */
            $hash = $node->attributes->getNamedItem('hash')->nodeValue;
            $commits[$hash] = $node->nodeValue;
        }
        
        return $commits;
    }
    
    /**
     * Loads the xml.
     * 
     * @return \DOMDocument
     */
    private function getDomDocument()
    {
        $document = new \DOMDocument();
        $document->load($this->file);
        
        return $document;
    }
    
    private function createEmptyFile()
    {
        file_put_contents($this->file, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<changelog></changelog>');
    }
}