<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Api\Controllers\Companies;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Api\Validation\CompaniesValidator;
use Phalcon\Filter\Filter;
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
     * @return void
     * @throws ModelException
     */
    public function callAction(): void
    {
        $validator = new CompaniesValidator();
        $messages  = $validator->validate($this->request->getPost());

        /**
         * validate() answers false when a beforeValidation handler cancels the
         * run, which is not the same as "nothing was wrong" - so it must not
         * fall through to the insert. CompaniesValidator installs no such
         * handler today, but the signature allows it and the old
         * `count($messages)` would have been fatal on false rather than caught.
         */
        if (false === $messages) {
            $this
                ->response
                ->setPayloadError('The company could not be validated')
            ;

            return;
        }

        if (0 !== $messages->count()) {
            $this
                ->response
                ->setPayloadErrors($messages)
            ;

            return;
        }

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

        if (false === $result) {
            /**
             * Errors happened store them
             */
            $this
                ->response
                ->setPayloadErrors($company->getMessages())
            ;

            return;
        }

        $data = $this->format(
            'item',
            $company,
            BaseTransformer::class,
            'companies'
        );

        $this
            ->response
            ->setHeader('Location', appUrl(Relationships::COMPANIES, $company->get('id')))
            ->setJsonContent($data)
            ->setStatusCode($this->response::CREATED)
        ;
    }
}
