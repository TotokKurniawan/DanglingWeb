<?php

namespace App\Services\Web;

use App\Models\Partner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PartnerService
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Partner::paginate($perPage);
    }

    public function create(array $data): Partner
    {
        return Partner::create($data);
    }

    public function update(Partner $partner, array $data): Partner
    {
        $partner->update($data);
        return $partner;
    }

    public function delete(Partner $partner): void
    {
        $partner->delete();
    }
}

