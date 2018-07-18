<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Users;

use Niden\Api\Validation\CompaniesValidator;
use Niden\Exception\ModelException;
use Niden\Http\Response;
use Niden\Models\Companies;
use Niden\Traits\FractalTrait;
use Niden\Transformers\CompaniesTransformer;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro;

/**
 * Class AddController
 *
 * @package Niden\Api\Controllers\Companies
 *
 * @property Micro    $application
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
            $address = $this->request->getPost('address', Filter::FILTER_STRING);
            $city    = $this->request->getPost('city', Filter::FILTER_STRING);
            $phone   = $this->request->getPost('phone', Filter::FILTER_STRING);

            $company = new Companies();
            $result  = $company
                ->set('com_name', $name)
                ->set('com_address', $address)
                ->set('com_city', $city)
                ->set('com_telephone', $phone)
                ->save()
            ;

            if (false !== $result) {
                /**
                 * Everything is fine, return the record back
                 */
                return $this->format([$result], CompaniesTransformer::class);
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
