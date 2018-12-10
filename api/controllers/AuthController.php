<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Users;
use Baka\Auth\Models\Users as BakaUsers;
use Gewaer\Models\UsersInvite;
use Gewaer\Models\UserLinkedSources;
use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Exception\UnprocessableEntityHttpException;
use Phalcon\Http\Response;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation;

/**
 * Class AuthController
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 * @property \Baka\Mail\Message $mail
 */
class AuthController extends \Baka\Auth\AuthController
{
    /**
     * Hash for invite record
     */
    protected $invite_hash = ' ';

    /**
     * Setup for this controller
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->userLinkedSourcesModel = new UserLinkedSources();
        $this->userModel = new Users();

        if (!isset($this->config->jwt)) {
            throw new ServerErrorHttpException('You need to configure your app JWT');
        }
    }

    /**
    * Set the email config array we are going to be sending
    *
    * @param String $emailAction
    * @param Users  $user
    */
    protected function sendEmail(BakaUsers $user, string $type): void
    {
        $send = true;
        $subject = null;
        $body = null;

        switch ($type) {
            case 'recover':
                $recoveryLink = $this->config->app->frontEndUrl . '/user/reset/' . $user->user_activation_forgot;

                $subject = _('Password Recovery');
                $body = sprintf(_('Click %shere%s to set a new password for your account.'), '<a href="' . $recoveryLink . '" target="_blank">', '</a>');

                // send email to recover password
                break;
            case 'reset':
                $activationUrl = $this->config->app->frontEndUrl . '/user/activate/' . $user->user_activation_key;

                $subject = _('Password Updated!');
                $body = sprintf(_('Your password was update please, use this link to activate your account: %sActivate account%s'), '<a href="' . $activationUrl . '">', '</a>');
                // send email that password was update
                break;
            case 'invite':
                $activationUrl = $this->config->app->frontEndUrl . '/user/invite/' . $this->invite_hash;
                //Send invitation link to person
                $subject = _('You have been invited!');
                $body = sprintf(_('Your have been invite to join our system, use this link to succesfully create your account: %Create account%s'), '<a href="' . $activationUrl . '">', '</a>');

                break;
            default:
                $send = false;
                break;
        }

        if ($send) {
            $this->mail
                ->to($user->email)
                ->subject($subject)
                ->content($body)
                ->sendNow();
        }
    }

    /**
     * Sets up invitation information for a would be user
     * @return Response
     */
    public function insertInvite(): Response
    {
        $request = $this->request->getPost();

        $validation = new Validation();
        $validation->add('email', new PresenceOf(['message' => _('The email is required.')]));
        $validation->add('role', new PresenceOf(['message' => _('The role is required.')]));

        //validate this form for password
        $messages = $validation->validate($this->request->getPost());
        if (count($messages)) {
            foreach ($messages as $message) {
                throw new ServerErrorHttpException((string)$message);
            }
        }

        //Save data to users_invite table and generate a hash for the invite
        $userInvite = new UsersInvite();
        $userInvite->company_id = $this->userData->default_company;
        $userInvite->app_id = 1;
        $userInvite->role_id = $request['role'] == 'Admins' ? 1 : 2;
        $userInvite->email = $request['email'];
        $userInvite->invite_hash = hash('md5', $request['email']);
        $userInvite->created_at = date('Y-m-d H:m:s');

        if (!$userInvite->save()) {
            throw new UnprocessableEntityHttpException((string) current($userInvite->getMessages()));
        }

        $userInviteArray = $userInvite->toArray();

        $this->setInviteHash($userInviteArray['invite_hash']);

        return $this->response($userInviteArray['invite_hash']);
    }

    /**
     * Set Invite Hash
     */
    protected function setInviteHash(string $hash)
    {
        $this->invite_hash = $hash;
    }
}
