<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Users;
use Baka\Auth\Models\Users as BakaUsers;
use Gewaer\Models\UserLinkedSources;
use Gewaer\Exception\ServerErrorHttpException;

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
}
