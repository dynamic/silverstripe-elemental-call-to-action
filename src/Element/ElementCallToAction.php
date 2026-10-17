<?php

namespace Dynamic\Elements\CTA\Elements;

use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\Forms\FieldList;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;

/**
 * Class \Dynamic\Elements\CTA\Elements\ElementCallToAction
 *
 * @property string $CtaLink
 */
class ElementCallToAction extends ElementContent
{
    /**
     * @var string
     */
    private static string $singular_name = 'Call to Action';

    /**
     * @var string
     */
    private static string $plural_name = 'Call to Action Blocks';

    /**
     * @var string
     */
    private static string $table_name = 'ElementCallToAction';

    /**
     * @var array|string[]
     */
    private static array $has_one = [
        'CtaLink' => Link::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'CtaLink',
    ];

    /**
     * Duplicate the linked Link record alongside the element, otherwise the duplicate copies
     * the CtaLinkID foreign key and both elements edit (and $owns) the same Link row.
     *
     * No matching cascade_deletes is declared: duplicates created before this fix may already
     * share Link rows, so deleting the Link of one of them would break the other.
     *
     * @var array
     */
    private static array $cascade_duplicates = [
        'CtaLink',
    ];

    /**
     * Whether the duplicated CtaLink record still has to be re-pointed at this record.
     *
     * @var bool
     */
    private bool $linkOwnerNeedsRepointing = false;

    /**
     * cascade_duplicates copies the Link row verbatim, and linkfield stores its owner in the
     * Link itself (OwnerID / OwnerClass / OwnerRelation). The copy therefore still claims the
     * original element as its owner, which makes Link::Owner() return null for it (the reciprocal
     * has_one no longer matches), so linkfield's permission checks silently fall back to
     * canCreate() and "used on" reports miss the duplicated block.
     *
     * The copy can only be re-pointed once this record has an ID, and duplicate(false) - the path
     * DNADesign\Elemental\Models\ElementalArea uses when a page is duplicated - does not write it
     * here, so the work is also retried from onAfterWrite().
     *
     * @param DataObject $original
     * @param bool $doWrite
     * @param array|null $relations
     */
    protected function onAfterDuplicate(DataObject $original, $doWrite, $relations): void
    {
        $this->linkOwnerNeedsRepointing = (int)$this->CtaLinkID > 0;
        $this->repointDuplicatedLinkOwner();
    }

    /**
     * Called by DataObject::write()
     */
    protected function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->linkOwnerNeedsRepointing) {
            $this->repointDuplicatedLinkOwner();
        }
    }

    /**
     * Point the Link this element owns back at this element.
     *
     * Only ever runs on a record that was just created by duplicate(), so a Link row that a
     * pre-existing duplicate still shares with its original is left alone - rewriting it on every
     * save would just hand ownership of the shared row back and forth.
     */
    private function repointDuplicatedLinkOwner(): void
    {
        if (!$this->linkOwnerNeedsRepointing) {
            return;
        }

        // An unwritten duplicate has no ID to hand to the Link yet - leave the flag set and retry
        // from the write that eventually persists it.
        if (!$this->isInDB() || (int)$this->CtaLinkID === 0) {
            return;
        }

        $this->linkOwnerNeedsRepointing = false;
        $link = $this->CtaLink();

        if (!$link->exists() || (int)$link->OwnerID === (int)$this->ID) {
            return;
        }

        $link->OwnerID = $this->ID;
        $link->OwnerClass = $this->ClassName;
        $link->OwnerRelation = 'CtaLink';
        $link->write();
    }

    /**
     * @return FieldList
     */
    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->replaceField(
                'CtaLinkID',
                $link = LinkField::create('CtaLink')
                    ->setTitle('CTA Link')
            );
        });

        return parent::getCMSFields();
        ;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return DBField::create_field('HTMLText', $this->HTML)->Summary(20);
    }

    /**
     * Override getType() because ElementContent hardcodes 'Content'.
     * Use singular_name() to allow extensibility while overriding parent behavior.
     *
     * @return string
     */
    public function getType(): string
    {
        return _t(__CLASS__ . '.BlockType', $this->singular_name());
    }
}
