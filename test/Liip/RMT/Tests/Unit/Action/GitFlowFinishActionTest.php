<?php
namespace Liip\RMT\Tests\Unit\Changelog;

use Liip\RMT\Action\GitFlowFinishAction;
use Liip\RMT\Context;
use Liip\RMT\Exception;
use Liip\RMT\Version\Detector\GitFlowBranch;
use PHPUnit_Framework_TestCase;


class GitFlowFinishActionTest extends PHPUnit_Framework_TestCase
{
    private $context;
    private $git;
    private $informationCollector;
    
    /**
     * system under test
     * @var \Liip\RMT\Action\GitFlowFinishReleaseAction 
     */
    private $action;
    
    public function setUp()
    {
        $this->prepareAction(GitFlowBranch::RELEASE);
    }
    
    private function prepareAction($branchType)
    {
        $this->git = $this->getMock("\Liip\RMT\VCS\GitFlow");
        
        $this->context = new Context();
        $this->context->setService('vcs', $this->git);
        $this->context->setService('output', $this->getMock("\Liip\RMT\Output\Output"));
        $this->informationCollector = $this->getMock("\Liip\RMT\Information\InformationCollector");
        $this->context->setService('information-collector', $this->informationCollector);
        $this->action = new GitFlowFinishAction($branchType);
        $this->action->setContext($this->context);
    }
    
    public function testFinishRelease()
    {
        $this->git->expects($this->once())
                ->method('finishRelease')
                ->will($this->returnValue('release/1.2.3'));
        
        $this->action->execute();
    }
    
    public function testFinishHotfix()
    {
        $this->prepareAction(GitFlowBranch::HOTFIX);
        
        $this->git->expects($this->once())
                ->method('finishHotfix')
                ->will($this->returnValue('hotfix/1.2.3'));
        
        $this->action->execute();
    }
    
    public function testFinishReleaseWorksWithDefaultGit()
    {
        $tempDir = tempnam(sys_get_temp_dir(),'');
        if (file_exists($tempDir)) {
            unlink($tempDir);
        }
        mkdir($tempDir);
        chdir($tempDir);
        exec('git init');
        $this->testDir = $tempDir;
        
        $this->git = $this->getMock("\Liip\RMT\VCS\Git");
        $this->context->setService('vcs', $this->git);
        
        //Expects that the mock is replaced.
        $this->setExpectedException("\Liip\RMT\Exception", "Not currently on any branch");
        $this->action->execute();
    }
    
    public function testFinishReleaseException()
    {
        $this->git->expects($this->once())
                ->method('finishRelease')
                ->will($this->throwException(new Exception('test')));
        
        $this->setExpectedException("\Liip\RMT\Exception");
        $this->action->execute();
    }
}
