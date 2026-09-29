<?php

namespace Modules\ExtraUser\Repositories;

use App\Traits\ImageStore;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Modules\Contact\Entities\ContactModel;

class ExtraUserRepository implements ExtraUserRepositoriesInterface
{
    use ImageStore;

    public function agency()
    {
        return ContactModel::agency()->latest()->get();
    }


    public function strategicPartner()
    {
        return ContactModel::strategicPartner()->latest()->get();
    }

    public function benches()
    {
        return ContactModel::benches()->latest()->get();
    }

    public function kiosks()
    {
        return ContactModel::kiosks()->latest()->get();
    }

    public function create(array $data)
    {
        $contact = new ContactModel();

        if (isset($data['file'])) {
            $data = Arr::add($data, 'avatar', $this->saveAvatar($data['file']));
        }
        $contact->fill($data)->save();
        return $contact;
    }

    public function find($id)
    {
        return ContactModel::findOrFail($id);
    }

    public function update($id, array $data)
    {

        $contact = $this->find($id);

        if (isset($data['file'])) {
            if (File::exists($contact->avatar)) {
                File::delete($contact->avatar);
            }
            $data = Arr::add($data, 'avatar', $this->saveAvatar($data['file']));
        }
        $contact->update($data);

        return $contact;
    }
}
