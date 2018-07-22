<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\TransformerAbstract;
use Niden\Constants\Resources;
use function Niden\Core\envValue;
use Niden\Models\Companies;

/**
 * Class CompaniesTransformer
 */
class CompaniesTransformer extends TransformerAbstract
{
    /**
     * @param Companies $company
     *
     * @return array
     * @throws \Niden\Exception\ModelException
     */
    public function transform(Companies $company)
    {
        return [
            'id'         => $company->get('com_id'),
            'type'       => Resources::COMPANIES,
            'attributes' => [
                'name'    => $company->get('com_name'),
                'address' => $company->get('com_address'),
                'city'    => $company->get('com_city'),
                'phone'   => $company->get('com_telephone'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/companies/%s',
                    envValue('APP_URL', 'localhost'),
                    $company->get('com_id')
                ),
            ]
        ];
    }
}
