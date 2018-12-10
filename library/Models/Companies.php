<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Gewaer\Exception\ModelException;

class Companies extends \Baka\Auth\Models\Companies
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $profile_image;

    /**
     *
     * @var string
     */
    public $website;

    /**
     *
     * @var integer
     */
    public $users_id;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var integer
     */
    public $is_deleted;

    /**
     * Provide the app plan id
     *
     * @var integer
     */
    public $appPlanId = null;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('companies');

        $this->belongsTo(
            'users_id',
            'Gewaer\Models\Users',
            'id',
            ['alias' => 'user']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\CompanyBranches',
            'company_id',
            ['alias' => 'branches']
        );
    }

    /**
     * Model validation
     *
     * @return void
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'name',
            new PresenceOf([
                'model' => $this,
                'required' => true,
            ])
        );

        return $this->validate($validator);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'companies';
    }

    /**
     * After creating the company
     *
     * @return void
     */
    public function afterCreate()
    {
        parent::afterCreate();

        /**
         * @var CompanyBranches
         */
        $branch = new CompanyBranches();
        $branch->company_id = $this->getId();
        $branch->users_id = $this->user->getId();
        $branch->name = 'Default';
        $branch->description = '';
        if (!$branch->save()) {
            throw new ModelException((string) current($branch->getMessages()));
        }

        //assign default branch to the user
        if (empty($this->user->default_company_branch)) {
            $this->user->default_company_branch = $branch->getId();
        }

        //we need to assign this company to a plan
        if (empty($this->appPlanId)) {
            $plan = AppsPlans::getDefaultPlan();
        }

        //look for the default plan for this app
        $companyApps = new UserCompanyApps();
        $companyApps->company_id = $this->getId();
        $companyApps->apps_id = $this->di->getApp()->getId();
        $companyApps->stripe_id = $plan->stripe_id;
        $companyApps->subscriptions_id = 0;

        if (!$companyApps->save()) {
            throw new ModelException((string) current($companyApps->getMessages()));
        }
    }
}
