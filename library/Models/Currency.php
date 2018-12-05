<?php
declare(strict_types=1);

namespace Gewaer\Models;

class Currency extends AbstractModel
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
    public $country;

    /**
     *
     * @var string
     */
    public $currency;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var string
     */
    public $symbel;

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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('currency');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'currency';
    }
}
