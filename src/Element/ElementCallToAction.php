<?php

namespace Dynamic\Elements\CTA\Elements;

use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\LinkField\JsonData;
use SilverStripe\LinkField\ORM\DBLink;
use SilverStripe\LinkField\Type\Registry;
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
    private static array $db = [
        'CtaLink' => DBLink::class,
    ];

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

    /**
     * @return JsonData|null
     */
    public function getLinkObject(): ?JsonData
    {
        $link = $this->dbObject('CtaLink');

        $value = $link->getValue();

        if ($value) {
            $type = Registry::singleton()->byKey($value['typeKey']);

            if ($type) {
                return $type->loadLinkData($value);
            }
        }

        return null;
    }
}
