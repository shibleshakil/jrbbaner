@extends('layouts.app')
@section('title', 'Promotion Banner')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <a href="{{ route('promotions.create') }}" class="btn btn-primary">Create New Banner</a>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <section id="promotion-form">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Generated Banners</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Hotel</th>
                                                <th>Download</th>
                                                <th>Total Offer</th>
                                                <th>Banner</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($promotions as $promotion)
                                            <tr>
                                                <td>{{ ($promotions->currentPage() - 1) * $promotions->perPage() + $loop->iteration }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-success" href="{{ route('promotions.download', $promotion) }}">Download</a>
                                                </td>
                                                <td>{{ $promotion->hotel_name }}</td>
                                                <td class="p-0">
                                                    <table class="" style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>From</th>
                                                                <th>To</th>
                                                                <th>Double</th>
                                                                <th>Triple</th>
                                                                <th>Quad</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($promotion->offerDetails as $offerDetail)
                                                            <tr>
                                                                <td>{{ $offerDetail->from_date }}</td>
                                                                <td>{{ $offerDetail->to_date }}</td>
                                                                <td>{{ $offerDetail->double_rate }}</td>
                                                                <td>{{ $offerDetail->triple_rate }}</td>
                                                                <td>{{ $offerDetail->quad_rate }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td>
                                                    @if ($promotion->generated_banner_path)
                                                    <img src="{{ route('promotions.preview', $promotion) }}" alt="banner" style="height: 140px;">
                                                    @else
                                                    Not generated
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No banners created yet.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mt-1 float-right">
                                    <div class="col-md-12 text-right">
                                        <!-- bootstrap pagination -->
                                        {{ $promotions->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('script')
@endsection
