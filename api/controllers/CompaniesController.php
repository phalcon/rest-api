<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Companies;
use Phalcon\Http\Response;
use Exception;

/**
 * Base controller
 *
 */
class CompaniesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['name', 'profile_image', 'website', 'users_id', 'created_at', 'updated_at'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['name', 'profile_image', 'website', 'users_id', 'created_at', 'updated_at'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Companies();

        $this->additionalSearchFields = [
            ['users_id', ':', $this->userData->getId()],
        ];
    }

    /**
     * Get Uer
     *
     * @param mixed $id
     *
     * @method GET
     * @url /v1/company/{id}
     *
     * @return Phalcon\Http\Response
     */
    public function getById($id) : Response
    {
        //find the info
        $company = $this->model->findFirst([
            'id = ?0 AND is_deleted = 0 and users_id = ?1',
            'bind' => [$id, $this->userData->getId()],
        ]);

        //get relationship
        if ($this->request->hasQuery('relationships')) {
            $relationships = $this->request->getQuery('relationships', 'string');

            $company = QueryParser::parseRelationShips($relationships, $company);
        }

        if ($company) {
            return $this->response($company);
        } else {
            throw new Exception('Record not found');
        }
    }

    /**
     * Update a User Info
     *
     * @method PUT
     * @url /v1/company/{id}
     *
     * @return Phalcon\Http\Response
     */
    public function edit($id) : Response
    {
        $company = $this->model->findFirst([
            'id = ?0 AND is_deleted = 0 and users_id = ?1',
            'bind' => [$id, $this->userData->getId()],
        ]);

        if ($company) {
            $request = $this->request->getPut();

            if (empty($request)) {
                $request = $this->request->getJsonRawBody(true);
            }

            //update
            if ($company->update($request, $this->updateFields)) {
                return $this->response($company);
            } else {
                //didnt work
                throw new Exception(current($company->getMessages()));
            }
        } else {
            throw new Exception('Record not found');
        }
    }
}