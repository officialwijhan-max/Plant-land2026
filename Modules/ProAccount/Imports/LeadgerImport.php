<?php

namespace Modules\ProAccount\Imports;
use Modules\ProAccount\Entities\Leadger;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadgerImport implements ToCollection, WithHeadingRow, WithValidation
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
            if (substr($row['code'],0,1) == "3") {
                $type = 2;
            }elseif (substr($row['code'],0,1) == "5") {
                $type = 3;
            }else {
                $type = substr($row['code'],0,1);
            }

            if ($row['parent_code'] == 0) {
                $count = 0;
                $parent_id = 0;
            }else {
                $count = 0;
                $parent = Leadger::with('parent')->where('code', $row['parent_code'])->first();
                $parent_id = $parent->id;
                $count += 1;
                while ($parent->parent_id != 0)
                {
                    $parent = $parent->parent;
                    $count += 1;
                    continue;
                }
            }
            $item = Leadger::create([
                'code'    => $row['code'],
                'type'    => $type,
                'name'    => $row['name'],
                'acc_type'    => $row['acc_type'],
                'parent_id'    => $parent_id,
                'level'    => $count,
                'is_cost_center'    => ($row['cost_center'] == "yes") ? 1 : 0,
            ]);

            if (substr($row['code'],0,1) == "3") {
                $jsonString = file_get_contents(base_path('Modules/ProAccount/Resources/assets/config_files/equity.json'));
                $data = json_decode($jsonString, true);
                // Update Keydd
                array_push($data['equity_account'],$item->id);
                // Write File
                $newJsonString = json_encode($data, JSON_PRETTY_PRINT);
                file_put_contents(base_path('Modules/ProAccount/Resources/assets/config_files/equity.json'), stripslashes($newJsonString));
            }
        }
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function rules(): array
   {
       return [
           'code' => 'required|unique:leadgers,code',
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
