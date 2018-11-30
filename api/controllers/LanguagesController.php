<?php
declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Languages;

/**
 * Base controller
 *
 */
class LanguagesController extends BaseController
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
        $this->model = new Languages();
        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
        ];
    }
}
