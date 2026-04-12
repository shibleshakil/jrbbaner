@extends('layouts.app')
@section('title', 'Create Jiwer Rawda Banner')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <a href="{{ route('availability_banners.index') }}" class="btn btn-primary">Back to list</a>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">New Availability banner</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('availability_banners.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="from_date">From date<span class="text-danger">*</span></label>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date') }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="to_date">To date<span class="text-danger">*</span></label>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date') }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="hotel_name">Hotel name</label>
                                    <input type="text" name="hotel_name" id="hotel_name" class="form-control" value="{{ old('hotel_name', 'Jiwar Rawda For Hotel') }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Image 1<small>(240X160)</small></label>
                                    <input type="file" name="image_1" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Image 2<small>(240X160)</small></label>
                                    <input type="file" name="image_2" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Image 3<small>(240X160)</small></label>
                                    <input type="file" name="image_3" class="form-control" accept="image/*">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Save &amp; generate PNG</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
