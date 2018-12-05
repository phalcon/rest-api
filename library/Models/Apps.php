<?php
declare(strict_types=1);

namespace Gewaer\Models;

class Apps extends \Baka\Auth\Models\Apps
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
    public $description;

    /**
     *
     * @var string
     */
    public $url;

    /**
     *
     * @var integer
     */
    public $is_actived;

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
     * Ecosystem default app
     * @var string
     */
    const GEWAER_DEFAULT_APP_NAME = 'Default';

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('apps');
    }

    /**
     * You can only get 2 variations or default in DB or the api app
     *
     * @param string $name
     * @return Apps
     */
    public static function getACLApp(string $name): Apps
    {
        if (trim($name) == self::GEWAER_DEFAULT_APP_NAME) {
            $app = self::findFirst(0);
        } else {
            $app = self::findFirst(\Phalcon\DI::getDefault()->getConfig()->app->id);
        }

        return $app;
    }

    /**
     * Is active?
     *
     * @return boolean
     */
    public function isActive(): bool
    {
        return (bool) $this->is_actived;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'apps';
    }
}
