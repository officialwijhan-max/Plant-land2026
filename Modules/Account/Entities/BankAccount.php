<?php



namespace Modules\Account\Entities;



use Illuminate\Database\Eloquent\Model;

use Modules\Account\Entities\OpeningBalanceHistory;

use Modules\Inventory\Entities\ShowRoom;



class BankAccount extends Model

{

    protected $fillable = ['bank_name','branch_name','account_name','account_no','description'];

    // Included so the edit modal's JS (bank_accounts.blade.php) can
    // prefill the Opening Balance field from the model JSON already
    // serialized into the "Edit" button's data-value attribute.
    protected $appends = ['opening_balance'];

    public function getOpeningBalanceAttribute()
    {
        return OpeningBalanceHistory::where('account_id', $this->chart_account_id)
            ->where('acc_type', 'asset')
            ->value('amount') ?? 0;
    }



    public function transactions()

    {

        return $this->hasMany(Transaction::class, "account_id",'chart_account_id');

    }



    // Branches this account is scoped to. No rows here means "shared
    // with every branch" - see the bank_account_showroom migration.
    public function showRooms()

    {

        return $this->belongsToMany(ShowRoom::class, 'bank_account_showroom', 'bank_account_id', 'showroom_id');

    }



    public function getBalanceAmountAttribute()
    {
        if ($this->chartAccount && ($this->chartAccount->type == 1 || $this->chartAccount->type == 3)) {
            return OpeningBalanceHistory::where('account_id', $this->chart_account_id)
                ->where('is_default', 0)
                ->sum('amount') 
                + $this->transactions->where('type', 'Dr')->sum('amount') 
                - $this->transactions->where('type', 'Cr')->sum('amount');
        } else {
            return $this->transactions->where('type', 'Cr')->sum('amount') 
                - $this->transactions->where('type', 'Dr')->sum('amount');
        }
    }




    public function chartAccount()

    {

        return $this->belongsTo(ChartAccount::class);

    }

}

