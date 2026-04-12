@extends('layouts.app')
@section('title', 'Jiwer Rawda Banners')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <a href="{{ route('availability_banners.create') }}" class="btn btn-primary">Create banner</a>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <section>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Availability banners</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body table-responsive">
                                @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Hotel</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Preview</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($banners as $banner)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $banner->hotel_name }}</td>
                                            <td>{{ $banner->from_date->format('Y-m-d') }}</td>
                                            <td>{{ $banner->to_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if ($banner->generated_banner_path)
                                                <img src="{{ route('availability_banners.preview', $banner) }}" alt="banner" style="height: 140px;">
                                                @else
                                                Not generated
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-success" href="{{ route('availability_banners.download', $banner) }}">Download PNG</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No banners yet.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
