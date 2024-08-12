<?php

namespace Dynamic\Elements\CTA\Elements;

use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\Forms\FieldList;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;
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
    private static string $plural_name = 'Call to Actions';

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
     * @return string
     */
    public function getType(): string
    {
        return _t(__CLASS__ . '.BlockType', 'Call To Action');
    }
}
