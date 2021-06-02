<?php
/**
 * Project qpartners
 * Created by danila 01.06.20 @ 20:55
 */

namespace App\Http\Controllers\Partner;


use App\Exports\ReportExport;
use App\Helpers\ReportTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use ReportTrait;
}
