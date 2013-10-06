<?php

namespace Liip\RMT\Tests\Unit\Version;

use Liip\RMT\Context;

class SemanticGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getVersionValues
     */
    public function testIncrement($current, $type, $result)
    {
        $generator = new \Liip\RMT\Version\Generator\SemanticGenerator();
        $this->assertEquals($result, $generator->generateNextVersion($current, $type));
    }

    public function getVersionValues()
    {
        return array(
            array('1.0.0',  'patch', '1.0.1'),
            array('1.23.0', 'minor', '1.24.0'),
            array('1.1.19', 'minor', '1.2.0'),
            array('1.0.0',  'major',  '2.0.0'),
            array('1.19.3', 'major',  '2.0.0'),
            array('3.3.3',  'major',  '4.0.0')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The option [type] must be one of: {patch, minor, major}, "full" given
     */
    public function testIncrementWithInvalidType()
    {
        $generator = new \Liip\RMT\Version\Generator\SemanticGenerator();
        $generator->generateNextVersion('1.0.0', 'full');
    }

}
