<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Companies;
use Phalcon\Http\Response;
use Gewaer\Exception\UnprocessableEntityHttpException;
use Baka\Http\QueryParser;

/**
 * Class CompaniesController
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 * @property Request $request
 */
class CompaniesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['name', 'profile_image', 'website', 'users_id'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['name', 'profile_image', 'website'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Companies();
        $this->model->users_id = $this->userData->getId();

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
     * @return Response
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
            throw new UnprocessableEntityHttpException('Record not found');
        }
    }

    /**
     * Add a new item
     *
     * @method POST
     * @url /v1/company
     *
     * @return Response
     */
    public function create() : Response
    {
        $request = $this->request->getPost();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }

        //transaction
        $this->db->begin();

        //alwasy overwrite userid
        $request['users_id'] = $this->userData->getId();

        //try to save all the fields we allow
        if ($this->model->save($request, $this->createFields)) {
            
            $this->db->commit();
            return $this->response($this->model->toArray());
        } else {
            $this->db->rollback();
            throw new UnprocessableEntityHttpException((string) $this->model->getMessages()[0]);
        }
    }

    /**
     * Update a User Info
     *
     * @method PUT
     * @url /v1/company/{id}
     *
     * @return Response
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
                throw new UnprocessableEntityHttpException((string) current($company->getMessages()));
            }
        } else {
            throw new UnprocessableEntityHttpException('Record not found');
        }
    }
}
