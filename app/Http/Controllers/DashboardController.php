<?php

namespace App\Http\Controllers;

use App\Models\JurnalKBM;
use App\Models\JurnalEkskul;
use App\Models\JurnalPiket;
use App\Models\JurnalKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];
        
        // Default values for all users
        $data['jurnalKBMCount'] = 0;
        $data['jurnalEkskulCount'] = 0;
        $data['jurnalPiketCount'] = 0;
        $data['jurnalKegiatanCount'] = 0;
        $data['recentActivities'] = collect();
        
        if ($user->isGuru() || $user->isAdmin()) {
            // Count jurnals for today - GLOBAL (from all teachers)
            $today = now()->format('Y-m-d');
            
            $data['jurnalKBMCount'] = JurnalKBM::whereDate('tanggal', $today)
                ->count();
                
            $data['jurnalEkskulCount'] = JurnalEkskul::whereDate('tanggal', $today)
                ->count();
                
            $data['jurnalPiketCount'] = JurnalPiket::whereDate('tanggal', $today)
                ->count();
                
            $data['jurnalKegiatanCount'] = JurnalKegiatan::whereDate('tanggal', $today)
                ->count();
            
            // Recent activities - GLOBAL (from all teachers), only within the last week
            $week = now()->subWeek();

            $kbmActivities = JurnalKBM::where(function($q) use ($week) {
                    $q->where('created_at', '>=', $week)->orWhere('updated_at', '>=', $week);
                })->orderBy('updated_at', 'desc')->get();

            $ekskulActivities = JurnalEkskul::where(function($q) use ($week) {
                    $q->where('created_at', '>=', $week)->orWhere('updated_at', '>=', $week);
                })->orderBy('updated_at', 'desc')->get();

            $piketActivities = JurnalPiket::where(function($q) use ($week) {
                    $q->where('created_at', '>=', $week)->orWhere('updated_at', '>=', $week);
                })->orderBy('updated_at', 'desc')->get();

            $kegiatanActivities = JurnalKegiatan::where(function($q) use ($week) {
                    $q->where('created_at', '>=', $week)->orWhere('updated_at', '>=', $week);
                })->orderBy('updated_at', 'desc')->get();

            // Merge all activities and sort by updated_at descending so recent edits bubble to the top, then take top 10
            $data['recentActivities'] = $kbmActivities
                ->merge($ekskulActivities)
                ->merge($piketActivities)
                ->merge($kegiatanActivities)
                ->sortByDesc(function($item) {
                    return $item->updated_at ?? $item->created_at;
                })
                ->take(10);
        }
        
        return view('dashboard', $data);
    }
}