<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if ($row[0] == 'email') {
            return null;
        }

        Log::info('Importing row:', $row);

        return new User([
            'email' => $row[0],
            'username' => $row[1],
            'npm' => $row[2],
            'name' => $row[3],
            'role' => 'USER',
            'password' => Hash::make('password123'),
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
