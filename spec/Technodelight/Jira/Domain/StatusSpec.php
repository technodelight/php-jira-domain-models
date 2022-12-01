<?php

namespace spec\Technodelight\Jira\Domain;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StatusSpec extends ObjectBehavior
{
    private $status = [
        'description' => '',
        'statusCategory' => [
            'name' => 'In Progress',
            'colorName' => 'yellow',
            'key' => 'indeterminate',
            'self' => 'https://fixture.jira.phar/rest/api/2/statuscategory/4',
            'id' => 4,
        ],
        'id' => '10100',
        'name' => 'Shortcut (open/close)',
        'iconUrl' => 'https://fixture.jira.phar/images/icons/statuses/generic.png',
        'self' => 'https://fixture.jira.phar/rest/api/2/status/10100',
    ];

    function it_is_initializable()
    {
        $this->beConstructedFromArray($this->status);
        $this->id()->shouldReturn(10100);
        $this->description()->shouldReturn('');
        $this->name()->shouldReturn('Shortcut (open/close)');
        $this->statusCategory()->shouldReturn('In Progress');
        $this->statusCategoryColor()->shouldReturn('yellow');
    }
}
