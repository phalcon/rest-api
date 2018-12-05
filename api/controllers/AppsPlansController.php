<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\AppsPlans;
use Gewaer\Exception\UnauthorizedHttpException;
use Gewaer\Exception\NotFoundHttpException;
use Stripe\Token as StripeToken;
use Phalcon\Http\Response;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Gewaer\Exception\UnprocessableEntityHttpException;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class AppsPlansController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = [];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = [];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new AppsPlans();
        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['apps_id', ':', $this->app->getId()],
        ];
    }

    /**
     * Given the app plan stripe id , subscribe the current company to this aps
     *
     * @param string $stripeId
     * @return void
     */
    public function create(): Response
    {
        if (!$this->userData->hasRole('Default.Admins')) {
            throw new UnauthorizedHttpException(_('You dont have permission to subscribe this apps'));
        }

        $appPlan = $this->model->findFirstByStripeId($stripeId);

        if (!$appPlan) {
            throw new NotFoundHttpException(_('This plan doesnt exist'));
        }

        //Ok let validate user password
        $validation = new Validation();
        $validation->add('stripe_id', new PresenceOf(['message' => _('The plan is required.')]));
        $validation->add('number', new PresenceOf(['message' => _('Credit Card Number is required.')]));
        $validation->add('exp_month', new PresenceOf(['message' => _('Credit Card Number is required.')]));
        $validation->add('exp_year', new PresenceOf(['message' => _('Credit Card Number is required.')]));
        $validation->add('cvc', new PresenceOf(['message' => _('CVC is required.')]));

        //validate this form for password
        $messages = $validation->validate($this->request->getPost());
        if (count($messages)) {
            foreach ($messages as $message) {
                throw new UnprocessableEntityHttpException($message);
            }
        }

        $stripeId = $this->request->getPost('stripe_id');
        $company = $this->userData->defaultCompany;
        $cardNumber = $this->request->getPost('number');
        $expMonth = $this->request->getPost('exp_month');
        $expYear = $this->request->getPost('exp_year');
        $cvc = $this->request->getPost('cvc');

        $card = StripeToken::create([
            'card' => [
                'number' => $cardNumber,
                'exp_month' => $expMonth,
                'exp_year' => $expYear,
                'cvc' => $cvc,
            ],
        ], [
            'api_key' => $this->config->stripe->secret
        ])->id;

        //if fails it will throw exception
        $this->userData->newSubscription($appPlan->name, $appPlan->stripe_id, $company, $this->app)->create($card);

        //sucess
        return $this->response($appPlan);
    }

    /**
     * Cancel a given subscription
     *
     * @param string $stripeId
     * @return boolean
     */
    public function update($stripeId) : Response
    {
    }

    /**
     * Cancel a given subscription
     *
     * @param string $stripeId
     * @return boolean
     */
    public function delete($stripeId): Response
    {
    }
}
