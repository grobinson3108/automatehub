@extends('watchtrend.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <i class="fa fa-binoculars me-2 text-emerald-600"></i>
                    WatchTrend
                </h3>
            </div>
            <div class="block-content block-content-full text-center py-5">
                <div class="py-5">
                    <i class="fa fa-binoculars fa-4x text-muted mb-4"></i>
                    <h3 class="fw-semibold text-muted">WatchTrend - Dashboard</h3>
                    <p class="text-muted mb-0">
                        Veille intelligente multi-sources avec analyse IA.<br>
                        <span class="badge bg-warning text-dark mt-2">Coming Soon - Sprint 1</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
