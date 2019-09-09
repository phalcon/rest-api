<?php

declare(strict_types=1);

namespace Phalcon\Api\Api\Controllers\Companies;

use Niden\Constants\Relationships;
use function Niden\Core\appUrl;
use Niden\Exception\ModelException;
use Niden\Http\Response;
use Niden\Models\Companies;
use Niden\Traits\FractalTrait;
use Niden\Transformers\BaseTransformer;
use Niden\Validation\CompaniesValidator;
use Phalcon\Filter\Filter;
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
                ->save();

            if (false !== $result) {
                $data = $this->format('item', $company, BaseTransformer::class, 'companies');
                $this
                    ->response
                    ->setHeader('Location', appUrl(Relationships::COMPANIES, $company->get('id')))
                    ->setJsonContent($data)
                    ->setStatusCode($this->response::CREATED)
                ;
            } else {
                /**
                 * Errors happened store them
                 */
                $this
                    ->response
                    ->setPayloadErrors($company->getMessages());
            }
        } else {
            /**
             * Set the errors in the payload
             */
            $this
                ->response
                ->setPayloadErrors($messages);
        }
    }
}
