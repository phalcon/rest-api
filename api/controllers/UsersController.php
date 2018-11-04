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

/**
 * Users controller
 *
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
            if (!$userSource = UserLinkedSources::findFirst(['conditions' => 'user_id = ?0 and source_user_id_text =?1', 'bind' => [$this->userData->getId(), $deviceId]])) {
                $userSource = new UserLinkedSources();
                $userSource->user_id = $this->userData->getId();
                $userSource->source_id = $source->source_id;
                $userSource->source_user_id = $this->userData->getId();
                $userSource->source_user_id_text = $deviceId;
                $userSource->source_username = $this->userData->displayname . ' ' . $app;

                if (!$userSource->save()) {
                    throw new UnprocessableEntityHttpException(current($userSource->getMessages()));
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
