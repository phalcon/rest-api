<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Users;
use Gewaer\Models\UserLinkedSources;
use Baka\Auth\Models\Sources;
use Phalcon\Http\Response;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Gewaer\Exception\BadRequestHttpException;
use Gewaer\Exception\UnprocessableEntityHttpException;
use Baka\Http\QueryParser;
use Gewaer\Exception\ModelException;
use Gewaer\Exception\NotFoundHttpException;
use Gewaer\Models\AccessList;

/**
 * Class UsersController
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 */
class UsersController extends \Baka\Auth\UsersController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['name', 'firstname', 'lastname', 'displayname', 'email', 'password', 'created_at', 'updated_at', 'default_company', 'family'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['name', 'firstname', 'lastname', 'displayname', 'email', 'password', 'created_at', 'updated_at', 'default_company'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Users();

        //if you are not a admin you cant see all the users
        if (!$this->userData->hasRole('Default.Admins')) {
            $this->additionalSearchFields = [
                ['id', ':', $this->userData->getId()],
            ];
        } else {
            //admin get all the users for this company
            $this->additionalSearchFields = [
                ['default_company', ':', $this->userData->default_company],
            ];
        }
    }

    /**
     * Get Uer
     *
     * @param mixed $id
     *
     * @method GET
     * @url /v1/users/{id}
     *
     * @return Response
     */
    public function getById($id) : Response
    {
        //find the info
        $user = $this->model->findFirst([
            'id = ?0 AND is_deleted = 0',
            'bind' => [$this->userData->getId()],
        ]);

        $user->password = null;

        //get relationship
        if ($this->request->hasQuery('relationships')) {
            $relationships = $this->request->getQuery('relationships', 'string');

            $user = QueryParser::parseRelationShips($relationships, $user);
        }

        //if you search for roles we give you the access for this app
        if (array_key_exists('roles', $user)) {
            $accesList = AccessList::find([
                'conditions' => 'roles_name = ?0 and apps_id = ?1 and allowed = 0',
                'bind' => [$user['roles'][0]->name, $this->config->app->id]
            ]);

            if (count($accesList) > 0) {
                foreach ($accesList as $access) {
                    $user['access_list'][strtolower($access->resources_name)][$access->access_name] = 0;
                }
            }
        }

        if ($user) {
            return $this->response($user);
        } else {
            throw new ModelException('Record not found');
        }
    }

    /**
     * Update a User Info
     *
     * @method PUT
     * @url /v1/users/{id}
     *
     * @return Response
     */
    public function edit($id) : Response
    {
        //none admin users can only edit themselves 
        if (!$this->userData->hasRole('Default.Admins')) {
            $id = $this->userData->getId();
        }

        if ($user = $this->model->findFirst($id)) {
            $request = $this->request->getPut();

            if (empty($request)) {
                $request = $this->request->getJsonRawBody(true);
            }

            //clean pass
            if (array_key_exists('password', $request) && !empty($request['password'])) {
                $user->password = Users::passwordHash($request['password']);
                unset($request['password']);
            }

            //clean default company
            if (array_key_exists('default_company', $request)) {
                //@todo check if I belong to this company
                if ($company = Companies::findFirst($request['default_company'])) {
                    $user->default_company = $company->getId();
                    unset($request['default_company']);
                }
            }

            //update
            if ($user->update($request, $this->updateFields)) {
                $user->password = null;
                return $this->response($user);
            } else {
                //didnt work
                throw new ModelException((string) current($user->getMessages()));
            }
        } else {
            throw new NotFoundHttpException('Record not found');
        }
    }

    /**
     * Add users notifications
     *
     * @param int $id
     * @method PUT
     * @return Response
     */
    public function updateNotifications($id): Response
    {
        //get the notification array
        //delete the current ones
        //iterate and save into users

        return $this->response(['OK']);
    }

    /**
     * Associate a Device with the corrent loggedin user
     *
     * @url /users/{id}/device
     * @method POST
     * @return Response
     */
    public function devices(): Response
    {
        //Ok let validate user password
        $validation = new Validation();
        $validation->add('app', new PresenceOf(['message' => _('App name is required.')]));
        $validation->add('deviceId', new PresenceOf(['message' => _('device ID is required.')]));

        //validate this form for password
        $messages = $validation->validate($this->request->getPost());
        if (count($messages)) {
            foreach ($messages as $message) {
                throw new BadRequestHttpException((string) $message);
            }
        }

        $app = $this->request->getPost('app', 'string');
        $deviceId = $this->request->getPost('deviceId', 'string');

        //get the app source
        if ($source = Sources::getByTitle($app)) {
            if (!$userSource = UserLinkedSources::findFirst(['conditions' => 'users_id = ?0 and source_users_id_text =?1', 'bind' => [$this->userData->getId(), $deviceId]])) {
                $userSource = new UserLinkedSources();
                $userSource->users_id = $this->userData->getId();
                $userSource->source_id = $source->getId();
                $userSource->source_users_id = $this->userData->getId();
                $userSource->source_users_id_text = $deviceId;
                $userSource->source_username = $this->userData->displayname . ' ' . $app;

                if (!$userSource->save()) {
                    throw new UnprocessableEntityHttpException((string) current($userSource->getMessages()));
                }

                $msg = 'User Device Associated';
            } else {
                $msg = 'User Device Already Associated';
            }
        }

        //clean password @todo move this to a better place
        $this->userData->password = null;

        return $this->response([
            'msg' => $msg,
            'user' => $this->userData
        ]);
    }
}
