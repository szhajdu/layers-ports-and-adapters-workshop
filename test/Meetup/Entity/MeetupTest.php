<?php
declare(strict_types = 1);

namespace Tests\Meetup\Entity;

use Meetup\Entity\Meetup;
use Meetup\Entity\MeetupId;
use Meetup\Entity\MemberId;
use Meetup\Entity\Name;
use Meetup\Entity\Rsvp;
use Meetup\Entity\Description;

final class MeetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_scheduled_with_just_a_name_description_and_date()
    {
        $id = MeetupId::fromString('id');
        $name = Name::fromString('Name');
        $description = Description::fromString('Description');
        $scheduledFor = new \DateTimeImmutable('now');

        $meetup = Meetup::schedule($id, $name, $description, $scheduledFor);

        $this->assertInstanceOf(Meetup::class, $meetup);
        $this->assertEquals($id, $meetup->id());
        $this->assertEquals($name, $meetup->name());
        $this->assertEquals($description, $meetup->description());
        $this->assertEquals($scheduledFor, $meetup->scheduledFor());
    }

    /**
     * @test
     */
    public function a_member_can_rsvp_yes_for_the_meetup()
    {
        $meetupId = MeetupId::fromString('id');
        $meetup = Meetup::schedule($meetupId, Name::fromString('Name'), Description::fromString('Description'),
            new \DateTimeImmutable('now'));
        $memberId = MemberId::fromString('member id');

        $rsvp = $meetup->rsvpYes($memberId);
        $this->assertInstanceOf(Rsvp::class, $rsvp);
        $this->assertEquals($meetupId, $rsvp->meetupId());
        $this->assertEquals($memberId, $rsvp->memberId());
        $this->assertEquals(Rsvp::YES, $rsvp->answer());
    }

    /**
     * @test
     */
    public function can_determine_whether_or_not_it_is_upcoming()
    {
        $now = new \DateTimeImmutable();

        $pastMeetup = Meetup::schedule(
            MeetupId::fromString('some id'),
            Name::fromString('Name'),
            Description::fromString('Description'),
            new \DateTimeImmutable('-5 days')
        );
        $this->assertFalse($pastMeetup->isUpcoming($now));

        $upcomingMeetup = Meetup::schedule(
            MeetupId::fromString('some id'),
            Name::fromString('Name'),
            Description::fromString('Description'),
            new \DateTimeImmutable('+5 days')
        );
        $this->assertTrue($upcomingMeetup->isUpcoming($now));
    }
}