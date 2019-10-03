<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Api\Controllers\Companies;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Api\Validation\CompaniesValidator;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;
use function Phalcon\Api\Core\appUrl;

/**
 * Class AddController
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
