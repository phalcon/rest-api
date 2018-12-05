<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\AppsPlans;
use Gewaer\Exception\UnauthorizedHttpException;
use Gewaer\Exception\NotFoundHttpException;
use Stripe\Token as StripeToken;
use Phalcon\Http\Response;

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
    public function subscribe(string $stripeId): Response
    {
        if (!$this->userData->hasRole('Default.Admins')) {
            throw new UnauthorizedHttpException(_('You dont have permission to subscribe this apps'));
        }

        $appPlan = $this->model->findFirstByStripeId($stripeId);

        if (!$appPlan) {
            throw new NotFoundHttpException(_('This plan doesnt exist'));
        }

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
    public function cancelSubscription(string $stripeId): Response
    {
    }
}
