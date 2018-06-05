<?php

namespace Niden\Transformers;

use League\Fractal\TransformerAbstract;
use Niden\Models\Users;

/**
 * Class UserTransformer
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * @param Users $user
     *
     * @return array
     * @throws \Niden\Exception\ModelException
     */
    public function transform(Users $user)
    {
        return [
            'id'            => $user->get('usr_id'),
            'status'        => $user->get('usr_status_flag'),
            'username'      => $user->get('usr_username'),
            'domainName'    => $user->get('usr_domain_name'),
            'tokenPassword' => $user->get('usr_token_password'),
            'tokenId'       => $user->get('usr_token_id'),
        ];
    }
}
