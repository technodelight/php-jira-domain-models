<?php

namespace spec\Technodelight\Jira\Domain\Project;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VersionSpec extends ObjectBehavior
{
    private $version = [
        'projectId' => 12345,
        'released' => true,
        'userReleaseDate' => '30/Jul/15',
        'name' => '0.9.2',
        'self' => 'https://fixture.jira.phar/rest/api/2/version/12441',
        'description' => 'Maintenance release',
        'releaseDate' => '2015-07-30T12:34:56+0000',
        'id' => '12441',
        'archived' => false,
    ];

    function it_is_initializable()
    {
        $this->beConstructedFromArray($this->version);
        $this->id()->shouldReturn(12441);
        $this->name()->shouldReturn('0.9.2');
        $this->description()->shouldReturn('Maintenance release');
        $this->isReleased()->shouldReturn(true);
        $this->releaseDate()->shouldBeLike(new \DateTime('2015-07-30 12:34:56', new \DateTimeZone('+0000')));
        $this->isArchived()->shouldReturn(false);
    }
}
