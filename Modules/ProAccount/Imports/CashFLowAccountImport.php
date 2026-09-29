<?php

namespace Modules\ProAccount\Imports;
use Modules\ProAccount\Entities\CashFLowAccount;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CashFLowAccountImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $item = CashFLowAccount::create([
                'code'    => $row['code'],
                'type'    => (strtolower($row['type']) == "expense") ? 3 : 4,
                'name'    => $row['name'],
            ]);
        }
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function rules(): array
   {
       return [
           'code' => 'required|unique:cash_flow_accounts,code',
           'name' => 'required|unique:leadgers,name',
       ];
   }
   public function customValidationMessages()
   {
       return [
           'code.unique' => 'Code has already been taken.',
           'name.unique' => 'Account Name has already been taken',
       ];
   }
}
