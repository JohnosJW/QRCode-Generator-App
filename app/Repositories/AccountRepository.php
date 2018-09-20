<?php

namespace App\Repositories;

use App\Models\Account;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AccountRepository
 * @package App\Repositories
 * @version September 19, 2018, 8:41 am UTC
 *
 * @method Account findWithoutFail($id, $columns = ['*'])
 * @method Account find($id, $columns = ['*'])
 * @method Account first($columns = ['*'])
*/
class AccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'balance',
        'total_credit',
        'total_debit',
        'withdrawal_method',
        'payment_email',
        'bank_name',
        'bank_branch',
        'bank_account',
        'applied_for_payout',
        'paid',
        'last_date_applied',
        'last_date_paid',
        'country',
        'other_details',
        'payment_details'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Account::class;
    }
}
