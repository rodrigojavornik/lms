<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Certificate;

class PublicCertificateController extends Controller
{
    public function verify($code)
    {
        $certificate = Certificate::where('verification_code', $code)
            ->with(['user', 'course'])
            ->first();

        return view('certificate-verify', compact('certificate', 'code'));
    }
}
