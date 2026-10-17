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
