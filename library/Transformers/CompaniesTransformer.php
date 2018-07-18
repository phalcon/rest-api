<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\TransformerAbstract;
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
            'id'      => $company->get('com_id'),
            'name'    => $company->get('com_name'),
            'address' => $company->get('com_address'),
            'city'    => $company->get('com_city'),
            'phone'   => $company->get('com_telephone'),
        ];
    }
}
