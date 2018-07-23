<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Companies;

use Niden\Exception\ModelException;
use Niden\Http\Response;
use Niden\Models\Companies;
use Niden\Traits\FractalTrait;
use Niden\Transformers\BaseTransformer;
use Niden\Validation\CompaniesValidator;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class AddController
 *
 * @package Niden\Api\Controllers\Companies
 *
 * @property Response $response
 */
class AddController extends Controller
{
    use FractalTrait;

    /**
     * Adds a record in the database
     *
     * @return array
     * @throws ModelException
     */
    public function callAction()
    {
        $validator = new CompaniesValidator();
        $messages  = $validator->validate($this->request->getPost());

        /**
         * If no messages are returned, go ahead with the query
         */
        if (0 === count($messages)) {
            $name    = $this->request->getPost('name', Filter::FILTER_STRING);
            $address = $this->request->getPost('address', Filter::FILTER_STRING, '');
            $city    = $this->request->getPost('city', Filter::FILTER_STRING, '');
            $phone   = $this->request->getPost('phone', Filter::FILTER_STRING, '');

            $company = new Companies();
            $result  = $company
                ->set('name', $name)
                ->set('address', $address)
                ->set('city', $city)
                ->set('phone', $phone)
                ->save()
            ;

            if (false !== $result) {
                /**
                 * Everything is fine, return the record back
                 */
                return $this->format([$company], BaseTransformer::class, 'companies');
            }

            /**
             * Errors happened store them
             */
            $messages = $company->getMessages();
        }

        /**
         * Set the errors in the payload
         */
        $this->response->setPayloadErrors($messages);
    }
}
