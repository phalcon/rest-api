<?php

declare(strict_types=1);

namespace Niden\Transformers;

use Niden\Constants\Resources;

/**
 * Class IndividualTypesTransformer
 */
class IndividualTypesTransformer extends TypesTransformer
{
    protected $prefix = 'idt';
    protected $type   = Resources::INDIVIDUAL_TYPES;
    protected $url    = 'individualtypes';
}
