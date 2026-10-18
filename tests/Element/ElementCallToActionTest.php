<?php

namespace Dynamic\Elements\CTA\Elements\Test;

use SilverStripe\Forms\FieldList;
use SilverStripe\LinkField\Models\ExternalLink;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\Dev\SapphireTest;
use Dynamic\Elements\CTA\Elements\ElementCallToAction;

class ElementCallToActionTest extends SapphireTest
{
    protected static $fixture_file = 'call-to-action.yml';

    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(ElementCallToAction::class, 'one');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     * Regression test: duplicate() used to copy the CtaLinkID foreign key, so the duplicate and
     * its original shared - and both $owned - a single Link record. Editing one block's button
     * silently changed the other one.
     */
    public function testDuplicateCopiesCtaLink(): void
    {
        $link = ExternalLink::create();
        $link->ExternalUrl = 'https://example.com/cta';
        $link->LinkText = 'Find out more';
        $link->write();

        $original = $this->objFromFixture(ElementCallToAction::class, 'one');
        $original->CtaLinkID = $link->ID;
        $original->write();

        // Mirror what linkfield's own LinkField does once the owner record has an ID.
        $link->OwnerID = $original->ID;
        $link->OwnerClass = $original->ClassName;
        $link->OwnerRelation = 'CtaLink';
        $link->write();

        $this->assertSame(
            (int)$original->ID,
            (int)$link->Owner()->ID,
            'Precondition: the original Link resolves its owner.'
        );

        $duplicate = $original->duplicate();

        $this->assertNotEquals(0, $original->CtaLinkID, 'Precondition: the original has a link.');
        $this->assertNotEquals(
            $original->CtaLinkID,
            $duplicate->CtaLinkID,
            'The duplicate must get its own Link record instead of sharing the original\'s.'
        );
        $this->assertTrue(
            $duplicate->CtaLink()->exists(),
            'The duplicated Link record must exist.'
        );
        $this->assertEquals(
            $original->CtaLink()->getURL(),
            $duplicate->CtaLink()->getURL(),
            'The duplicated Link must keep pointing at the same URL.'
        );
        $this->assertSame(
            'https://example.com/cta',
            $duplicate->CtaLink()->getURL(),
            'Precondition: the URL was actually stored, so the comparison above is not vacuous.'
        );
        $this->assertSame(
            'Find out more',
            $duplicate->CtaLink()->LinkText,
            'The duplicated Link must carry the original link\'s data.'
        );
        $this->assertInstanceOf(
            ExternalLink::class,
            $duplicate->CtaLink(),
            'The duplicated Link must keep its link type instead of degrading to a plain Link.'
        );

        $this->assertSame(
            (int)$duplicate->ID,
            (int)$duplicate->CtaLink()->OwnerID,
            'The duplicated Link must be owned by the duplicated block, not by the original one.'
        );
        $this->assertNotNull(
            $duplicate->CtaLink()->Owner(),
            'The duplicated Link must still resolve its owner, so linkfield\'s permission checks '
            . 'keep working off the owning block instead of falling back to canCreate().'
        );
        $this->assertSame(
            (int)$duplicate->ID,
            (int)$duplicate->CtaLink()->Owner()->ID,
            'The duplicated Link\'s owner must be the duplicated block.'
        );
        $this->assertSame(
            'CtaLink',
            $duplicate->CtaLink()->OwnerRelation,
            'The duplicated Link must record which relation points at it.'
        );

        // The original Link row must still belong to the original element, i.e. the fix created a
        // new row rather than re-pointing the existing one at the duplicate.
        $this->assertSame(
            $link->ID,
            (int)ElementCallToAction::get()->byID($original->ID)->CtaLinkID
        );
        $this->assertSame(
            'https://example.com/cta',
            ExternalLink::get()->byID($link->ID)->ExternalUrl
        );
    }

    /**
     * DNADesign\Elemental\Models\ElementalArea duplicates a page's blocks with duplicate(false), so
     * the block has no ID yet when the Link is copied. The Link must still be re-pointed at the
     * block once the block itself is written.
     */
    public function testUnwrittenDuplicateRepointsLinkOwnerWhenWritten(): void
    {
        $link = ExternalLink::create();
        $link->ExternalUrl = 'https://example.com/page-copy';
        $link->write();

        $original = $this->objFromFixture(ElementCallToAction::class, 'one');
        $original->CtaLinkID = $link->ID;
        $original->write();
        $link->OwnerID = $original->ID;
        $link->OwnerClass = $original->ClassName;
        $link->OwnerRelation = 'CtaLink';
        $link->write();

        $duplicate = $original->duplicate(false);

        $this->assertEquals(0, (int)$duplicate->ID, 'Precondition: the duplicate is still unwritten.');
        $this->assertNotEquals(0, (int)$duplicate->CtaLinkID, 'Precondition: the Link was copied.');

        $duplicate->write();

        $this->assertNotEquals(0, (int)$duplicate->ID, 'Precondition: the duplicate is written.');
        $this->assertSame(
            (int)$duplicate->ID,
            (int)$duplicate->CtaLink()->OwnerID,
            'A Link copied by an unwritten duplicate must be re-pointed when the block is written.'
        );
        $this->assertSame(
            (int)$link->ID,
            (int)ElementCallToAction::get()->byID($original->ID)->CtaLinkID,
            'The original block must keep its own Link.'
        );
    }

    /**
     * A caller can ask duplicate() to skip relation copying, in which case the clone shares the
     * original's Link row instead of getting one of its own. That shared row must keep the original
     * block as its owner.
     */
    public function testDuplicateWithoutLinkRelationLeavesSharedLinkOwnerAlone(): void
    {
        $link = ExternalLink::create();
        $link->ExternalUrl = 'https://example.com/shared';
        $link->write();

        $original = $this->objFromFixture(ElementCallToAction::class, 'one');
        $original->CtaLinkID = $link->ID;
        $original->write();
        $link->OwnerID = $original->ID;
        $link->OwnerClass = $original->ClassName;
        $link->OwnerRelation = 'CtaLink';
        $link->write();

        $duplicate = $original->duplicate(true, []);

        $this->assertSame(
            (int)$original->CtaLinkID,
            (int)$duplicate->CtaLinkID,
            'Precondition: no Link was copied, so the duplicate shares the original\'s row.'
        );
        $this->assertSame(
            (int)$original->ID,
            (int)ExternalLink::get()->byID($link->ID)->OwnerID,
            'A Link the duplicate did not create must keep the original block as its owner.'
        );
        $this->assertSame(
            (int)$original->ID,
            (int)$link->Owner()->ID,
            'The original block must still resolve as the Link\'s owner.'
        );
    }

    /**
     * Duplicating an element that has no link must not error and must not invent one.
     */
    public function testDuplicateWithoutCtaLink(): void
    {
        $original = ElementCallToAction::create();
        $original->Title = 'No link block';
        $original->HTML = '<p>Some content</p>';
        $original->write();

        $duplicate = $original->duplicate();

        $this->assertEquals(0, $duplicate->CtaLinkID);
        $this->assertFalse($duplicate->CtaLink()->exists());
        $this->assertSame(0, Link::get()->count(), 'No Link row must be created for a link-less block.');
    }
}
